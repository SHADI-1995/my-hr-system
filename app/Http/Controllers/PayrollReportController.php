<?php

namespace App\Http\Controllers;

use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;

class PayrollReportController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.view'), 403);

        $periodsQuery = PayrollPeriod::query()
            ->latest();

        if ($request->month) {
            $periodsQuery->where('month', $request->month);
        }

        if ($request->status) {
            $periodsQuery->where('status', $request->status);
        }

        $periods = $periodsQuery->paginate(12)->withQueryString();

        $selectedPeriod = null;

        if ($request->filled('period_id')) {
            $selectedPeriod = PayrollPeriod::with([
                'items.employee.department',
                'items.components',
                'createdBy',
                'calculatedBy',
                'approvedBy',
                'paidBy',
            ])->find($request->period_id);
        }

        return view('payroll_reports.index', compact('periods', 'selectedPeriod'));
    }

    public function show(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.view'), 403);

        $selectedPeriod = $payrollPeriod->load([
            'items.employee.department',
            'items.components',
            'createdBy',
            'calculatedBy',
            'approvedBy',
            'paidBy',
        ]);

        $periods = PayrollPeriod::query()
            ->latest()
            ->paginate(12);

        return view('payroll_reports.index', compact('periods', 'selectedPeriod'));
    }

    public function exportExcel(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.export'), 403);

        $payrollPeriod->load([
            'items.employee.department',
            'items.components',
        ]);

        $fileName = 'payroll-report-' . $payrollPeriod->month . '.xls';

        return response()
            ->view('payroll_reports.excel', compact('payrollPeriod'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function printPdf(PayrollPeriod $payrollPeriod)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.export'), 403);

        $payrollPeriod->load([
            'items.employee.department',
            'items.components',
            'createdBy',
            'calculatedBy',
            'approvedBy',
            'paidBy',
        ]);

        return view('payroll_reports.print_pdf', compact('payrollPeriod'));
    }

    public function payslip(PayrollItem $payrollItem)
    {
        abort_if(!auth()->user()->hasPermission('payroll_reports.payslip'), 403);

        $payrollItem->load([
            'employee.department',
            'employee.position',
            'payrollPeriod',
            'components',
        ]);

        return view('payroll_reports.payslip', compact('payrollItem'));
    }
}
