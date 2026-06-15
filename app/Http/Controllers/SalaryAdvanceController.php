<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryAdvance;
use App\Models\SalaryAdvanceInstallment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalaryAdvanceController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('salary_advances.view'), 403);

        $query = SalaryAdvance::with(['employee.department', 'createdBy', 'approvedBy'])
            ->withCount('installments');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('advance_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('employee', function ($employeeQuery) use ($request) {
                        $employeeQuery->where('full_name', 'like', '%' . $request->search . '%')
                            ->orWhere('employee_number', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $advances = $query->latest()->paginate(20);

        return view('salary_advances.index', compact('advances'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('salary_advances.create'), 403);

        $employees = Employee::orderBy('full_name')->get();

        return view('salary_advances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('salary_advances.create'), 403);

        $data = $this->validateAdvance($request);

        DB::transaction(function () use ($data) {
            $amount = round((float) $data['amount'], 2);
            $count = (int) $data['installments_count'];
            $monthlyAmount = $this->resolveMonthlyInstallment($amount, $count, $data['installment_amount'] ?? null);

            $selectedMonths = $this->normalizeSelectedMonths($data['installment_months'], $count);

            $advance = SalaryAdvance::create([
                'employee_id' => $data['employee_id'],
                'advance_number' => SalaryAdvance::generateNumber(),
                'amount' => $amount,
                'installments_count' => $count,
                'installment_amount' => $monthlyAmount,
                'request_date' => now()->toDateString(),
                'deduction_start_date' => $selectedMonths[0],
                'status' => 'pending',
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->generateInstallmentsFromSelectedMonths($advance, $selectedMonths);
        });

        return redirect()
            ->route('salary-advances.index')
            ->with('success', 'تم إنشاء السلفة وجدولة الأقساط حسب الأشهر المختارة بنجاح');
    }

    public function show(SalaryAdvance $salaryAdvance)
    {
        abort_if(!auth()->user()->hasPermission('salary_advances.view'), 403);

        $salaryAdvance->load(['employee.department', 'installments', 'createdBy', 'approvedBy']);

        return view('salary_advances.show', compact('salaryAdvance'));
    }

    public function edit(SalaryAdvance $salaryAdvance)
    {
        abort_if(!auth()->user()->hasPermission('salary_advances.edit'), 403);

        if (!$salaryAdvance->can_edit_schedule) {
            return redirect()
                ->route('salary-advances.show', $salaryAdvance)
                ->with('error', 'لا يمكن تعديل السلفة بعد خصم أي قسط من مسير الرواتب');
        }

        $salaryAdvance->load('installments');
        $employees = Employee::orderBy('full_name')->get();
        $selectedMonths = $salaryAdvance->installments
            ->pluck('due_date')
            ->map(fn ($date) => optional($date)->format('Y-m-01'))
            ->filter()
            ->values()
            ->toArray();

        return view('salary_advances.edit', compact('salaryAdvance', 'employees', 'selectedMonths'));
    }

    public function update(Request $request, SalaryAdvance $salaryAdvance)
    {
        abort_if(!auth()->user()->hasPermission('salary_advances.edit'), 403);

        if (!$salaryAdvance->can_edit_schedule) {
            return redirect()
                ->route('salary-advances.show', $salaryAdvance)
                ->with('error', 'لا يمكن تعديل السلفة بعد خصم أي قسط من مسير الرواتب');
        }

        $data = $this->validateAdvance($request);

        DB::transaction(function () use ($data, $salaryAdvance) {
            $amount = round((float) $data['amount'], 2);
            $count = (int) $data['installments_count'];
            $monthlyAmount = $this->resolveMonthlyInstallment($amount, $count, $data['installment_amount'] ?? null);
            $selectedMonths = $this->normalizeSelectedMonths($data['installment_months'], $count);

            $salaryAdvance->update([
                'employee_id' => $data['employee_id'],
                'amount' => $amount,
                'installments_count' => $count,
                'installment_amount' => $monthlyAmount,
                'deduction_start_date' => $selectedMonths[0],
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $salaryAdvance->installments()->delete();
            $this->generateInstallmentsFromSelectedMonths($salaryAdvance->fresh(), $selectedMonths);
        });

        return redirect()
            ->route('salary-advances.show', $salaryAdvance)
            ->with('success', 'تم تعديل السلفة والأشهر المختارة للأقساط بنجاح');
    }

    public function approve(SalaryAdvance $salaryAdvance)
    {
        abort_if(!auth()->user()->hasPermission('salary_advances.approve'), 403);

        if ($salaryAdvance->status !== 'pending') {
            return back()->with('error', 'لا يمكن اعتماد هذه السلفة في حالتها الحالية');
        }

        $salaryAdvance->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد السلفة بنجاح');
    }

    public function cancel(Request $request, SalaryAdvance $salaryAdvance)
    {
        abort_if(!auth()->user()->hasPermission('salary_advances.cancel'), 403);

        if (!in_array($salaryAdvance->status, ['pending', 'approved'], true)) {
            return back()->with('error', 'لا يمكن إلغاء هذه السلفة');
        }

        DB::transaction(function () use ($request, $salaryAdvance) {
            $salaryAdvance->update([
                'status' => 'cancelled',
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason,
            ]);

            $salaryAdvance->installments()
                ->where('status', 'pending')
                ->update([
                    'status' => 'cancelled',
                    'notes' => 'تم إلغاء السلفة',
                ]);
        });

        return back()->with('success', 'تم إلغاء السلفة والأقساط المتبقية بنجاح');
    }

    private function validateAdvance(Request $request): array
    {
        return $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'installments_count' => 'required|integer|min:1|max:60',
            'installment_amount' => 'nullable|numeric|min:0',
            'deduction_start_date' => 'required|date',
            'installment_months' => 'required|array',
            'installment_months.*' => 'required|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'amount.required' => 'مبلغ السلفة مطلوب',
            'installments_count.required' => 'عدد الأقساط مطلوب',
            'deduction_start_date.required' => 'تاريخ بداية عرض الأشهر مطلوب',
            'installment_months.required' => 'يجب اختيار أشهر الأقساط',
        ]);
    }

    private function normalizeSelectedMonths(array $months, int $requiredCount): array
    {
        $normalized = collect($months)
            ->filter()
            ->map(fn ($month) => Carbon::parse($month)->startOfMonth()->toDateString())
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        if (count($normalized) !== $requiredCount) {
            throw ValidationException::withMessages([
                'installment_months' => 'عدد الأشهر المختارة يجب أن يساوي عدد الأقساط. عدد الأقساط المطلوب: ' . $requiredCount,
            ]);
        }

        return $normalized;
    }

    private function resolveMonthlyInstallment(float $amount, int $count, $installmentAmount): float
    {
        $monthlyAmount = $installmentAmount !== null && (float) $installmentAmount > 0
            ? round((float) $installmentAmount, 2)
            : round($amount / $count, 2);

        if ($monthlyAmount <= 0) {
            throw ValidationException::withMessages([
                'installment_amount' => 'قيمة القسط الشهري غير صحيحة',
            ]);
        }

        $lastInstallment = round($amount - ($monthlyAmount * ($count - 1)), 2);

        if ($lastInstallment <= 0) {
            throw ValidationException::withMessages([
                'installment_amount' => 'قيمة القسط الشهري كبيرة جدًا مقارنة بمبلغ السلفة وعدد الأقساط',
            ]);
        }

        return $monthlyAmount;
    }

    private function generateInstallmentsFromSelectedMonths(SalaryAdvance $advance, array $selectedMonths): void
    {
        $amount = round((float) $advance->amount, 2);
        $count = (int) $advance->installments_count;
        $monthlyAmount = round((float) $advance->installment_amount, 2);

        foreach ($selectedMonths as $index => $month) {
            $installmentNumber = $index + 1;
            $currentAmount = $monthlyAmount;

            if ($installmentNumber === $count) {
                $previousTotal = $monthlyAmount * ($count - 1);
                $currentAmount = round($amount - $previousTotal, 2);
            }

            SalaryAdvanceInstallment::create([
                'salary_advance_id' => $advance->id,
                'employee_id' => $advance->employee_id,
                'installment_number' => $installmentNumber,
                'amount' => $currentAmount,
                'due_date' => Carbon::parse($month)->startOfMonth()->toDateString(),
                'status' => 'pending',
            ]);
        }
    }
}
