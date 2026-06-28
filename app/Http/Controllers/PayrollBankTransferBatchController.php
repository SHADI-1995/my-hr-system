<?php


namespace App\Http\Controllers;

use App\Models\PayrollBankTransferBatch;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollBankTransferBatchController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.view'), 403);

        $batches = PayrollBankTransferBatch::query()
            ->with(['payrollPeriod', 'generatedBy', 'sentBy', 'confirmedBy', 'cancelledBy'])
            ->latest()
            ->paginate(20);

        $periods = PayrollPeriod::query()
            ->whereIn('status', ['approved', 'paid'])
            ->whereDoesntHave('bankTransferBatches', function ($query) {
                $query->whereIn('status', ['generated', 'sent', 'confirmed']);
            })
            ->orderByDesc('id')
            ->get(['id', 'period_number', 'month', 'status']);

        return view('payroll_bank_transfer_batches.index', compact('batches', 'periods'));
    }

    public function show(PayrollBankTransferBatch $batch)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.view'), 403);

        $batch->load(['payrollPeriod', 'generatedBy', 'sentBy', 'confirmedBy', 'cancelledBy']);

        $items = $this->batchItems($batch)->get();

        return view('payroll_bank_transfer_batches.show', compact('batch', 'items'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.create'), 403);

        $data = $request->validate([
            'payroll_period_id' => 'required|exists:payroll_periods,id',
            'notes' => 'nullable|string',
        ], [
            'payroll_period_id.required' => 'يجب اختيار مسير الرواتب',
            'payroll_period_id.exists' => 'مسير الرواتب غير صحيح',
        ]);

        $period = PayrollPeriod::findOrFail($data['payroll_period_id']);

        if (!in_array($period->status, ['approved', 'paid'], true)) {
            return back()->with('error', 'لا يمكن إنشاء دفعة تحويل إلا لمسير معتمد أو مدفوع.');
        }

        $activeExists = PayrollBankTransferBatch::query()
            ->where('payroll_period_id', $period->id)
            ->whereIn('status', ['generated', 'sent', 'confirmed'])
            ->exists();

        if ($activeExists) {
            return back()->with('error', 'يوجد دفعة تحويل فعالة لهذا المسير بالفعل.');
        }

        $stats = $this->periodTransferStats($period);

        if ($stats['employees_count'] <= 0) {
            return back()->with('error', 'لا توجد رواتب صافية قابلة للتحويل لهذا المسير.');
        }

        PayrollBankTransferBatch::create([
            'payroll_period_id' => $period->id,
            'batch_number' => PayrollBankTransferBatch::generateNumber(),
            'status' => 'generated',
            'employees_count' => $stats['employees_count'],
            'total_amount' => $stats['total_amount'],
            'missing_bank_data_count' => $stats['missing_bank_data_count'],
            'generated_by' => auth()->id(),
            'generated_at' => now(),
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'تم إنشاء دفعة تحويل الرواتب بنجاح.');
    }

    public function markSent(PayrollBankTransferBatch $batch)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.send'), 403);

        if (!$batch->can_send) {
            return back()->with('error', 'لا يمكن إرسال هذه الدفعة في حالتها الحالية.');
        }

        $batch->update([
            'status' => 'sent',
            'sent_by' => auth()->id(),
            'sent_at' => now(),
        ]);

        return back()->with('success', 'تم تسجيل إرسال دفعة التحويل للبنك بنجاح.');
    }

    public function confirm(Request $request, PayrollBankTransferBatch $batch)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.confirm'), 403);

        if (!$batch->can_confirm) {
            return back()->with('error', 'لا يمكن تأكيد هذه الدفعة في حالتها الحالية.');
        }

        $data = $request->validate([
            'bank_reference' => 'nullable|string|max:255',
            'bank_transfer_date' => 'nullable|date',
            'confirmation_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'confirmation_file.mimes' => 'ملف إثبات التحويل يجب أن يكون PDF أو صورة JPG/PNG',
            'confirmation_file.max' => 'حجم ملف إثبات التحويل يجب ألا يتجاوز 5MB',
        ]);

        $filePath = null;

        if ($request->hasFile('confirmation_file')) {
            $filePath = $request->file('confirmation_file')
                ->store('payroll_bank_confirmations', 'public');
        }

        $batch->update([
            'status' => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
            'bank_reference' => $data['bank_reference'] ?? null,
            'bank_transfer_date' => $data['bank_transfer_date'] ?? now()->toDateString(),
            'confirmation_file' => $filePath,
        ]);

        return back()->with('success', 'تم تأكيد تحويل الرواتب وتسجيل بيانات مرجع البنك بنجاح.');
    }

    public function cancel(PayrollBankTransferBatch $batch)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.cancel'), 403);

        if (!$batch->can_cancel) {
            return back()->with('error', 'لا يمكن إلغاء هذه الدفعة في حالتها الحالية.');
        }

        $batch->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'تم إلغاء دفعة التحويل بنجاح.');
    }

    public function exportCsv(PayrollBankTransferBatch $batch): StreamedResponse
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.export'), 403);

        $batch->load('payrollPeriod');

        if (!$batch->payrollPeriod) {
            abort(404, 'مسير الرواتب المرتبط بالدفعة غير موجود.');
        }

        $items = $this->batchItems($batch)->get();
        $fileName = 'bank_transfer_batch_' . $batch->batch_number . '_' . now()->format('Y_m_d_H_i_s') . '.csv';

        return response()->streamDownload(function () use ($items, $batch) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Batch Number',
                'Batch Status',
                'Bank Reference',
                'Bank Transfer Date',
                'Payroll Period',
                'Payroll Month',
                'Employee Number',
                'Employee Name',
                'Department',
                'Bank Name',
                'IBAN',
                'Payment Method',
                'Net Salary',
                'Validation Status',
                'Notes',
            ]);

            foreach ($items as $item) {
                $employee = $item->employee;

                $bankName = $employee?->bank_name ?: '';
                $iban = $employee?->iban ?: '';
                $method = $item->salary_payment_method_name
                    ?? $employee?->salaryPaymentMethod?->name_ar
                    ?? $employee?->salary_payment_method
                    ?? '';

                $isReady = !empty($bankName) && !empty($iban);

                fputcsv($handle, [
                    $batch->batch_number,
                    $batch->status,
                    $batch->bank_reference,
                    optional($batch->bank_transfer_date)->format('Y-m-d'),
                    $batch->payrollPeriod?->period_number,
                    $batch->payrollPeriod?->month,
                    $item->employee_number,
                    $item->employee_name,
                    $item->employee_department ?? $employee?->department?->name ?? '',
                    $bankName,
                    $iban,
                    $method,
                    number_format((float) $item->net_salary, 2, '.', ''),
                    $isReady ? 'READY' : 'MISSING_BANK_DATA',
                    $isReady ? '' : 'Missing bank name or IBAN',
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportExcel(PayrollBankTransferBatch $batch)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.export'), 403);

        $batch->load('payrollPeriod');
        $items = $this->batchItems($batch)->get();

        $fileName = 'bank_transfer_batch_' . $batch->batch_number . '_' . now()->format('Y_m_d_H_i_s') . '.xls';

        return response()
            ->view('payroll_bank_transfer_batches.excel', compact('batch', 'items'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function printPdf(PayrollBankTransferBatch $batch)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.export'), 403);

        $batch->load('payrollPeriod');
        $items = $this->batchItems($batch)->get();

        return view('payroll_bank_transfer_batches.print_pdf', compact('batch', 'items'));
    }

    private function periodTransferStats(PayrollPeriod $period): array
    {
        $items = PayrollItem::query()
            ->with('employee')
            ->where('payroll_period_id', $period->id)
            ->where('net_salary', '>', 0)
            ->get();

        return [
            'employees_count' => $items->count(),
            'total_amount' => round((float) $items->sum('net_salary'), 2),
            'missing_bank_data_count' => $items->filter(function ($item) {
                return empty($item->employee?->bank_name) || empty($item->employee?->iban);
            })->count(),
        ];
    }

    private function batchItems(PayrollBankTransferBatch $batch)
    {
        return PayrollItem::query()
            ->with([
                'employee.salaryPaymentMethod',
                'employee.department',
                'employee.position',
            ])
            ->where('payroll_period_id', $batch->payroll_period_id)
            ->where('net_salary', '>', 0)
            ->orderBy('employee_number');
    }
}
