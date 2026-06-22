<?php

namespace App\Http\Controllers;

use App\Models\DeductionType;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeDeductionController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.view'), 403);

        /*
         * تحميل العلاقات الأساسية.
         * علاقة deductionType يتم تحميلها فقط إذا كانت مضافة داخل موديل EmployeeDeduction
         * حتى لا يظهر خطأ إذا نسيت إضافة العلاقة في الموديل.
         */
        $relations = [
            'employee.department',
            'createdBy',
            'approvedBy',
        ];

        if (method_exists(EmployeeDeduction::class, 'deductionType')) {
            $relations[] = 'deductionType';
        }

        $query = EmployeeDeduction::with($relations)
            ->withCount([
                'schedules',
                'pendingSchedules',
                'deductedSchedules',
            ]);

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        /*
         * الفلتر الجديد حسب جدول deduction_types.
         */
        if ($request->deduction_type_id && Schema::hasColumn('employee_deductions', 'deduction_type_id')) {
            $query->where('deduction_type_id', $request->deduction_type_id);
        }

        /*
         * دعم احتياطي للفلتر القديم إذا كان موجودًا في روابط أو صفحات قديمة.
         */
        if ($request->deduction_type) {
            $query->where('deduction_type', $request->deduction_type);
        }

        if ($request->deduction_mode) {
            $query->where('deduction_mode', $request->deduction_mode);
        }

        $deductionTypes = $this->activeDeductionTypes();

        $deductions = $query->latest()->paginate(20)->withQueryString();

        return view('employee_deductions.index', compact('deductions', 'deductionTypes'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.create'), 403);

        if (!Schema::hasTable('deduction_types')) {
            return redirect()
                ->route('employee-deductions.index')
                ->with('error', 'يجب تشغيل Migration الخاص بأنواع الاستقطاعات أولًا قبل إضافة استقطاع.');
        }

        $employees = Employee::orderBy('full_name')->get();

        /*
         * نوع الاستقطاع أصبح من جدول مستقل deduction_types.
         */
        $deductionTypes = $this->activeDeductionTypes();

        return view('employee_deductions.create', compact('employees', 'deductionTypes'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.create'), 403);

        if (!Schema::hasTable('deduction_types') || !Schema::hasColumn('employee_deductions', 'deduction_type_id')) {
            return back()
                ->withInput()
                ->with('error', 'يجب تشغيل Migration الخاص بأنواع الاستقطاعات وربطها بالاستقطاعات قبل الحفظ.');
        }

        $data = $this->validateDeductionData($request);

        $this->validateDeductionMonths($request);

        $deductionType = DeductionType::query()
            ->where('is_active', true)
            ->find($data['deduction_type_id']);

        if (!$deductionType) {
            return back()
                ->withInput()
                ->withErrors(['deduction_type_id' => 'نوع الاستقطاع المحدد غير نشط أو غير موجود']);
        }

        DB::transaction(function () use ($data, $request, $deductionType) {
            $data['deduction_number'] = EmployeeDeduction::generateNumber();
            $data['status'] = 'pending';
            $data['created_by'] = auth()->id();

            $data = $this->prepareDeductionPayload($data, $request, $deductionType);

            EmployeeDeduction::create($data);
        });

        return redirect()
            ->route('employee-deductions.index')
            ->with('success', 'تم إضافة الاستقطاع بنجاح، بانتظار الاعتماد');
    }

    public function edit(EmployeeDeduction $employeeDeduction)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.edit'), 403);

        /*
         * لا نسمح بتعديل استقطاع تم خصمه فعليًا داخل مسير الرواتب.
         */
        if ($this->hasDeductedSchedules($employeeDeduction)) {
            return redirect()
                ->route('employee-deductions.index')
                ->with('error', 'لا يمكن تعديل هذا الاستقطاع لأنه يحتوي على خصومات تم احتسابها أو صرفها.');
        }

        if (!Schema::hasTable('deduction_types')) {
            return redirect()
                ->route('employee-deductions.index')
                ->with('error', 'يجب تشغيل Migration الخاص بأنواع الاستقطاعات أولًا.');
        }

        $employees = Employee::orderBy('full_name')->get();
        $deductionTypes = $this->activeDeductionTypes();

        return view('employee_deductions.edit', compact('employeeDeduction', 'employees', 'deductionTypes'));
    }

    public function update(Request $request, EmployeeDeduction $employeeDeduction)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.edit'), 403);

        /*
         * لا نعدل أي استقطاع تم خصم جزء منه فعليًا.
         */
        if ($this->hasDeductedSchedules($employeeDeduction)) {
            return redirect()
                ->route('employee-deductions.index')
                ->with('error', 'لا يمكن تعديل هذا الاستقطاع لأنه يحتوي على خصومات تم احتسابها أو صرفها.');
        }

        if (!Schema::hasTable('deduction_types') || !Schema::hasColumn('employee_deductions', 'deduction_type_id')) {
            return back()
                ->withInput()
                ->with('error', 'يجب تشغيل Migration الخاص بأنواع الاستقطاعات وربطها بالاستقطاعات قبل التعديل.');
        }

        $data = $this->validateDeductionData($request);

        $this->validateDeductionMonths($request);

        $deductionType = DeductionType::query()
            ->where('is_active', true)
            ->find($data['deduction_type_id']);

        if (!$deductionType) {
            return back()
                ->withInput()
                ->withErrors(['deduction_type_id' => 'نوع الاستقطاع المحدد غير نشط أو غير موجود']);
        }

        DB::transaction(function () use ($data, $request, $deductionType, $employeeDeduction) {
            $data = $this->prepareDeductionPayload($data, $request, $deductionType);

            /*
             * عند التعديل نحذف الجدولة المعلقة فقط.
             * لا نحذف أي جدولة تم خصمها فعليًا.
             */
            if (method_exists($employeeDeduction, 'schedules')) {
                $employeeDeduction->schedules()
                    ->where('status', 'pending')
                    ->delete();
            }

            $employeeDeduction->update($data);

            /*
             * إذا كان الاستقطاع معتمدًا أو نشطًا نعيد توليد الجدولة المعلقة مباشرة بعد التعديل.
             * أما pending فيتم توليدها عند الاعتماد.
             */
            if (
                in_array($employeeDeduction->status, ['approved', 'active'], true) &&
                Schema::hasTable('employee_deduction_schedules') &&
                method_exists($employeeDeduction, 'generateSchedules')
            ) {
                $employeeDeduction->refresh();
                $employeeDeduction->generateSchedules(false);
            }
        });

        return redirect()
            ->route('employee-deductions.index')
            ->with('success', 'تم تعديل الاستقطاع بنجاح');
    }

    public function approve(EmployeeDeduction $employeeDeduction)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.approve'), 403);

        if ($employeeDeduction->status !== 'pending') {
            return back()->with('error', 'لا يمكن اعتماد هذا الاستقطاع في حالته الحالية');
        }

        if (
            !Schema::hasTable('employee_deduction_schedules') ||
            !method_exists($employeeDeduction, 'generateSchedules')
        ) {
            return back()->with('error', 'يجب تركيب جدول جدولة الاستقطاعات وتحديث الموديل قبل اعتماد الاستقطاع.');
        }

        DB::transaction(function () use ($employeeDeduction) {
            $employeeDeduction->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            /*
             * بعد الاعتماد يتم توليد جدول الخصومات الشهرية.
             * PayrollPeriodController سيقرأ من هذا الجدول وقت احتساب المسير.
             */
            $employeeDeduction->refresh();
            $employeeDeduction->generateSchedules(true);
        });

        return back()->with('success', 'تم اعتماد الاستقطاع وتوليد جدول الخصومات الشهرية بنجاح');
    }

    public function cancel(Request $request, EmployeeDeduction $employeeDeduction)
    {
        abort_if(!auth()->user()->hasPermission('employee_deductions.cancel'), 403);

        if (!in_array($employeeDeduction->status, ['pending', 'approved', 'active'], true)) {
            return back()->with('error', 'لا يمكن إلغاء هذا الاستقطاع');
        }

        DB::transaction(function () use ($request, $employeeDeduction) {
            $employeeDeduction->update([
                'status' => 'cancelled',
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason,
            ]);

            /*
             * نلغي فقط الخصومات المعلقة.
             * الخصومات التي تم خصمها سابقًا تبقى محفوظة للتدقيق والسجلات.
             */
            if (method_exists($employeeDeduction, 'schedules')) {
                $employeeDeduction->schedules()
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'cancelled',
                        'notes' => $request->cancel_reason,
                        'updated_at' => now(),
                    ]);
            }
        });

        return back()->with('success', 'تم إلغاء الاستقطاع والخصومات المعلقة بنجاح');
    }

    private function validateDeductionData(Request $request): array
    {
        return $request->validate([
            'employee_id' => 'required|exists:employees,id',

            /*
             * نوع الاستقطاع الآن يأتي من جدول deduction_types.
             */
            'deduction_type_id' => 'required|exists:deduction_types,id',
            'title' => 'nullable|string|max:255',

            /*
             * one_time        = مرة واحدة
             * monthly         = كل شهر
             * selected_months = أشهر محددة
             * installments    = أقساط
             * percentage      = نسبة من الراتب
             */
            'deduction_mode' => 'required|in:one_time,monthly,selected_months,installments,percentage',
            'calculation_type' => 'nullable|in:fixed,percentage',

            /*
             * في حالة percentage نستخدم amount كنسبة إذا لم يصل percentage.
             * هذا يحافظ على توافق النظام القديم.
             */
            'amount' => 'required|numeric|min:0.01',
            'percentage' => 'nullable|required_if:calculation_type,percentage|numeric|min:0.01|max:100',
            'total_amount' => 'nullable|numeric|min:0.01',

            'installments_count' => 'nullable|required_if:deduction_mode,installments|integer|min:1',
            'monthly_amount' => 'nullable|numeric|min:0',

            /*
             * دعم النظام الجديد بالشهر، مع دعم start_date/end_date القديم.
             */
            'start_month' => 'nullable|required_unless:deduction_mode,selected_months|date_format:Y-m',
            'end_month' => 'nullable|date_format:Y-m',
            'selected_months' => 'nullable|required_if:deduction_mode,selected_months|array',
            'selected_months.*' => 'date_format:Y-m',

            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',

            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'employee_id.exists' => 'الموظف المحدد غير صحيح',

            'deduction_type_id.required' => 'نوع الاستقطاع مطلوب',
            'deduction_type_id.exists' => 'نوع الاستقطاع المحدد غير صحيح',

            'amount.required' => 'مبلغ الاستقطاع مطلوب',
            'amount.numeric' => 'مبلغ الاستقطاع يجب أن يكون رقمًا',
            'amount.min' => 'مبلغ الاستقطاع يجب أن يكون أكبر من صفر',

            'deduction_mode.required' => 'طريقة الاستقطاع مطلوبة',
            'deduction_mode.in' => 'طريقة الاستقطاع غير صحيحة',

            'percentage.required_if' => 'النسبة مطلوبة عند اختيار خصم نسبة',
            'percentage.numeric' => 'النسبة يجب أن تكون رقمًا',
            'percentage.max' => 'النسبة يجب ألا تتجاوز 100%',

            'installments_count.required_if' => 'عدد الأقساط مطلوب عند اختيار الاستقطاع بالأقساط',
            'installments_count.integer' => 'عدد الأقساط يجب أن يكون رقمًا صحيحًا',
            'installments_count.min' => 'عدد الأقساط يجب أن يكون 1 على الأقل',

            'start_month.required_unless' => 'شهر بداية الاستقطاع مطلوب',
            'start_month.date_format' => 'صيغة شهر البداية يجب أن تكون مثل 2026-06',
            'end_month.date_format' => 'صيغة شهر النهاية يجب أن تكون مثل 2026-06',

            'selected_months.required_if' => 'يجب اختيار شهر واحد على الأقل عند اختيار أشهر محددة',
            'selected_months.array' => 'الأشهر المحددة يجب أن تكون قائمة',
            'selected_months.*.date_format' => 'صيغة أحد الأشهر المحددة غير صحيحة',

            'start_date.date' => 'تاريخ بداية الاستقطاع غير صحيح',
            'end_date.after_or_equal' => 'تاريخ نهاية الاستقطاع يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);
    }

    private function prepareDeductionPayload(array $data, Request $request, DeductionType $deductionType): array
    {
        /*
         * نملأ الحقل النصي القديم deduction_type باسم النوع
         * حتى لا تتأثر الصفحات أو التقارير القديمة.
         */
        $data['deduction_type'] = $deductionType->name_ar;

        /*
         * تجهيز الحقول الجديدة مع التوافق مع الحقول القديمة.
         */
        $data['calculation_type'] = $request->calculation_type
            ?: ($request->deduction_mode === 'percentage' ? 'percentage' : 'fixed');

        if ($data['calculation_type'] === 'percentage' && empty($data['percentage'])) {
            $data['percentage'] = $request->amount;
        }

        if (empty($data['total_amount'])) {
            $data['total_amount'] = $request->deduction_mode === 'installments'
                ? $request->amount
                : ($request->total_amount ?: $request->amount);
        }

        if (empty($data['title'])) {
            $data['title'] = $deductionType->name_ar;
        }

        /*
         * إذا كانت الواجهة القديمة ترسل start_date فقط، نحولها إلى start_month.
         */
        if (empty($data['start_month']) && !empty($data['start_date'])) {
            $data['start_month'] = date('Y-m', strtotime($data['start_date']));
        }

        if (empty($data['end_month']) && !empty($data['end_date'])) {
            $data['end_month'] = date('Y-m', strtotime($data['end_date']));
        }

        if ($request->deduction_mode === 'selected_months') {
            $data['selected_months'] = collect($request->selected_months ?? [])
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all();
        } else {
            $data['selected_months'] = null;
        }

        return $data;
    }

    private function validateDeductionMonths(Request $request): void
    {
        /*
         * تحقق إضافي على مستوى الأشهر لأن after_or_equal لا يدعم Y-m مباشرة بشكل مثالي.
         */
        if ($request->filled('start_month') && $request->filled('end_month')) {
            if ($request->end_month < $request->start_month) {
                abort(
                    redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['end_month' => 'شهر النهاية يجب أن يكون بعد أو يساوي شهر البداية'])
                );
            }
        }

        if ($request->deduction_mode === 'selected_months') {
            $months = collect($request->selected_months ?? [])
                ->filter()
                ->unique()
                ->values();

            if ($months->isEmpty()) {
                abort(
                    redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['selected_months' => 'يجب اختيار شهر واحد على الأقل'])
                );
            }
        }
    }

    private function hasDeductedSchedules(EmployeeDeduction $employeeDeduction): bool
    {
        if (!method_exists($employeeDeduction, 'schedules')) {
            return false;
        }

        return $employeeDeduction->schedules()
            ->whereIn('status', ['deducted', 'paid'])
            ->exists();
    }

    private function activeDeductionTypes()
    {
        if (!Schema::hasTable('deduction_types')) {
            return collect();
        }

        return DeductionType::active()
            ->orderBy('sort_order')
            ->orderBy('name_ar')
            ->get();
    }
}
