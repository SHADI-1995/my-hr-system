<?php

namespace App\Http\Controllers;

use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;

class PayrollReportController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.view'), 403);

        $payrollSetting = PayrollSetting::current();

        $periodsQuery = PayrollPeriod::query()->latest();

        if ($request->month) {
            $periodsQuery->where('month', $request->month);
        }

        if ($request->status) {
            $periodsQuery->where('status', $request->status);
        }

        $periods = $periodsQuery->paginate(12)->withQueryString();

        $selectedPeriod = null;

        if ($request->filled('period_id')) {
            $selectedPeriod = PayrollPeriod::with($this->payrollReportRelations())
                ->find($request->period_id);
        }

        return view('payroll_reports.index', compact('periods', 'selectedPeriod', 'payrollSetting'));
    }

    public function show(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.view'), 403);

        $payrollSetting = PayrollSetting::current();

        $selectedPeriod = $payrollPeriod->load($this->payrollReportRelations());

        $periods = PayrollPeriod::query()
            ->latest()
            ->paginate(12);

        return view('payroll_reports.index', compact('periods', 'selectedPeriod', 'payrollSetting'));
    }

    public function exportExcel(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.export'), 403);

        $payrollSetting = PayrollSetting::current();

        $payrollPeriod->load($this->payrollReportRelations());

        $fileName = 'payroll-report-' . $payrollPeriod->month . '.xls';

        return response()
            ->view('payroll_reports.excel', compact('payrollPeriod', 'payrollSetting'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function printPdf(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.export'), 403);

        $payrollSetting = PayrollSetting::current();

        $payrollPeriod->load($this->payrollReportRelations());

        return view('payroll_reports.print_pdf', compact('payrollPeriod', 'payrollSetting'));
    }

    public function payslip(PayrollItem $payrollItem)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.payslip'), 403);

        $payrollSetting = PayrollSetting::current();

        $payrollItem->load($this->payslipRelations());

        return view('payroll_reports.payslip', compact('payrollItem', 'payrollSetting'));
    }

    private function payrollReportRelations(): array
    {
        return [
            'items.employee.department',
            'items.employee.position',
            'items.employee.nationality',
            'items.employee.salaryPaymentMethod',
            'items.employee.payrollGroup',
            'items.employee.costCenter',
            'items.components',
            'createdBy',
            'calculatedBy',
            'approvedBy',
            'paidBy',
        ];
    }

    private function payslipRelations(): array
    {
        return [
            'employee.department',
            'employee.position',
            'employee.nationality',
            'employee.salaryPaymentMethod',
            'employee.payrollGroup',
            'employee.costCenter',
            'payrollPeriod',
            'components',
        ];
    }
}
