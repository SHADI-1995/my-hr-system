<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeLeaveTransaction;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LeaveReportsHubController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_reports.view'), 403);

        $reports = $this->reports();
        $search = trim((string) $request->get('search'));
        $selectedReport = $request->get('report_key');

        $filteredReports = collect($reports)->filter(function ($report) use ($search) {
            if ($search === '') {
                return true;
            }

            $q = mb_strtolower($search);

            return str_contains(mb_strtolower($report['title']), $q)
                || str_contains(mb_strtolower($report['description']), $q)
                || str_contains(mb_strtolower($report['category']), $q);
        })->values()->all();

        $currentReport = $selectedReport ? collect($reports)->firstWhere('key', $selectedReport) : null;

        return view('leave_reports.hub', compact(
            'reports',
            'filteredReports',
            'selectedReport',
            'currentReport',
            'search'
        ));
    }

    public function exportExcel(string $reportKey): Response
    {
        abort_if(!auth()->user()->hasPermission('leave_reports.export'), 403);

        $report = collect($this->reports())->firstWhere('key', $reportKey);

        abort_if(!$report, 404, 'التقرير غير موجود');

        [$headers, $rows] = $this->rowsForReport($reportKey);

        $html = view('leave_reports.hub_excel', compact('report', 'headers', 'rows'))->render();

        $fileName = 'leave-report-' . $reportKey . '-' . now()->format('Y-m-d-H-i') . '.xls';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function rowsForReport(string $key): array
    {
        return match ($key) {
            'leave_balances' => $this->balanceRows(),
            'leave_transactions' => $this->transactionRows(),
            'transactions_workflow' => $this->transactionRows('workflow'),
            'transactions_balance' => $this->transactionRows('balance'),
            'department_summary' => $this->departmentSummaryRows(),
            'leave_type_summary' => $this->leaveTypeSummaryRows(),
            'manager_performance' => $this->managerPerformanceRows(),
            'hr_performance' => $this->hrPerformanceRows(),

            'pending_manager' => $this->leaveRows(['workflow_status' => 'pending_manager']),
            'pending_hr' => $this->leaveRows(['workflow_status' => 'manager_approved_pending_hr']),
            'approved_by_hr' => $this->leaveRows(['workflow_status' => 'approved_by_hr']),
            'rejected_by_manager' => $this->leaveRows(['workflow_status' => 'rejected_by_manager']),
            'rejected_by_hr' => $this->leaveRows(['workflow_status' => 'rejected_by_hr']),
            'cancelled' => $this->leaveRows(['workflow_status' => 'cancelled']),
            'deducted' => $this->leaveRows(['deduction_status' => 'deducted']),
            'not_deducted' => $this->leaveRows(['deduction_status' => 'not_deducted']),
            'reversed' => $this->leaveRows(['deduction_status' => 'reversed']),
            'current_leaves' => $this->leaveRows(['current' => true]),
            'upcoming_leaves' => $this->leaveRows(['upcoming' => true]),
            'attachments' => $this->leaveRows(['attachments' => true]),

            default => $this->leaveRows([]),
        };
    }

    private function leaveRows(array $filters): array
    {
        $query = LeaveRequest::with([
            'employee.department',
            'employee.position',
            'employee.directManagerUser',
            'leaveType',
            'directManagerApprovedBy',
            'directManagerRejectedBy',
            'hrApprovedBy',
            'hrRejectedBy',
        ]);

        if (!empty($filters['workflow_status'])) {
            $query->where('workflow_status', $filters['workflow_status']);
        }

        if (($filters['deduction_status'] ?? null) === 'deducted') {
            $query->where('workflow_status', 'approved_by_hr');
        }

        if (($filters['deduction_status'] ?? null) === 'not_deducted') {
            $query->whereIn('workflow_status', [
                'pending_manager',
                'manager_approved_pending_hr',
                'rejected_by_manager',
                'rejected_by_hr',
                'cancelled',
            ]);
        }

        if (($filters['deduction_status'] ?? null) === 'reversed') {
            $query->where('workflow_status', 'cancelled')->where('hr_status', 'cancelled_after_approval');
        }

        if (($filters['current'] ?? false) === true) {
            $query->where('workflow_status', 'approved_by_hr')
                ->whereDate('start_date', '<=', now()->toDateString())
                ->whereDate('end_date', '>=', now()->toDateString());
        }

        if (($filters['upcoming'] ?? false) === true) {
            $query->where('workflow_status', 'approved_by_hr')
                ->whereDate('start_date', '>', now()->toDateString());
        }

        if (($filters['attachments'] ?? false) === true) {
            $query->whereNotNull('attachment');
        }

        $headers = [
            'رقم الطلب',
            'الموظف',
            'الرقم الوظيفي',
            'القسم',
            'الوظيفة',
            'نوع الإجازة',
            'من تاريخ',
            'إلى تاريخ',
            'عدد الأيام',
            'حالة المسار',
            'المدير المباشر',
            'قرار المدير',
            'وقت قرار المدير',
            'قرار HR',
            'وقت قرار HR',
            'حالة الخصم',
            'سبب الطلب',
            'سبب الرفض',
            'تاريخ الطلب',
        ];

        $rows = $query->orderByDesc('id')->get()->map(function ($r) {
            $managerAt = optional($r->direct_manager_approved_at ?? $r->direct_manager_rejected_at)->format('Y-m-d H:i') ?? '-';
            $hrAt = optional($r->hr_approved_at ?? $r->hr_rejected_at)->format('Y-m-d H:i') ?? '-';

            return [
                $r->id,
                $r->employee->display_name ?? $r->employee->full_name ?? $r->employee->name ?? '-',
                $r->employee->employee_number ?? '-',
                $r->employee->department->name ?? '-',
                $r->employee->position->title ?? '-',
                $r->leaveType->name ?? '-',
                optional($r->start_date)->format('Y-m-d') ?? '-',
                optional($r->end_date)->format('Y-m-d') ?? '-',
                number_format((float) $r->days_count, 2),
                $this->workflowName($r->workflow_status),
                $r->employee->directManagerUser->name ?? '-',
                $this->decisionName($r->direct_manager_status),
                $managerAt,
                $this->hrName($r->hr_status),
                $hrAt,
                $this->deductionName($r),
                $r->reason ?? '-',
                $r->reject_reason ?? $r->direct_manager_reject_reason ?? $r->hr_reject_reason ?? '-',
                optional($r->created_at)->format('Y-m-d H:i') ?? '-',
            ];
        })->all();

        return [$headers, $rows];
    }

    private function transactionRows(?string $category = null): array
    {
        $workflowTypes = $this->workflowTransactionTypes();

        $query = EmployeeLeaveTransaction::with(['employee', 'createdBy']);

        if ($category === 'workflow') {
            $query->whereIn('transaction_type', $workflowTypes);
        }

        if ($category === 'balance') {
            $query->whereNotIn('transaction_type', $workflowTypes);
        }

        $headers = [
            'رقم الحركة',
            'الموظف',
            'الرقم الوظيفي',
            'نوع الحركة',
            'التصنيف',
            'الأيام',
            'الرصيد قبل',
            'الرصيد بعد',
            'الوصف',
            'تم بواسطة',
            'التاريخ',
        ];

        $rows = $query->latest()->get()->map(function ($t) use ($workflowTypes) {
            $isWorkflow = in_array($t->transaction_type, $workflowTypes, true);

            return [
                $t->id,
                $t->employee->display_name ?? $t->employee->full_name ?? $t->employee->name ?? '-',
                $t->employee->employee_number ?? '-',
                $this->transactionName($t->transaction_type),
                $isWorkflow ? 'مسار الموافقات' : 'حركة رصيد',
                number_format((float) $t->days, 2),
                number_format((float) $t->before_balance, 2),
                number_format((float) $t->after_balance, 2),
                $t->description ?? '-',
                $t->createdBy->name ?? '-',
                optional($t->created_at)->format('Y-m-d H:i') ?? '-',
            ];
        })->all();

        return [$headers, $rows];
    }

    private function balanceRows(): array
    {
        $headers = [
            'الموظف',
            'الرقم الوظيفي',
            'الرصيد السنوي',
            'الرصيد المرحل',
            'المستخدم',
            'المتبقي',
            'بداية سنة الخدمة',
            'نهاية سنة الخدمة',
            'الحالة',
        ];

        $rows = EmployeeLeaveBalance::with('employee')->orderByDesc('id')->get()->map(function ($b) {
            return [
                $b->employee->display_name ?? $b->employee->full_name ?? $b->employee->name ?? '-',
                $b->employee->employee_number ?? '-',
                number_format((float) ($b->annual_days ?? 0), 2),
                number_format((float) ($b->carried_forward_days ?? 0), 2),
                number_format((float) ($b->used_days ?? 0), 2),
                number_format((float) ($b->remaining_days ?? 0), 2),
                $b->service_year_start ?? '-',
                $b->service_year_end ?? '-',
                $b->status ?? '-',
            ];
        })->all();

        return [$headers, $rows];
    }

    private function departmentSummaryRows(): array
    {
        $headers = ['القسم', 'عدد الطلبات', 'إجمالي الأيام', 'المعتمدة', 'المرفوضة', 'الملغاة'];

        $rows = LeaveRequest::with('employee.department')->get()
            ->groupBy(fn ($r) => $r->employee->department->name ?? 'بدون قسم')
            ->map(fn ($items, $name) => [
                $name,
                $items->count(),
                number_format((float) $items->sum('days_count'), 2),
                $items->where('workflow_status', 'approved_by_hr')->count(),
                $items->whereIn('workflow_status', ['rejected_by_manager', 'rejected_by_hr'])->count(),
                $items->where('workflow_status', 'cancelled')->count(),
            ])->values()->all();

        return [$headers, $rows];
    }

    private function leaveTypeSummaryRows(): array
    {
        $headers = ['نوع الإجازة', 'عدد الطلبات', 'إجمالي الأيام', 'المعتمدة', 'المرفوضة', 'الملغاة'];

        $rows = LeaveRequest::with('leaveType')->get()
            ->groupBy(fn ($r) => $r->leaveType->name ?? 'بدون نوع')
            ->map(fn ($items, $name) => [
                $name,
                $items->count(),
                number_format((float) $items->sum('days_count'), 2),
                $items->where('workflow_status', 'approved_by_hr')->count(),
                $items->whereIn('workflow_status', ['rejected_by_manager', 'rejected_by_hr'])->count(),
                $items->where('workflow_status', 'cancelled')->count(),
            ])->values()->all();

        return [$headers, $rows];
    }

    private function managerPerformanceRows(): array
    {
        $headers = ['المدير المباشر', 'إجمالي الطلبات', 'موافق عليها', 'مرفوضة', 'معلقة'];

        $rows = LeaveRequest::with('employee.directManagerUser')->get()
            ->groupBy(fn ($r) => $r->employee->directManagerUser->name ?? 'بدون مدير')
            ->map(fn ($items, $name) => [
                $name,
                $items->count(),
                $items->where('direct_manager_status', 'approved')->count(),
                $items->where('direct_manager_status', 'rejected')->count(),
                $items->where('direct_manager_status', 'pending')->count(),
            ])->values()->all();

        return [$headers, $rows];
    }

    private function hrPerformanceRows(): array
    {
        return [
            ['المؤشر', 'العدد'],
            [
                ['طلبات وصلت HR', LeaveRequest::whereIn('hr_status', ['pending', 'approved', 'rejected'])->count()],
                ['معتمدة من HR', LeaveRequest::where('hr_status', 'approved')->count()],
                ['مرفوضة من HR', LeaveRequest::where('hr_status', 'rejected')->count()],
                ['معلقة لدى HR', LeaveRequest::where('hr_status', 'pending')->count()],
            ],
        ];
    }

    private function reports(): array
    {
        return [
            ['key'=>'leave_general','title'=>'تقرير الإجازات العام','category'=>'طلبات الإجازات','description'=>'جميع طلبات الإجازات حسب الموظف والقسم والنوع والحالة والفترة.','route'=>route('leave-reports.index')],
            ['key'=>'workflow_status','title'=>'تقرير مسار الموافقات','category'=>'مسار الموافقة','description'=>'حالة الطلب عند المدير المباشر ثم الموارد البشرية.','route'=>route('leave-reports.index')],
            ['key'=>'pending_manager','title'=>'طلبات بانتظار المدير المباشر','category'=>'طلبات معلقة','description'=>'الطلبات التي لم يقررها المدير المباشر بعد.','route'=>route('leave-reports.index',['workflow_status'=>'pending_manager'])],
            ['key'=>'pending_hr','title'=>'طلبات بانتظار الموارد البشرية','category'=>'طلبات معلقة','description'=>'الطلبات التي وافق عليها المدير وتنتظر الموارد البشرية.','route'=>route('leave-reports.index',['workflow_status'=>'manager_approved_pending_hr'])],
            ['key'=>'approved_by_hr','title'=>'الإجازات المعتمدة نهائيًا','category'=>'طلبات معتمدة','description'=>'الإجازات المعتمدة من الموارد البشرية والتي تم خصمها من الرصيد.','route'=>route('leave-reports.index',['workflow_status'=>'approved_by_hr'])],
            ['key'=>'rejected_by_manager','title'=>'الإجازات المرفوضة من المدير','category'=>'طلبات مرفوضة','description'=>'الطلبات التي رفضها المدير المباشر ولم تنتقل إلى HR.','route'=>route('leave-reports.index',['workflow_status'=>'rejected_by_manager'])],
            ['key'=>'rejected_by_hr','title'=>'الإجازات المرفوضة من الموارد البشرية','category'=>'طلبات مرفوضة','description'=>'الطلبات التي رفضتها الموارد البشرية بعد موافقة المدير.','route'=>route('leave-reports.index',['workflow_status'=>'rejected_by_hr'])],
            ['key'=>'cancelled','title'=>'الإجازات الملغاة','category'=>'طلبات ملغاة','description'=>'الطلبات الملغاة قبل أو بعد الاعتماد النهائي.','route'=>route('leave-reports.index',['workflow_status'=>'cancelled'])],
            ['key'=>'leave_balances','title'=>'تقرير أرصدة الإجازات','category'=>'الأرصدة','description'=>'الرصيد السنوي والمستخدم والمتبقي لكل موظف.','route'=>route('leave-balances.index')],
            ['key'=>'leave_transactions','title'=>'سجل حركات الإجازات','category'=>'الحركات','description'=>'حركات الرصيد ومسار الموافقات والخصم والإرجاع.','route'=>route('leave-transactions.index')],
            ['key'=>'transactions_workflow','title'=>'حركات مسار الموافقات','category'=>'الحركات','description'=>'تقديم الطلب وموافقة المدير وHR والرفض والإلغاء.','route'=>route('leave-transactions.index')],
            ['key'=>'transactions_balance','title'=>'حركات الرصيد','category'=>'الحركات','description'=>'إضافة الرصيد والخصم والإرجاع وإعادة الاحتساب.','route'=>route('leave-transactions.index')],
            ['key'=>'deducted','title'=>'الإجازات التي تم خصمها من الرصيد','category'=>'الأرصدة','description'=>'الإجازات المعتمدة نهائيًا والتي أثرت على الرصيد.','route'=>route('leave-reports.index',['deduction_status'=>'deducted'])],
            ['key'=>'not_deducted','title'=>'الإجازات التي لم تخصم من الرصيد','category'=>'الأرصدة','description'=>'الطلبات المعلقة أو المرفوضة أو الملغاة قبل الاعتماد.','route'=>route('leave-reports.index',['deduction_status'=>'not_deducted'])],
            ['key'=>'reversed','title'=>'الإجازات التي تم إرجاع رصيدها','category'=>'الأرصدة','description'=>'الإجازات الملغاة بعد الاعتماد وتم إرجاع رصيدها.','route'=>route('leave-reports.index',['deduction_status'=>'reversed'])],
            ['key'=>'current_leaves','title'=>'الموظفون في إجازة حاليًا','category'=>'تشغيلي','description'=>'الموظفون الذين لديهم إجازة معتمدة خلال تاريخ اليوم.','route'=>route('leave-reports.index')],
            ['key'=>'upcoming_leaves','title'=>'الإجازات القادمة','category'=>'تشغيلي','description'=>'الإجازات المعتمدة التي ستبدأ لاحقًا.','route'=>route('leave-reports.index')],
            ['key'=>'department_summary','title'=>'الإجازات حسب القسم','category'=>'تحليلي','description'=>'تحليل عدد الطلبات والأيام حسب الأقسام.','route'=>route('leave-reports.index')],
            ['key'=>'leave_type_summary','title'=>'الإجازات حسب نوع الإجازة','category'=>'تحليلي','description'=>'تحليل أكثر أنواع الإجازات استخدامًا.','route'=>route('leave-reports.index')],
            ['key'=>'manager_performance','title'=>'أداء المديرين في الموافقات','category'=>'إداري','description'=>'عدد الطلبات لكل مدير ومتوسط سرعة القرار.','route'=>route('leave-reports.index')],
            ['key'=>'hr_performance','title'=>'أداء الموارد البشرية في الموافقات','category'=>'إداري','description'=>'عدد الطلبات المعالجة من HR ومتوسط وقت المعالجة.','route'=>route('leave-reports.index')],
            ['key'=>'attachments','title'=>'تقرير مرفقات الإجازات','category'=>'المرفقات','description'=>'الطلبات التي تحتوي على مرفقات وصور أو ملفات PDF.','route'=>route('leave-reports.index')],
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

    private function workflowName(?string $status): string
    {
        return match ($status) {
            'pending_manager' => 'بانتظار المدير المباشر',
            'manager_approved_pending_hr' => 'موافق من المدير - بانتظار HR',
            'approved_by_hr' => 'موافق نهائيًا من HR',
            'rejected_by_manager' => 'مرفوض من المدير',
            'rejected_by_hr' => 'مرفوض من HR',
            'cancelled' => 'ملغي',
            default => 'قيد المراجعة',
        };
    }

    private function decisionName(?string $status): string
    {
        return match ($status) {
            'approved' => 'موافق',
            'rejected' => 'مرفوض',
            default => 'قيد المراجعة',
        };
    }

    private function hrName(?string $status): string
    {
        return match ($status) {
            'approved' => 'موافق',
            'rejected' => 'مرفوض',
            'pending' => 'قيد المعالجة',
            'waiting_manager' => 'بانتظار المدير',
            'not_required' => 'غير مطلوب',
            'cancelled' => 'ملغي',
            'cancelled_after_approval' => 'ملغي بعد الاعتماد',
            default => '-',
        };
    }

    private function deductionName(LeaveRequest $r): string
    {
        return match (true) {
            $r->workflow_status === 'approved_by_hr' => 'تم الخصم',
            $r->workflow_status === 'cancelled' && $r->hr_status === 'cancelled_after_approval' => 'تم إرجاع الرصيد',
            default => 'لم يتم الخصم',
        };
    }

    private function transactionName(?string $type): string
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
            'paid_leave_reversal' => 'إرجاع رصيد',
            'unpaid_leave_record' => 'تسجيل إجازة غير مدفوعة',
            'unpaid_leave_reversal' => 'إلغاء إجازة غير مدفوعة',
            'official_leave_record' => 'تسجيل إجازة رسمية',
            'other_leave_record' => 'تسجيل إجازة أخرى',
            'other_leave_reversal' => 'إلغاء إجازة أخرى',
            default => $type ?? '-',
        };
    }
}
