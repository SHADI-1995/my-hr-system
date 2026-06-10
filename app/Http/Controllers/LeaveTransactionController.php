<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLeaveTransaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveTransactionController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_transactions.view'), 403);

        $query = $this->buildQuery($request);

        $transactions = $query
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $employees = Employee::orderBy('full_name')->get();

        $summaryQuery = $this->buildQuery($request);

        $summary = [
            'total_transactions' => (clone $summaryQuery)->count(),
            'total_positive_days' => (float) (clone $summaryQuery)->where('days', '>', 0)->sum('days'),
            'total_negative_days' => (float) (clone $summaryQuery)->where('days', '<', 0)->sum('days'),
            'workflow_transactions' => (clone $summaryQuery)
                ->whereIn('transaction_type', $this->workflowTransactionTypes())
                ->count(),
            'balance_transactions' => (clone $summaryQuery)
                ->whereNotIn('transaction_type', $this->workflowTransactionTypes())
                ->count(),
            'last_transaction_at' => optional((clone $summaryQuery)->latest()->first())->created_at,
        ];

        $transactionTypes = $this->transactionTypesForFilter();

        return view('leave_transactions.index', compact(
            'transactions',
            'employees',
            'summary',
            'transactionTypes'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        abort_if(!auth()->user()->hasPermission('leave_transactions.export'), 403);

        $fileName = 'leave-transactions-' . now()->format('Y-m-d-H-i') . '.csv';

        $query = $this->buildQuery($request)
            ->latest();

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM لدعم العربية في Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'رقم الحركة',
                'الموظف',
                'الرقم الوظيفي',
                'نوع الحركة',
                'تصنيف الحركة',
                'الأيام',
                'الرصيد قبل',
                'الرصيد بعد',
                'الوصف',
                'تم بواسطة',
                'التاريخ',
            ]);

            $query->chunk(300, function ($transactions) use ($handle) {
                foreach ($transactions as $transaction) {
                    fputcsv($handle, [
                        $transaction->id,
                        $transaction->employee->display_name
                        ?? $transaction->employee->full_name
                            ?? $transaction->employee->name
                            ?? '-',
                        $transaction->employee->employee_number ?? '-',
                        $this->transactionTypeName($transaction->transaction_type),
                        $this->transactionCategoryName($transaction->transaction_type),
                        number_format((float) $transaction->days, 2),
                        number_format((float) $transaction->before_balance, 2),
                        number_format((float) $transaction->after_balance, 2),
                        $transaction->description ?? '-',
                        $transaction->createdBy->name ?? '-',
                        optional($transaction->created_at)->format('Y-m-d H:i') ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildQuery(Request $request)
    {
        $query = EmployeeLeaveTransaction::with([
            'employee',
            'createdBy',
            'leaveBalance',
        ]);

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->transaction_category === 'workflow') {
            $query->whereIn('transaction_type', $this->workflowTransactionTypes());
        }

        if ($request->transaction_category === 'balance') {
            $query->whereNotIn('transaction_type', $this->workflowTransactionTypes());
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function transactionTypesForFilter(): array
    {
        return [
            'workflow' => [
                'leave_request_created' => 'تم تقديم طلب إجازة',
                'manager_approved' => 'موافقة المدير المباشر',
                'manager_rejected' => 'رفض المدير المباشر',
                'hr_approved' => 'موافقة الموارد البشرية',
                'hr_rejected' => 'رفض الموارد البشرية',
                'leave_cancelled' => 'إلغاء طلب قبل الاعتماد النهائي',
                'leave_cancelled_after_approval' => 'إلغاء إجازة معتمدة',
            ],
            'balance' => [
                'annual_accrual' => 'إضافة رصيد سنوي',
                'carry_forward' => 'ترحيل رصيد',
                'policy_recalculation' => 'إعادة احتساب سياسة',
                'paid_leave_deduction' => 'خصم إجازة مدفوعة',
                'paid_leave_reversal' => 'إرجاع رصيد إجازة',
                'unpaid_leave_record' => 'تسجيل إجازة غير مدفوعة',
                'unpaid_leave_reversal' => 'إلغاء إجازة غير مدفوعة',
                'official_leave_record' => 'تسجيل إجازة رسمية',
                'other_leave_record' => 'تسجيل إجازة أخرى',
                'other_leave_reversal' => 'إلغاء إجازة أخرى',
            ],
        ];
    }

    private function workflowTransactionTypes(): array
    {
        return [
            'leave_request_created',
            'manager_approved',
            'manager_rejected',
            'hr_approved',
            'hr_rejected',
            'leave_cancelled',
            'leave_cancelled_after_approval',
        ];
    }

    private function transactionTypeName(?string $type): string
    {
        return match ($type) {
            'leave_request_created' => 'تم تقديم طلب إجازة',
            'manager_approved' => 'موافقة المدير المباشر',
            'manager_rejected' => 'رفض المدير المباشر',
            'hr_approved' => 'موافقة الموارد البشرية',
            'hr_rejected' => 'رفض الموارد البشرية',
            'leave_cancelled' => 'إلغاء طلب قبل الاعتماد النهائي',
            'leave_cancelled_after_approval' => 'إلغاء إجازة معتمدة',

            'annual_accrual' => 'إضافة رصيد سنوي',
            'carry_forward' => 'ترحيل رصيد',
            'policy_recalculation' => 'إعادة احتساب سياسة',
            'paid_leave_deduction' => 'خصم إجازة مدفوعة',
            'paid_leave_reversal' => 'إرجاع رصيد إجازة',
            'unpaid_leave_record' => 'تسجيل إجازة غير مدفوعة',
            'unpaid_leave_reversal' => 'إلغاء إجازة غير مدفوعة',
            'official_leave_record' => 'تسجيل إجازة رسمية',
            'other_leave_record' => 'تسجيل إجازة أخرى',
            'other_leave_reversal' => 'إلغاء إجازة أخرى',
            default => $type ?? '-',
        };
    }

    private function transactionCategoryName(?string $type): string
    {
        if (in_array($type, $this->workflowTransactionTypes(), true)) {
            return 'مسار الموافقات';
        }

        return 'حركة رصيد';
    }
}
