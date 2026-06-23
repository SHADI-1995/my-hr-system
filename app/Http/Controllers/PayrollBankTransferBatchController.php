<?php

namespace App\Http\Controllers;

use App\Models\PayrollBankTransferBatch;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($period, $data, $stats) {
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
        });

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

    public function confirm(PayrollBankTransferBatch $batch)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfer_batches.confirm'), 403);

        if (!$batch->can_confirm) {
            return back()->with('error', 'لا يمكن تأكيد هذه الدفعة في حالتها الحالية.');
        }

        $batch->update([
            'status' => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'تم تأكيد تحويل الرواتب بنجاح.');
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
}
