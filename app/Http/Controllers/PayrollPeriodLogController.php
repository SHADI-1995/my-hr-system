<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Models\PayrollPeriodLog;
use App\Models\User;
use Illuminate\Http\Request;

class PayrollPeriodLogController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_period_logs.view'), 403);

        $logs = $this->filteredQuery($request)
            ->latest()
            ->paginate(25)
            ->withQueryString();

        [$periods, $users, $actions] = $this->filterData();

        return view('payroll_period_logs.index', compact(
            'logs',
            'periods',
            'users',
            'actions'
        ));
    }

    public function exportExcel(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_period_logs.export'), 403);

        $logs = $this->filteredQuery($request)
            ->latest()
            ->get();

        $actions = $this->actions();

        $fileName = 'payroll_period_logs_' . now()->format('Y_m_d_H_i_s') . '.xls';

        return response()
            ->view('payroll_period_logs.excel', compact('logs', 'actions'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function printPdf(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('payroll_period_logs.export'), 403);

        $logs = $this->filteredQuery($request)
            ->latest()
            ->get();

        $actions = $this->actions();

        return view('payroll_period_logs.print_pdf', compact('logs', 'actions'));
    }

    private function filteredQuery(Request $request)
    {
        $query = PayrollPeriodLog::query()
            ->with(['payrollPeriod', 'user']);

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('payroll_period_id')) {
            $query->where('payroll_period_id', $request->payroll_period_id);
        }

        if ($request->filled('period_number')) {
            $periodIds = PayrollPeriod::query()
                ->where('period_number', 'like', '%' . $request->period_number . '%')
                ->pluck('id');

            $query->whereIn('payroll_period_id', $periodIds);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    private function filterData(): array
    {
        $periods = PayrollPeriod::query()
            ->orderByDesc('id')
            ->limit(100)
            ->get(['id', 'period_number', 'month']);

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return [$periods, $users, $this->actions()];
    }

    private function actions(): array
    {
        return [
            'created' => 'إنشاء المسير',
            'calculated' => 'احتساب / إعادة احتساب',
            'approved' => 'اعتماد المسير',
            'approval_cancelled' => 'إلغاء الاعتماد',
            'paid' => 'صرف المسير',
            'deleted' => 'حذف المسير',
        ];
    }
}
