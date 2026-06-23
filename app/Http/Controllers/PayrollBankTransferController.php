<?php

namespace App\Http\Controllers;

use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollBankTransferController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfers.view'), 403);

        $periods = PayrollPeriod::query()
            ->whereIn('status', ['approved', 'paid'])
            ->orderByDesc('id')
            ->get(['id', 'period_number', 'month', 'status', 'total_net_salary', 'employees_count']);

        $selectedPeriod = null;
        $items = collect();

        if ($request->filled('payroll_period_id')) {
            $selectedPeriod = PayrollPeriod::query()
                ->whereIn('status', ['approved', 'paid'])
                ->findOrFail($request->payroll_period_id);

            $items = $this->transferItems($selectedPeriod)->get();
        }

        return view('payroll_bank_transfers.index', compact('periods', 'selectedPeriod', 'items'));
    }

    public function exportExcel(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfers.export'), 403);

        if (!in_array($payrollPeriod->status, ['approved', 'paid'], true)) {
            return back()->with('error', 'لا يمكن تصدير ملف التحويل إلا لمسير معتمد أو مدفوع.');
        }

        $items = $this->transferItems($payrollPeriod)->get();
        $fileName = 'bank_transfer_' . $payrollPeriod->period_number . '_' . now()->format('Y_m_d_H_i_s') . '.xls';

        return response()
            ->view('payroll_bank_transfers.excel', compact('payrollPeriod', 'items'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function exportCsv(PayrollPeriod $payrollPeriod): StreamedResponse
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfers.export'), 403);

        if (!in_array($payrollPeriod->status, ['approved', 'paid'], true)) {
            abort(422, 'لا يمكن تصدير ملف التحويل إلا لمسير معتمد أو مدفوع.');
        }

        $items = $this->transferItems($payrollPeriod)->get();
        $fileName = 'bank_transfer_' . $payrollPeriod->period_number . '_' . now()->format('Y_m_d_H_i_s') . '.csv';

        return response()->streamDownload(function () use ($items, $payrollPeriod) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM حتى يظهر العربي بشكل صحيح في Excel.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
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
                    $payrollPeriod->period_number,
                    $payrollPeriod->month,
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

    public function printPdf(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_bank_transfers.export'), 403);

        if (!in_array($payrollPeriod->status, ['approved', 'paid'], true)) {
            return back()->with('error', 'لا يمكن طباعة ملف التحويل إلا لمسير معتمد أو مدفوع.');
        }

        $items = $this->transferItems($payrollPeriod)->get();

        return view('payroll_bank_transfers.print_pdf', compact('payrollPeriod', 'items'));
    }

    private function transferItems(PayrollPeriod $payrollPeriod)
    {
        return PayrollItem::query()
            ->with([
                'employee.salaryPaymentMethod',
                'employee.department',
                'employee.position',
            ])
            ->where('payroll_period_id', $payrollPeriod->id)
            ->where('net_salary', '>', 0)
            ->orderBy('employee_number');
    }
}
