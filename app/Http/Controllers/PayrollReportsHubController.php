<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollBankTransferBatch;
use App\Models\PayrollBankTransferBatchLog;
use App\Models\PayrollItem;
use App\Models\PayrollItemComponent;
use App\Models\PayrollPeriod;
use App\Models\PayrollPeriodLog;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class PayrollReportsHubController extends Controller
{
    private array $reports = [
        'payroll_summary' => 'ملخص مسير الرواتب',
        'employee_details' => 'تفاصيل رواتب الموظفين',
        'payslips' => 'قسائم الرواتب',
        'net_salary' => 'صافي الرواتب',
        'gross_salary' => 'إجمالي الرواتب',
        'deductions' => 'الخصومات',
        'salary_advances' => 'السلف المخصومة',
        'cost_centers' => 'مراكز التكلفة',
        'department_payroll' => 'الرواتب حسب الأقسام',
        'payroll_statuses' => 'حالة مسيرات الرواتب',
        'bank_transfer' => 'كشف تحويل الرواتب للبنك',
        'bank_transfer_batches' => 'دفعات تحويل الرواتب',
        'missing_bank_data' => 'نواقص البيانات البنكية',
        'payroll_period_logs' => 'سجل حركات مسير الرواتب',
        'bank_transfer_batch_logs' => 'سجل حركات دفعات التحويل',

        /*
         |--------------------------------------------------------------------------
         | المرحلة الثالثة - تقارير تحليلية
         |--------------------------------------------------------------------------
         */
        'payroll_comparison' => 'مقارنة الرواتب بين مسيرين',
        'net_salary_changes' => 'تغير صافي الراتب',
        'employees_without_payroll' => 'الموظفون غير الموجودين في المسير',
        'employee_status_payroll' => 'الرواتب حسب حالة الموظف',
        'nationality_payroll' => 'الرواتب حسب الجنسية',
        'payable_days' => 'أيام الاستحقاق',
        'paid_archive' => 'أرشيف المسيرات المدفوعة',
        'payroll_warnings' => 'الأخطاء والتحذيرات في المسير',

        /*
         |--------------------------------------------------------------------------
         | المرحلة الرابعة - تقارير تفصيلية إضافية
         |--------------------------------------------------------------------------
         */
        'payroll_components' => 'مكونات الرواتب التفصيلية',
        'allowances_details' => 'تفاصيل البدلات',
        'deductions_details' => 'تفاصيل الخصومات',
        'employee_payroll_history' => 'سجل رواتب الموظف',
        'monthly_payroll_trend' => 'اتجاه الرواتب شهريًا',
        'top_net_salary' => 'أعلى صافي رواتب',
        'zero_negative_net_salary' => 'رواتب صفرية أو سالبة',
        'payment_methods_summary' => 'ملخص طرق الدفع والبنوك',

        /*
         |--------------------------------------------------------------------------
         | المرحلة الخامسة - تقارير إدارية وتجميعية
         |--------------------------------------------------------------------------
         */
        'payroll_groups_summary' => 'ملخص مجموعات الرواتب',
        'position_payroll' => 'الرواتب حسب المسمى الوظيفي',
        'salary_range_distribution' => 'توزيع الرواتب حسب الشرائح',
        'allowance_component_summary' => 'ملخص البدلات حسب النوع',
        'deduction_component_summary' => 'ملخص الخصومات حسب النوع',
        'bank_summary' => 'ملخص الرواتب حسب البنك',
        'employee_cost_summary' => 'ملخص تكلفة الموظف',
        'newly_hired_payroll' => 'رواتب الموظفين الجدد',

        /*
         |--------------------------------------------------------------------------
         | المرحلة السادسة - تقارير شهرية وسنوية
         |--------------------------------------------------------------------------
         */
        'annual_payroll_summary' => 'الملخص السنوي للرواتب',
        'employee_annual_summary' => 'ملخص رواتب الموظف سنويًا',
        'department_monthly_trend' => 'اتجاه الأقسام شهريًا',
        'cost_center_monthly_trend' => 'اتجاه مراكز التكلفة شهريًا',
        'bank_monthly_trend' => 'اتجاه التحويلات البنكية شهريًا',
        'highest_deductions' => 'أعلى خصومات على الموظفين',
        'highest_advances' => 'أعلى سلف مخصومة',
        'payroll_liability_summary' => 'ملخص الالتزامات غير المدفوعة',
    ];

    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports_hub.view'), 403);

        return view('payroll_reports_hub.index', $this->buildReport($request));
    }

    public function exportExcel(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports_hub.export'), 403);

        $data = $this->buildReport($request);
        $fileName = 'payroll_report_' . $data['reportType'] . '_' . now()->format('Y_m_d_H_i_s') . '.xls';

        return response()
            ->view('payroll_reports_hub.excel', $data)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function printPdf(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports_hub.export'), 403);

        return view('payroll_reports_hub.print_pdf', $this->buildReport($request));
    }

    private function buildReport(Request $request): array
    {
        $reportType = $request->get('report_type', 'payroll_summary');

        if (!array_key_exists($reportType, $this->reports)) {
            $reportType = 'payroll_summary';
        }

        $periodsForFilter = PayrollPeriod::query()
            ->orderByDesc('id')
            ->get(['id', 'period_number', 'month', 'status', 'start_date', 'end_date']);

        $periods = $this->filteredPeriods($request);
        $periodIds = $periods->pluck('id')->values();

        $items = $this->filteredPayrollItems($request, $periodIds);
        $periodsById = $periods->keyBy('id');

        [$columns, $rows] = match ($reportType) {
            'employee_details' => $this->employeeDetailsRows($items, $periodsById),
            'payslips' => $this->payslipRows($items, $periodsById),
            'net_salary' => $this->netSalaryRows($items, $periodsById),
            'gross_salary' => $this->grossSalaryRows($items, $periodsById),
            'deductions' => $this->deductionRows($items, $periodsById),
            'salary_advances' => $this->salaryAdvanceRows($items, $periodsById),
            'cost_centers' => $this->costCenterRows($items),
            'department_payroll' => $this->departmentPayrollRows($items),
            'payroll_statuses' => $this->payrollStatusRows($periods),
            'bank_transfer' => $this->bankTransferRows($items, $periodsById),
            'bank_transfer_batches' => $this->bankTransferBatchRows($periodIds),
            'missing_bank_data' => $this->missingBankDataRows($items, $periodsById),
            'payroll_period_logs' => $this->payrollPeriodLogRows($periodIds),
            'bank_transfer_batch_logs' => $this->bankTransferBatchLogRows(),
            'payroll_comparison' => $this->payrollComparisonRows($request),
            'net_salary_changes' => $this->netSalaryChangesRows($request),
            'employees_without_payroll' => $this->employeesWithoutPayrollRows($request),
            'employee_status_payroll' => $this->employeeStatusPayrollRows($items),
            'nationality_payroll' => $this->nationalityPayrollRows($items),
            'payable_days' => $this->payableDaysRows($items, $periodsById),
            'paid_archive' => $this->paidArchiveRows($request),
            'payroll_warnings' => $this->payrollWarningsRows($items, $periodsById),
            'payroll_components' => $this->payrollComponentRows($periodIds, $periodsById),
            'allowances_details' => $this->payrollComponentRows($periodIds, $periodsById, ['allowance', 'allowances', 'بدل', 'بدلات']),
            'deductions_details' => $this->payrollComponentRows($periodIds, $periodsById, ['deduction', 'deductions', 'خصم', 'خصومات']),
            'employee_payroll_history' => $this->employeePayrollHistoryRows($items, $periodsById),
            'monthly_payroll_trend' => $this->monthlyPayrollTrendRows($periods),
            'top_net_salary' => $this->topNetSalaryRows($items, $periodsById),
            'zero_negative_net_salary' => $this->zeroNegativeNetSalaryRows($items, $periodsById),
            'payment_methods_summary' => $this->paymentMethodsSummaryRows($items),
            'payroll_groups_summary' => $this->payrollGroupsSummaryRows($items),
            'position_payroll' => $this->positionPayrollRows($items),
            'salary_range_distribution' => $this->salaryRangeDistributionRows($items),
            'allowance_component_summary' => $this->componentSummaryRows($periodIds, $periodsById, ['allowance', 'allowances', 'بدل', 'بدلات']),
            'deduction_component_summary' => $this->componentSummaryRows($periodIds, $periodsById, ['deduction', 'deductions', 'خصم', 'خصومات']),
            'bank_summary' => $this->bankSummaryRows($items),
            'employee_cost_summary' => $this->employeeCostSummaryRows($items),
            'newly_hired_payroll' => $this->newlyHiredPayrollRows($items, $periodsById),
            'annual_payroll_summary' => $this->annualPayrollSummaryRows($periods),
            'employee_annual_summary' => $this->employeeAnnualSummaryRows($items, $periodsById),
            'department_monthly_trend' => $this->departmentMonthlyTrendRows($items, $periodsById),
            'cost_center_monthly_trend' => $this->costCenterMonthlyTrendRows($items, $periodsById),
            'bank_monthly_trend' => $this->bankMonthlyTrendRows($items, $periodsById),
            'highest_deductions' => $this->highestDeductionsRows($items, $periodsById),
            'highest_advances' => $this->highestAdvancesRows($items, $periodsById),
            'payroll_liability_summary' => $this->payrollLiabilitySummaryRows($periods),
            default => $this->payrollSummaryRows($periods),
        };

        $summary = [
            'periods_count' => $periods->count(),
            'rows_count' => count($rows),
            'employees_count' => $items->count(),
            'gross_total' => $this->formatMoney($periods->sum('total_gross_salary')),
            'deductions_total' => $this->formatMoney($periods->sum('total_deductions')),
            'net_total' => $this->formatMoney($periods->sum('total_net_salary')),
        ];

        return [
            'reports' => $this->reports,
            'reportType' => $reportType,
            'reportTitle' => $this->reports[$reportType],
            'periodsForFilter' => $periodsForFilter,
            'columns' => $columns,
            'rows' => $rows,
            'summary' => $summary,
        ];
    }

    private function filteredPeriods(Request $request): Collection
    {
        $periodsQuery = PayrollPeriod::query();

        if ($request->filled('payroll_period_id')) {
            $periodsQuery->where('id', $request->payroll_period_id);
        }

        if ($request->filled('status')) {
            $periodsQuery->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $periodsQuery->where('month', $request->month);
        }

        if ($request->filled('date_from')) {
            $periodsQuery->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $periodsQuery->whereDate('end_date', '<=', $request->date_to);
        }

        return $periodsQuery->orderByDesc('id')->get();
    }

    private function filteredPayrollItems(Request $request, $periodIds): Collection
    {
        if ($periodIds->isEmpty()) {
            return collect();
        }

        $itemsQuery = PayrollItem::query()
            ->with(['employee.department', 'employee.position', 'employee.salaryPaymentMethod'])
            ->whereIn('payroll_period_id', $periodIds)
            ->orderBy('employee_number');

        if ($request->filled('employee_search')) {
            $search = $request->employee_search;

            $itemsQuery->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', '%' . $search . '%')
                    ->orWhere('employee_number', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('department')) {
            $department = $request->department;

            $itemsQuery->where(function ($q) use ($department) {
                $q->where('employee_department', 'like', '%' . $department . '%')
                    ->orWhere('department_name', 'like', '%' . $department . '%')
                    ->orWhereHas('employee.department', function ($departmentQuery) use ($department) {
                        $departmentQuery->where('name', 'like', '%' . $department . '%')
                            ->orWhere('name_ar', 'like', '%' . $department . '%');
                    });
            });
        }

        return $itemsQuery->get();
    }

    /*
     |--------------------------------------------------------------------------
     | Phase 1 + Phase 2 Reports
     |--------------------------------------------------------------------------
     */

    private function payrollSummaryRows($periods): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'status' => 'الحالة',
            'employees_count' => 'الموظفين',
            'gross' => 'إجمالي الرواتب',
            'regular_deductions' => 'خصومات عادية',
            'salary_advances' => 'السلف',
            'suspension_deductions' => 'إيقافات',
            'total_deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $rows = $periods->map(fn($period) => [
            'period_number' => $period->period_number,
            'month' => $period->month,
            'status' => $period->status_text,
            'employees_count' => $period->employees_count,
            'gross' => $this->formatMoney($period->total_gross_salary),
            'regular_deductions' => $this->formatMoney($period->total_regular_deductions),
            'salary_advances' => $this->formatMoney($period->total_salary_advances),
            'suspension_deductions' => $this->formatMoney($period->total_suspension_deductions),
            'total_deductions' => $this->formatMoney($period->total_deductions),
            'net' => $this->formatMoney($period->total_net_salary),
        ])->values()->all();

        return [$columns, $rows];
    }

    private function employeeDetailsRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'department' => 'القسم',
            'position' => 'المسمى',
            'gross' => 'إجمالي الراتب',
            'regular_deductions' => 'خصومات عادية',
            'salary_advances' => 'السلف',
            'suspension_deductions' => 'إيقافات',
            'total_deductions' => 'إجمالي الخصومات',
            'net' => 'الصافي',
        ];

        $rows = $items->map(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);

            return [
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'employee_number' => $this->value($item, ['employee_number']),
                'employee_name' => $this->value($item, ['employee_name']),
                'department' => $this->departmentName($item),
                'position' => $this->positionName($item),
                'gross' => $this->formatMoney($this->moneyValue($item, ['gross_salary', 'total_gross_salary'])),
                'regular_deductions' => $this->formatMoney($this->moneyValue($item, ['regular_deductions', 'total_regular_deductions'])),
                'salary_advances' => $this->formatMoney($this->moneyValue($item, ['salary_advances', 'total_salary_advances'])),
                'suspension_deductions' => $this->formatMoney($this->moneyValue($item, ['suspension_deductions', 'total_suspension_deductions'])),
                'total_deductions' => $this->formatMoney($this->moneyValue($item, ['total_deductions'])),
                'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function payslipRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'gross' => 'الإجمالي',
            'deductions' => 'الخصومات',
            'net' => 'الصافي',
            'status' => 'حالة القسيمة',
        ];

        $rows = $items->map(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);

            return [
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'employee_number' => $this->value($item, ['employee_number']),
                'employee_name' => $this->value($item, ['employee_name']),
                'gross' => $this->formatMoney($this->moneyValue($item, ['gross_salary', 'total_gross_salary'])),
                'deductions' => $this->formatMoney($this->moneyValue($item, ['total_deductions'])),
                'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
                'status' => 'جاهزة',
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function netSalaryRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'department' => 'القسم',
            'payment_method' => 'طريقة الدفع',
            'bank' => 'البنك',
            'iban' => 'IBAN',
            'net' => 'صافي الراتب',
        ];

        $rows = $items->map(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);

            return [
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'employee_number' => $this->value($item, ['employee_number']),
                'employee_name' => $this->value($item, ['employee_name']),
                'department' => $this->departmentName($item),
                'payment_method' => $this->paymentMethodName($item),
                'bank' => $this->bankName($item),
                'iban' => $this->iban($item),
                'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function grossSalaryRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'basic' => 'الراتب الأساسي',
            'allowances' => 'البدلات',
            'gross' => 'إجمالي الراتب',
        ];

        $rows = $items->map(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);

            return [
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'employee_number' => $this->value($item, ['employee_number']),
                'employee_name' => $this->value($item, ['employee_name']),
                'basic' => $this->formatMoney($this->moneyValue($item, ['basic_salary'])),
                'allowances' => $this->formatMoney($this->moneyValue($item, ['total_allowances', 'allowances'])),
                'gross' => $this->formatMoney($this->moneyValue($item, ['gross_salary', 'total_gross_salary'])),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function deductionRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'regular_deductions' => 'خصومات عادية',
            'salary_advances' => 'السلف',
            'suspension_deductions' => 'إيقافات',
            'total_deductions' => 'إجمالي الخصومات',
        ];

        $rows = $items->map(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);

            return [
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'employee_number' => $this->value($item, ['employee_number']),
                'employee_name' => $this->value($item, ['employee_name']),
                'regular_deductions' => $this->formatMoney($this->moneyValue($item, ['regular_deductions', 'total_regular_deductions'])),
                'salary_advances' => $this->formatMoney($this->moneyValue($item, ['salary_advances', 'total_salary_advances'])),
                'suspension_deductions' => $this->formatMoney($this->moneyValue($item, ['suspension_deductions', 'total_suspension_deductions'])),
                'total_deductions' => $this->formatMoney($this->moneyValue($item, ['total_deductions'])),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function salaryAdvanceRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'salary_advances' => 'مبلغ السلفة المخصوم',
            'net' => 'صافي الراتب',
        ];

        $rows = $items->filter(fn($item) => $this->moneyValue($item, ['salary_advances', 'total_salary_advances']) > 0)
            ->map(function ($item) use ($periodsById) {
                $period = $periodsById->get($item->payroll_period_id);

                return [
                    'period_number' => $period?->period_number ?? '-',
                    'month' => $period?->month ?? '-',
                    'employee_number' => $this->value($item, ['employee_number']),
                    'employee_name' => $this->value($item, ['employee_name']),
                    'salary_advances' => $this->formatMoney($this->moneyValue($item, ['salary_advances', 'total_salary_advances'])),
                    'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function costCenterRows($items): array
    {
        $columns = [
            'cost_center' => 'مركز التكلفة',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $rows = $items->groupBy(fn($item) => $this->value($item, ['cost_center_name', 'payroll_cost_center_name', 'cost_center'], 'بدون مركز تكلفة'))
            ->map(function ($group, $key) {
                return [
                    'cost_center' => $key,
                    'employees_count' => $group->count(),
                    'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                    'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                    'net' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))),
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function departmentPayrollRows($items): array
    {
        $columns = [
            'department' => 'القسم',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $rows = $items->groupBy(fn($item) => $this->departmentName($item))
            ->map(function ($group, $key) {
                return [
                    'department' => $key,
                    'employees_count' => $group->count(),
                    'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                    'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                    'net' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))),
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function payrollStatusRows($periods): array
    {
        $columns = [
            'status' => 'حالة المسير',
            'periods_count' => 'عدد المسيرات',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $rows = $periods->groupBy('status')->map(function ($group, $status) {
            $statusText = match ($status) {
                'draft' => 'مسودة',
                'calculated' => 'محسوب',
                'approved' => 'معتمد',
                'paid' => 'مدفوع',
                'cancelled' => 'ملغي',
                default => $status ?: '-',
            };

            return [
                'status' => $statusText,
                'periods_count' => $group->count(),
                'employees_count' => $group->sum('employees_count'),
                'gross' => $this->formatMoney($group->sum('total_gross_salary')),
                'deductions' => $this->formatMoney($group->sum('total_deductions')),
                'net' => $this->formatMoney($group->sum('total_net_salary')),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function bankTransferRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'bank' => 'البنك',
            'iban' => 'IBAN',
            'payment_method' => 'طريقة الصرف',
            'net' => 'صافي التحويل',
            'validation' => 'التحقق',
        ];

        $rows = $items->filter(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']) > 0)
            ->map(function ($item) use ($periodsById) {
                $period = $periodsById->get($item->payroll_period_id);
                $bank = $this->bankName($item);
                $iban = $this->iban($item);
                $ready = $bank !== '-' && $iban !== '-';

                return [
                    'period_number' => $period?->period_number ?? '-',
                    'month' => $period?->month ?? '-',
                    'employee_number' => $this->value($item, ['employee_number']),
                    'employee_name' => $this->value($item, ['employee_name']),
                    'bank' => $bank,
                    'iban' => $iban,
                    'payment_method' => $this->paymentMethodName($item),
                    'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
                    'validation' => $ready ? 'جاهز' : 'ناقص بيانات بنكية',
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function bankTransferBatchRows($periodIds): array
    {
        $columns = [
            'batch_number' => 'رقم الدفعة',
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'status' => 'الحالة',
            'employees_count' => 'عدد الموظفين',
            'total_amount' => 'إجمالي التحويل',
            'missing_bank_data_count' => 'نواقص بنكية',
            'bank_reference' => 'مرجع البنك',
            'confirmed_at' => 'تاريخ التأكيد',
        ];

        if (!Schema::hasTable('payroll_bank_transfer_batches')) {
            return [$columns, []];
        }

        $query = PayrollBankTransferBatch::query()->with('payrollPeriod');

        if ($periodIds->isNotEmpty()) {
            $query->whereIn('payroll_period_id', $periodIds);
        }

        $rows = $query->latest()->get()->map(function ($batch) {
            return [
                'batch_number' => $batch->batch_number,
                'period_number' => $batch->payrollPeriod?->period_number ?? '-',
                'month' => $batch->payrollPeriod?->month ?? '-',
                'status' => $batch->status_text,
                'employees_count' => $batch->employees_count,
                'total_amount' => $this->formatMoney($batch->total_amount),
                'missing_bank_data_count' => $batch->missing_bank_data_count,
                'bank_reference' => $batch->bank_reference ?: '-',
                'confirmed_at' => optional($batch->confirmed_at)->format('Y-m-d H:i') ?? '-',
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function missingBankDataRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'department' => 'القسم',
            'bank' => 'البنك',
            'iban' => 'IBAN',
            'missing' => 'النقص',
        ];

        $rows = $items->filter(fn($item) => $this->bankName($item) === '-' || $this->iban($item) === '-')
            ->map(function ($item) use ($periodsById) {
                $period = $periodsById->get($item->payroll_period_id);
                $missing = [];

                if ($this->bankName($item) === '-') {
                    $missing[] = 'اسم البنك';
                }

                if ($this->iban($item) === '-') {
                    $missing[] = 'IBAN';
                }

                return [
                    'period_number' => $period?->period_number ?? '-',
                    'month' => $period?->month ?? '-',
                    'employee_number' => $this->value($item, ['employee_number']),
                    'employee_name' => $this->value($item, ['employee_name']),
                    'department' => $this->departmentName($item),
                    'bank' => $this->bankName($item),
                    'iban' => $this->iban($item),
                    'missing' => implode('، ', $missing),
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function payrollPeriodLogRows($periodIds): array
    {
        $columns = [
            'created_at' => 'التاريخ',
            'period_number' => 'رقم المسير',
            'action' => 'العملية',
            'status_from' => 'من حالة',
            'status_to' => 'إلى حالة',
            'user' => 'بواسطة',
            'description' => 'الوصف',
        ];

        if (!Schema::hasTable('payroll_period_logs')) {
            return [$columns, []];
        }

        $query = PayrollPeriodLog::query()->with(['payrollPeriod', 'user']);

        if ($periodIds->isNotEmpty()) {
            $query->whereIn('payroll_period_id', $periodIds);
        }

        $rows = $query->latest()->get()->map(function ($log) {
            return [
                'created_at' => optional($log->created_at)->format('Y-m-d H:i'),
                'period_number' => $log->payrollPeriod?->period_number ?? '-',
                'action' => $log->action_text ?? $log->action,
                'status_from' => $log->status_from ?: '-',
                'status_to' => $log->status_to ?: '-',
                'user' => $log->user?->name ?? '-',
                'description' => $log->description ?: '-',
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function bankTransferBatchLogRows(): array
    {
        $columns = [
            'created_at' => 'التاريخ',
            'batch_number' => 'رقم الدفعة',
            'action' => 'العملية',
            'status_from' => 'من حالة',
            'status_to' => 'إلى حالة',
            'user' => 'بواسطة',
            'description' => 'الوصف',
        ];

        if (!Schema::hasTable('payroll_bank_transfer_batch_logs')) {
            return [$columns, []];
        }

        $rows = PayrollBankTransferBatchLog::query()
            ->with(['batch', 'user'])
            ->latest()
            ->get()
            ->map(function ($log) {
                return [
                    'created_at' => optional($log->created_at)->format('Y-m-d H:i'),
                    'batch_number' => $log->batch?->batch_number ?? '-',
                    'action' => $log->action_text ?? $log->action,
                    'status_from' => $log->status_from_text ?? ($log->status_from ?: '-'),
                    'status_to' => $log->status_to_text ?? ($log->status_to ?: '-'),
                    'user' => $log->user?->name ?? '-',
                    'description' => $log->description ?: '-',
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    /*
     |--------------------------------------------------------------------------
     | Phase 3 Analytical Reports
     |--------------------------------------------------------------------------
     */

    private function payrollComparisonRows(Request $request): array
    {
        $columns = [
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'base_period' => 'المسير الأول',
            'compare_period' => 'مسير المقارنة',
            'base_net' => 'صافي الأول',
            'compare_net' => 'صافي المقارنة',
            'difference' => 'الفرق',
            'change_status' => 'الحالة',
        ];

        if (!$request->filled('payroll_period_id') || !$request->filled('compare_payroll_period_id')) {
            return [$columns, []];
        }

        $basePeriod = PayrollPeriod::find($request->payroll_period_id);
        $comparePeriod = PayrollPeriod::find($request->compare_payroll_period_id);

        if (!$basePeriod || !$comparePeriod) {
            return [$columns, []];
        }

        $baseItems = PayrollItem::query()
            ->where('payroll_period_id', $basePeriod->id)
            ->get()
            ->keyBy('employee_number');

        $compareItems = PayrollItem::query()
            ->where('payroll_period_id', $comparePeriod->id)
            ->get()
            ->keyBy('employee_number');

        $employeeNumbers = $baseItems->keys()->merge($compareItems->keys())->unique();

        $rows = $employeeNumbers->map(function ($employeeNumber) use ($baseItems, $compareItems, $basePeriod, $comparePeriod) {
            $baseItem = $baseItems->get($employeeNumber);
            $compareItem = $compareItems->get($employeeNumber);

            $baseNet = $baseItem ? $this->moneyValue($baseItem, ['net_salary', 'total_net_salary']) : 0;
            $compareNet = $compareItem ? $this->moneyValue($compareItem, ['net_salary', 'total_net_salary']) : 0;
            $diff = $compareNet - $baseNet;

            return [
                'employee_number' => $employeeNumber,
                'employee_name' => $this->value($compareItem ?? $baseItem, ['employee_name']),
                'base_period' => $basePeriod->period_number,
                'compare_period' => $comparePeriod->period_number,
                'base_net' => $this->formatMoney($baseNet),
                'compare_net' => $this->formatMoney($compareNet),
                'difference' => $this->formatMoney($diff),
                'change_status' => $diff > 0 ? 'زيادة' : ($diff < 0 ? 'نقص' : 'بدون تغيير'),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function netSalaryChangesRows(Request $request): array
    {
        [$columns, $rows] = $this->payrollComparisonRows($request);

        $rows = collect($rows)->filter(function ($row) {
            return (float) str_replace(',', '', $row['difference']) != 0.0;
        })->values()->all();

        return [$columns, $rows];
    }

    private function employeesWithoutPayrollRows(Request $request): array
    {
        $columns = [
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'department' => 'القسم',
            'status' => 'حالة الموظف',
            'reason' => 'الملاحظة',
        ];

        if (!$request->filled('payroll_period_id')) {
            return [$columns, []];
        }

        $periodId = $request->payroll_period_id;

        $includedEmployeeIds = PayrollItem::query()
            ->where('payroll_period_id', $periodId)
            ->whereNotNull('employee_id')
            ->pluck('employee_id')
            ->unique();

        $employeesQuery = Employee::query()->with(['department']);

        if ($includedEmployeeIds->isNotEmpty()) {
            $employeesQuery->whereNotIn('id', $includedEmployeeIds);
        }

        if (Schema::hasColumn('employees', 'status')) {
            $employeesQuery->where(function ($q) {
                $q->where('status', 'active')
                    ->orWhereNull('status');
            });
        }

        $rows = $employeesQuery->orderBy('full_name')->get()->map(function ($employee) {
            return [
                'employee_number' => $employee->employee_number ?? '-',
                'employee_name' => $employee->display_name ?? $employee->full_name ?? $employee->name ?? '-',
                'department' => $employee->department?->name_ar ?? $employee->department?->name ?? '-',
                'status' => $employee->status ?? '-',
                'reason' => 'غير موجود في المسير المحدد',
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function employeeStatusPayrollRows($items): array
    {
        $columns = [
            'employee_status' => 'حالة الموظف',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $rows = $items->groupBy(function ($item) {
            return $item->employee?->status ?? 'غير محدد';
        })->map(function ($group, $status) {
            return [
                'employee_status' => $status,
                'employees_count' => $group->count(),
                'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                'net' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function nationalityPayrollRows($items): array
    {
        $columns = [
            'nationality' => 'الجنسية',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $rows = $items->groupBy(function ($item) {
            return $item->employee?->nationality ?? $item->employee?->nationality_name ?? 'غير محدد';
        })->map(function ($group, $nationality) {
            return [
                'nationality' => $nationality,
                'employees_count' => $group->count(),
                'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                'net' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function payableDaysRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'payable_days' => 'أيام الاستحقاق',
            'absent_days' => 'أيام الغياب',
            'unpaid_leave_days' => 'إجازة غير مدفوعة',
            'net' => 'صافي الراتب',
        ];

        $rows = $items->map(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);

            return [
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'employee_number' => $this->value($item, ['employee_number']),
                'employee_name' => $this->value($item, ['employee_name']),
                'payable_days' => $this->formatNumber($this->moneyValue($item, ['payable_days', 'working_days', 'paid_days'])),
                'absent_days' => $this->formatNumber($this->moneyValue($item, ['absent_days', 'absence_days'])),
                'unpaid_leave_days' => $this->formatNumber($this->moneyValue($item, ['unpaid_leave_days'])),
                'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function paidArchiveRows(Request $request): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'paid_at' => 'تاريخ الصرف',
            'paid_by' => 'تم الصرف بواسطة',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $query = PayrollPeriod::query()
            ->with('paidBy')
            ->where('status', 'paid');

        if ($request->filled('payroll_period_id')) {
            $query->where('id', $request->payroll_period_id);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        $rows = $query->orderByDesc('paid_at')->get()->map(function ($period) {
            return [
                'period_number' => $period->period_number,
                'month' => $period->month,
                'paid_at' => optional($period->paid_at)->format('Y-m-d H:i') ?? '-',
                'paid_by' => $period->paidBy?->name ?? '-',
                'employees_count' => $period->employees_count,
                'gross' => $this->formatMoney($period->total_gross_salary),
                'deductions' => $this->formatMoney($period->total_deductions),
                'net' => $this->formatMoney($period->total_net_salary),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function payrollWarningsRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'warning_type' => 'نوع التحذير',
            'description' => 'الوصف',
        ];

        $rows = [];

        foreach ($items as $item) {
            $period = $periodsById->get($item->payroll_period_id);
            $base = [
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'employee_number' => $this->value($item, ['employee_number']),
                'employee_name' => $this->value($item, ['employee_name']),
            ];

            if ($this->moneyValue($item, ['net_salary', 'total_net_salary']) < 0) {
                $rows[] = array_merge($base, [
                    'warning_type' => 'صافي راتب سالب',
                    'description' => 'صافي الراتب أقل من صفر، يجب مراجعة الخصومات.',
                ]);
            }

            if ($this->bankName($item) === '-' || $this->iban($item) === '-') {
                $rows[] = array_merge($base, [
                    'warning_type' => 'بيانات بنكية ناقصة',
                    'description' => 'اسم البنك أو IBAN غير مكتمل.',
                ]);
            }

            if ($this->moneyValue($item, ['gross_salary', 'total_gross_salary']) <= 0) {
                $rows[] = array_merge($base, [
                    'warning_type' => 'إجمالي راتب غير صحيح',
                    'description' => 'إجمالي الراتب صفر أو أقل.',
                ]);
            }
        }

        return [$columns, $rows];
    }


    /*
     |--------------------------------------------------------------------------
     | Phase 4 Detailed Reports
     |--------------------------------------------------------------------------
     */

    private function payrollComponentRows($periodIds, $periodsById, ?array $typeFilter = null): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'component_name' => 'اسم المكون',
            'component_type' => 'نوع المكون',
            'amount' => 'المبلغ',
            'notes' => 'ملاحظات',
        ];

        if (!Schema::hasTable('payroll_item_components')) {
            return [$columns, []];
        }

        $query = PayrollItemComponent::query();

        if ($periodIds->isNotEmpty() && Schema::hasColumn('payroll_item_components', 'payroll_period_id')) {
            $query->whereIn('payroll_period_id', $periodIds);
        }

        if ($typeFilter && Schema::hasColumn('payroll_item_components', 'component_type')) {
            $query->where(function ($q) use ($typeFilter) {
                foreach ($typeFilter as $type) {
                    $q->orWhere('component_type', 'like', '%' . $type . '%');
                }
            });
        } elseif ($typeFilter && Schema::hasColumn('payroll_item_components', 'type')) {
            $query->where(function ($q) use ($typeFilter) {
                foreach ($typeFilter as $type) {
                    $q->orWhere('type', 'like', '%' . $type . '%');
                }
            });
        }

        $components = $query->latest('id')->get();

        $payrollItemsById = collect();

        if (Schema::hasColumn('payroll_item_components', 'payroll_item_id')) {
            $payrollItemIds = $components->pluck('payroll_item_id')->filter()->unique();

            if ($payrollItemIds->isNotEmpty()) {
                $payrollItemsById = PayrollItem::query()
                    ->whereIn('id', $payrollItemIds)
                    ->get()
                    ->keyBy('id');
            }
        }

        $rows = $components->map(function ($component) use ($periodsById, $payrollItemsById) {
            $item = $payrollItemsById->get($component->payroll_item_id ?? null);
            $periodId = $component->payroll_period_id ?? $item?->payroll_period_id;
            $period = $periodsById->get($periodId);

            $type = $this->value($component, ['component_type', 'type', 'category'], '-');

            return [
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'employee_number' => $this->value($component, ['employee_number'], $item?->employee_number ?? '-'),
                'employee_name' => $this->value($component, ['employee_name'], $item?->employee_name ?? '-'),
                'component_name' => $this->value($component, ['component_name', 'name_ar', 'name', 'title', 'label']),
                'component_type' => $this->componentTypeText($type),
                'amount' => $this->formatMoney($this->moneyValue($component, ['amount', 'value', 'calculated_amount', 'total_amount'])),
                'notes' => $this->value($component, ['notes', 'description'], '-'),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function employeePayrollHistoryRows($items, $periodsById): array
    {
        $columns = [
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'gross' => 'إجمالي الراتب',
            'deductions' => 'الخصومات',
            'net' => 'الصافي',
            'status' => 'حالة المسير',
        ];

        $rows = $items->sortBy([
            ['employee_number', 'asc'],
            ['payroll_period_id', 'desc'],
        ])->map(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);

            return [
                'employee_number' => $this->value($item, ['employee_number']),
                'employee_name' => $this->value($item, ['employee_name']),
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'gross' => $this->formatMoney($this->moneyValue($item, ['gross_salary', 'total_gross_salary'])),
                'deductions' => $this->formatMoney($this->moneyValue($item, ['total_deductions'])),
                'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
                'status' => $period?->status_text ?? '-',
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function monthlyPayrollTrendRows($periods): array
    {
        $columns = [
            'month' => 'الشهر',
            'periods_count' => 'عدد المسيرات',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
            'average_net' => 'متوسط الصافي',
        ];

        $rows = $periods->groupBy('month')->map(function ($group, $month) {
            $employeesCount = (int) $group->sum('employees_count');
            $net = (float) $group->sum('total_net_salary');

            return [
                'month' => $month ?: '-',
                'periods_count' => $group->count(),
                'employees_count' => $employeesCount,
                'gross' => $this->formatMoney($group->sum('total_gross_salary')),
                'deductions' => $this->formatMoney($group->sum('total_deductions')),
                'net' => $this->formatMoney($net),
                'average_net' => $this->formatMoney($employeesCount > 0 ? $net / $employeesCount : 0),
            ];
        })->sortBy('month')->values()->all();

        return [$columns, $rows];
    }

    private function topNetSalaryRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'department' => 'القسم',
            'gross' => 'الإجمالي',
            'deductions' => 'الخصومات',
            'net' => 'الصافي',
        ];

        $rows = $items->sortByDesc(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))
            ->take(50)
            ->map(function ($item) use ($periodsById) {
                $period = $periodsById->get($item->payroll_period_id);

                return [
                    'period_number' => $period?->period_number ?? '-',
                    'month' => $period?->month ?? '-',
                    'employee_number' => $this->value($item, ['employee_number']),
                    'employee_name' => $this->value($item, ['employee_name']),
                    'department' => $this->departmentName($item),
                    'gross' => $this->formatMoney($this->moneyValue($item, ['gross_salary', 'total_gross_salary'])),
                    'deductions' => $this->formatMoney($this->moneyValue($item, ['total_deductions'])),
                    'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function zeroNegativeNetSalaryRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'gross' => 'الإجمالي',
            'deductions' => 'الخصومات',
            'net' => 'الصافي',
            'status' => 'التصنيف',
        ];

        $rows = $items->filter(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']) <= 0)
            ->map(function ($item) use ($periodsById) {
                $period = $periodsById->get($item->payroll_period_id);
                $net = $this->moneyValue($item, ['net_salary', 'total_net_salary']);

                return [
                    'period_number' => $period?->period_number ?? '-',
                    'month' => $period?->month ?? '-',
                    'employee_number' => $this->value($item, ['employee_number']),
                    'employee_name' => $this->value($item, ['employee_name']),
                    'gross' => $this->formatMoney($this->moneyValue($item, ['gross_salary', 'total_gross_salary'])),
                    'deductions' => $this->formatMoney($this->moneyValue($item, ['total_deductions'])),
                    'net' => $this->formatMoney($net),
                    'status' => $net < 0 ? 'سالب' : 'صفر',
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function paymentMethodsSummaryRows($items): array
    {
        $columns = [
            'payment_method' => 'طريقة الدفع',
            'bank' => 'البنك',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $rows = $items->groupBy(function ($item) {
            return $this->paymentMethodName($item) . '|' . $this->bankName($item);
        })->map(function ($group, $key) {
            [$method, $bank] = array_pad(explode('|', $key, 2), 2, '-');

            return [
                'payment_method' => $method ?: '-',
                'bank' => $bank ?: '-',
                'employees_count' => $group->count(),
                'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                'net' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function componentTypeText(string $type): string
    {
        $lower = mb_strtolower($type);

        if (str_contains($lower, 'allowance') || str_contains($lower, 'بدل')) {
            return 'بدل';
        }

        if (str_contains($lower, 'deduction') || str_contains($lower, 'خصم')) {
            return 'خصم';
        }

        if (str_contains($lower, 'earning') || str_contains($lower, 'استحقاق')) {
            return 'استحقاق';
        }

        return $type ?: '-';
    }


    /*
     |--------------------------------------------------------------------------
     | Phase 5 Management Summary Reports
     |--------------------------------------------------------------------------
     */

    private function payrollGroupsSummaryRows($items): array
    {
        $columns = [
            'payroll_group' => 'مجموعة الرواتب',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
            'average_net' => 'متوسط الصافي',
        ];

        $rows = $items->groupBy(function ($item) {
            return $this->value($item, ['payroll_group_name', 'group_name', 'salary_group_name'], 'بدون مجموعة');
        })->map(function ($group, $key) {
            $count = $group->count();
            $net = $group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']));

            return [
                'payroll_group' => $key,
                'employees_count' => $count,
                'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                'net' => $this->formatMoney($net),
                'average_net' => $this->formatMoney($count > 0 ? $net / $count : 0),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function positionPayrollRows($items): array
    {
        $columns = [
            'position' => 'المسمى الوظيفي',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
            'average_net' => 'متوسط الصافي',
        ];

        $rows = $items->groupBy(fn($item) => $this->positionName($item))
            ->map(function ($group, $position) {
                $count = $group->count();
                $net = $group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']));

                return [
                    'position' => $position,
                    'employees_count' => $count,
                    'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                    'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                    'net' => $this->formatMoney($net),
                    'average_net' => $this->formatMoney($count > 0 ? $net / $count : 0),
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function salaryRangeDistributionRows($items): array
    {
        $columns = [
            'range' => 'شريحة صافي الراتب',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $rows = $items->groupBy(function ($item) {
            $net = $this->moneyValue($item, ['net_salary', 'total_net_salary']);

            if ($net <= 0) {
                return 'صفر أو سالب';
            }

            if ($net <= 3000) {
                return '1 - 3,000';
            }

            if ($net <= 5000) {
                return '3,001 - 5,000';
            }

            if ($net <= 10000) {
                return '5,001 - 10,000';
            }

            if ($net <= 20000) {
                return '10,001 - 20,000';
            }

            return 'أكثر من 20,000';
        })->map(function ($group, $range) {
            return [
                'range' => $range,
                'employees_count' => $group->count(),
                'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                'net' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function componentSummaryRows($periodIds, $periodsById, ?array $typeFilter = null): array
    {
        $columns = [
            'component_name' => 'اسم المكون',
            'component_type' => 'نوع المكون',
            'records_count' => 'عدد السجلات',
            'employees_count' => 'عدد الموظفين',
            'total_amount' => 'إجمالي المبلغ',
            'average_amount' => 'متوسط المبلغ',
        ];

        if (!Schema::hasTable('payroll_item_components')) {
            return [$columns, []];
        }

        $query = PayrollItemComponent::query();

        if ($periodIds->isNotEmpty() && Schema::hasColumn('payroll_item_components', 'payroll_period_id')) {
            $query->whereIn('payroll_period_id', $periodIds);
        }

        if ($typeFilter && Schema::hasColumn('payroll_item_components', 'component_type')) {
            $query->where(function ($q) use ($typeFilter) {
                foreach ($typeFilter as $type) {
                    $q->orWhere('component_type', 'like', '%' . $type . '%');
                }
            });
        } elseif ($typeFilter && Schema::hasColumn('payroll_item_components', 'type')) {
            $query->where(function ($q) use ($typeFilter) {
                foreach ($typeFilter as $type) {
                    $q->orWhere('type', 'like', '%' . $type . '%');
                }
            });
        }

        $components = $query->get();

        $rows = $components->groupBy(function ($component) {
            $name = $this->value($component, ['component_name', 'name_ar', 'name', 'title', 'label']);
            $type = $this->value($component, ['component_type', 'type', 'category'], '-');

            return $name . '|' . $type;
        })->map(function ($group, $key) {
            [$name, $type] = array_pad(explode('|', $key, 2), 2, '-');
            $total = $group->sum(fn($component) => $this->moneyValue($component, ['amount', 'value', 'calculated_amount', 'total_amount']));
            $count = $group->count();

            return [
                'component_name' => $name ?: '-',
                'component_type' => $this->componentTypeText($type),
                'records_count' => $count,
                'employees_count' => $group->pluck('employee_id')->filter()->unique()->count() ?: '-',
                'total_amount' => $this->formatMoney($total),
                'average_amount' => $this->formatMoney($count > 0 ? $total / $count : 0),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function bankSummaryRows($items): array
    {
        $columns = [
            'bank' => 'البنك',
            'employees_count' => 'عدد الموظفين',
            'total_net' => 'إجمالي صافي التحويل',
            'missing_iban_count' => 'عدد IBAN ناقص',
            'payment_methods' => 'طرق الدفع',
        ];

        $rows = $items->groupBy(fn($item) => $this->bankName($item))
            ->map(function ($group, $bank) {
                return [
                    'bank' => $bank,
                    'employees_count' => $group->count(),
                    'total_net' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))),
                    'missing_iban_count' => $group->filter(fn($item) => $this->iban($item) === '-')->count(),
                    'payment_methods' => $group->map(fn($item) => $this->paymentMethodName($item))->unique()->implode('، '),
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function employeeCostSummaryRows($items): array
    {
        $columns = [
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'periods_count' => 'عدد المسيرات',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
            'average_net' => 'متوسط الصافي',
        ];

        $rows = $items->groupBy('employee_number')->map(function ($group, $employeeNumber) {
            $periodsCount = $group->pluck('payroll_period_id')->unique()->count();
            $net = $group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']));

            return [
                'employee_number' => $employeeNumber ?: '-',
                'employee_name' => $this->value($group->first(), ['employee_name']),
                'periods_count' => $periodsCount,
                'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                'net' => $this->formatMoney($net),
                'average_net' => $this->formatMoney($periodsCount > 0 ? $net / $periodsCount : 0),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function newlyHiredPayrollRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'hire_date' => 'تاريخ التوظيف',
            'department' => 'القسم',
            'net' => 'صافي الراتب',
            'note' => 'ملاحظة',
        ];

        $rows = $items->filter(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);
            $hireDate = $this->employeeHireDate($item);

            if (!$period || !$hireDate || !$period->start_date || !$period->end_date) {
                return false;
            }

            return $hireDate >= optional($period->start_date)->format('Y-m-d')
                && $hireDate <= optional($period->end_date)->format('Y-m-d');
        })->map(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);

            return [
                'period_number' => $period?->period_number ?? '-',
                'month' => $period?->month ?? '-',
                'employee_number' => $this->value($item, ['employee_number']),
                'employee_name' => $this->value($item, ['employee_name']),
                'hire_date' => $this->employeeHireDate($item) ?: '-',
                'department' => $this->departmentName($item),
                'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
                'note' => 'تاريخ التوظيف داخل فترة المسير',
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function employeeHireDate($item): ?string
    {
        $hireDate = $item->employee?->hire_date
            ?? $item->employee?->hiring_date
            ?? $item->employee?->joining_date
            ?? null;

        if (!$hireDate) {
            return null;
        }

        if ($hireDate instanceof \Carbon\Carbon) {
            return $hireDate->format('Y-m-d');
        }

        $timestamp = strtotime((string) $hireDate);

        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }


    /*
     |--------------------------------------------------------------------------
     | Phase 6 Monthly and Annual Reports
     |--------------------------------------------------------------------------
     */

    private function annualPayrollSummaryRows($periods): array
    {
        $columns = [
            'year' => 'السنة',
            'periods_count' => 'عدد المسيرات',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'regular_deductions' => 'خصومات عادية',
            'salary_advances' => 'السلف',
            'suspension_deductions' => 'الإيقافات',
            'total_deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
            'average_net' => 'متوسط الصافي',
        ];

        $rows = $periods->groupBy(fn($period) => $this->periodYear($period))
            ->map(function ($group, $year) {
                $employeesCount = (int) $group->sum('employees_count');
                $net = (float) $group->sum('total_net_salary');

                return [
                    'year' => $year,
                    'periods_count' => $group->count(),
                    'employees_count' => $employeesCount,
                    'gross' => $this->formatMoney($group->sum('total_gross_salary')),
                    'regular_deductions' => $this->formatMoney($group->sum('total_regular_deductions')),
                    'salary_advances' => $this->formatMoney($group->sum('total_salary_advances')),
                    'suspension_deductions' => $this->formatMoney($group->sum('total_suspension_deductions')),
                    'total_deductions' => $this->formatMoney($group->sum('total_deductions')),
                    'net' => $this->formatMoney($net),
                    'average_net' => $this->formatMoney($employeesCount > 0 ? $net / $employeesCount : 0),
                ];
            })->sortByDesc('year')->values()->all();

        return [$columns, $rows];
    }

    private function employeeAnnualSummaryRows($items, $periodsById): array
    {
        $columns = [
            'year' => 'السنة',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'periods_count' => 'عدد المسيرات',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
            'average_net' => 'متوسط الصافي',
        ];

        $rows = $items->groupBy(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);
            return $this->periodYear($period) . '|' . $this->value($item, ['employee_number']);
        })->map(function ($group, $key) {
            [$year, $employeeNumber] = array_pad(explode('|', $key, 2), 2, '-');
            $periodsCount = $group->pluck('payroll_period_id')->unique()->count();
            $net = $group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']));

            return [
                'year' => $year,
                'employee_number' => $employeeNumber,
                'employee_name' => $this->value($group->first(), ['employee_name']),
                'periods_count' => $periodsCount,
                'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                'net' => $this->formatMoney($net),
                'average_net' => $this->formatMoney($periodsCount > 0 ? $net / $periodsCount : 0),
            ];
        })->sortByDesc('year')->values()->all();

        return [$columns, $rows];
    }

    private function departmentMonthlyTrendRows($items, $periodsById): array
    {
        $columns = [
            'month' => 'الشهر',
            'department' => 'القسم',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
            'average_net' => 'متوسط الصافي',
        ];

        $rows = $items->groupBy(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);
            return ($period?->month ?? '-') . '|' . $this->departmentName($item);
        })->map(function ($group, $key) {
            [$month, $department] = array_pad(explode('|', $key, 2), 2, '-');
            $count = $group->count();
            $net = $group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']));

            return [
                'month' => $month,
                'department' => $department,
                'employees_count' => $count,
                'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                'net' => $this->formatMoney($net),
                'average_net' => $this->formatMoney($count > 0 ? $net / $count : 0),
            ];
        })->sortBy('month')->values()->all();

        return [$columns, $rows];
    }

    private function costCenterMonthlyTrendRows($items, $periodsById): array
    {
        $columns = [
            'month' => 'الشهر',
            'cost_center' => 'مركز التكلفة',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الرواتب',
        ];

        $rows = $items->groupBy(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);
            $costCenter = $this->value($item, ['cost_center_name', 'payroll_cost_center_name', 'cost_center'], 'بدون مركز تكلفة');
            return ($period?->month ?? '-') . '|' . $costCenter;
        })->map(function ($group, $key) {
            [$month, $costCenter] = array_pad(explode('|', $key, 2), 2, '-');

            return [
                'month' => $month,
                'cost_center' => $costCenter,
                'employees_count' => $group->count(),
                'gross' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['gross_salary', 'total_gross_salary']))),
                'deductions' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['total_deductions']))),
                'net' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))),
            ];
        })->sortBy('month')->values()->all();

        return [$columns, $rows];
    }

    private function bankMonthlyTrendRows($items, $periodsById): array
    {
        $columns = [
            'month' => 'الشهر',
            'bank' => 'البنك',
            'payment_method' => 'طريقة الدفع',
            'employees_count' => 'عدد الموظفين',
            'total_transfer' => 'إجمالي التحويل',
            'missing_iban_count' => 'IBAN ناقص',
        ];

        $rows = $items->groupBy(function ($item) use ($periodsById) {
            $period = $periodsById->get($item->payroll_period_id);
            return ($period?->month ?? '-') . '|' . $this->bankName($item) . '|' . $this->paymentMethodName($item);
        })->map(function ($group, $key) {
            [$month, $bank, $paymentMethod] = array_pad(explode('|', $key, 3), 3, '-');

            return [
                'month' => $month,
                'bank' => $bank,
                'payment_method' => $paymentMethod,
                'employees_count' => $group->count(),
                'total_transfer' => $this->formatMoney($group->sum(fn($item) => $this->moneyValue($item, ['net_salary', 'total_net_salary']))),
                'missing_iban_count' => $group->filter(fn($item) => $this->iban($item) === '-')->count(),
            ];
        })->sortBy('month')->values()->all();

        return [$columns, $rows];
    }

    private function highestDeductionsRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'department' => 'القسم',
            'gross' => 'إجمالي الراتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'الصافي',
        ];

        $rows = $items->sortByDesc(fn($item) => $this->moneyValue($item, ['total_deductions']))
            ->take(50)
            ->map(function ($item) use ($periodsById) {
                $period = $periodsById->get($item->payroll_period_id);

                return [
                    'period_number' => $period?->period_number ?? '-',
                    'month' => $period?->month ?? '-',
                    'employee_number' => $this->value($item, ['employee_number']),
                    'employee_name' => $this->value($item, ['employee_name']),
                    'department' => $this->departmentName($item),
                    'gross' => $this->formatMoney($this->moneyValue($item, ['gross_salary', 'total_gross_salary'])),
                    'deductions' => $this->formatMoney($this->moneyValue($item, ['total_deductions'])),
                    'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function highestAdvancesRows($items, $periodsById): array
    {
        $columns = [
            'period_number' => 'رقم المسير',
            'month' => 'الشهر',
            'employee_number' => 'الرقم الوظيفي',
            'employee_name' => 'الموظف',
            'salary_advances' => 'السلف المخصومة',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'الصافي',
        ];

        $rows = $items->filter(fn($item) => $this->moneyValue($item, ['salary_advances', 'total_salary_advances']) > 0)
            ->sortByDesc(fn($item) => $this->moneyValue($item, ['salary_advances', 'total_salary_advances']))
            ->take(50)
            ->map(function ($item) use ($periodsById) {
                $period = $periodsById->get($item->payroll_period_id);

                return [
                    'period_number' => $period?->period_number ?? '-',
                    'month' => $period?->month ?? '-',
                    'employee_number' => $this->value($item, ['employee_number']),
                    'employee_name' => $this->value($item, ['employee_name']),
                    'salary_advances' => $this->formatMoney($this->moneyValue($item, ['salary_advances', 'total_salary_advances'])),
                    'deductions' => $this->formatMoney($this->moneyValue($item, ['total_deductions'])),
                    'net' => $this->formatMoney($this->moneyValue($item, ['net_salary', 'total_net_salary'])),
                ];
            })->values()->all();

        return [$columns, $rows];
    }

    private function payrollLiabilitySummaryRows($periods): array
    {
        $columns = [
            'liability_status' => 'تصنيف الالتزام',
            'periods_count' => 'عدد المسيرات',
            'employees_count' => 'عدد الموظفين',
            'gross' => 'إجمالي الرواتب',
            'deductions' => 'إجمالي الخصومات',
            'net' => 'صافي الالتزام',
        ];

        $rows = $periods->groupBy(function ($period) {
            return match ($period->status) {
                'paid' => 'مدفوع',
                'approved', 'calculated' => 'التزام قائم غير مدفوع',
                'draft' => 'مسودة غير معتمدة',
                'cancelled' => 'ملغي',
                default => $period->status ?: '-',
            };
        })->map(function ($group, $status) {
            return [
                'liability_status' => $status,
                'periods_count' => $group->count(),
                'employees_count' => $group->sum('employees_count'),
                'gross' => $this->formatMoney($group->sum('total_gross_salary')),
                'deductions' => $this->formatMoney($group->sum('total_deductions')),
                'net' => $this->formatMoney($group->sum('total_net_salary')),
            ];
        })->values()->all();

        return [$columns, $rows];
    }

    private function periodYear($period): string
    {
        if (!$period) {
            return '-';
        }

        if (!empty($period->month)) {
            return substr((string) $period->month, 0, 4);
        }

        if (!empty($period->start_date)) {
            return optional($period->start_date)->format('Y') ?: date('Y', strtotime((string) $period->start_date));
        }

        return '-';
    }

    /*
     |--------------------------------------------------------------------------
     | Helpers
     |--------------------------------------------------------------------------
     */

    private function value($model, array $keys, string $default = '-'): string
    {
        if (!$model) {
            return $default;
        }

        foreach ($keys as $key) {
            $value = $model->{$key} ?? null;

            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        return $default;
    }

    private function moneyValue($model, array $keys): float
    {
        if (!$model) {
            return 0.0;
        }

        foreach ($keys as $key) {
            $value = $model->{$key} ?? null;

            if ($value !== null && $value !== '') {
                return (float) $value;
            }
        }

        return 0.0;
    }

    private function departmentName($item): string
    {
        $direct = $this->value($item, ['employee_department', 'department_name'], '');

        return $direct !== ''
            ? $direct
            : ($item->employee?->department?->name_ar ?? $item->employee?->department?->name ?? '-');
    }

    private function positionName($item): string
    {
        $direct = $this->value($item, ['employee_position', 'position_name'], '');

        return $direct !== ''
            ? $direct
            : ($item->employee?->position?->title_ar ?? $item->employee?->position?->title ?? '-');
    }

    private function paymentMethodName($item): string
    {
        $direct = $this->value($item, ['salary_payment_method_name', 'salary_payment_method'], '');

        return $direct !== ''
            ? $direct
            : ($item->employee?->salaryPaymentMethod?->name_ar ?? $item->employee?->salaryPaymentMethod?->name ?? '-');
    }

    private function bankName($item): string
    {
        $direct = $this->value($item, ['bank_name'], '');

        return $direct !== ''
            ? $direct
            : ($item->employee?->bank_name ?: '-');
    }

    private function iban($item): string
    {
        $direct = $this->value($item, ['iban'], '');

        return $direct !== ''
            ? $direct
            : ($item->employee?->iban ?: '-');
    }

    private function formatMoney($value): string
    {
        return number_format((float) $value, 2);
    }

    private function formatNumber($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
    }
}
