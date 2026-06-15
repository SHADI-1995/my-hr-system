<?php

namespace App\Http\Controllers;

use App\Models\SalaryPaymentMethod;
use Illuminate\Http\Request;

class SalaryPaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('salary_payment_methods.view'), 403);

        $query = SalaryPaymentMethod::query()
            ->withCount('employees')
            ->orderBy('sort_order')
            ->orderBy('name_ar');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name_ar', 'like', '%' . $request->search . '%')
                    ->orWhere('name_en', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $salaryPaymentMethods = $query->paginate(20)->withQueryString();

        return view('salary_payment_methods.index', compact('salaryPaymentMethods'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('salary_payment_methods.create'), 403);

        return view('salary_payment_methods.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('salary_payment_methods.create'), 403);

        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'required|string|max:100|alpha_dash|unique:salary_payment_methods,code',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ], [
            'name_ar.required' => 'اسم طريقة الصرف بالعربي مطلوب',
            'code.required' => 'الكود مطلوب',
            'code.unique' => 'الكود مستخدم من قبل',
            'code.alpha_dash' => 'الكود يجب أن يكون حروف أو أرقام أو شرطة فقط',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;

        SalaryPaymentMethod::create($data);

        return redirect()
            ->route('salary-payment-methods.index')
            ->with('success', 'تم إضافة طريقة صرف الراتب بنجاح');
    }

    public function edit(SalaryPaymentMethod $salaryPaymentMethod)
    {
        abort_if(!auth()->user()->hasPermission('salary_payment_methods.edit'), 403);

        return view('salary_payment_methods.edit', compact('salaryPaymentMethod'));
    }

    public function update(Request $request, SalaryPaymentMethod $salaryPaymentMethod)
    {
        abort_if(!auth()->user()->hasPermission('salary_payment_methods.edit'), 403);

        $request->validate([
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:100|alpha_dash|unique:salary_payment_methods,code,' . $salaryPaymentMethod->id,
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ], [
            'code.unique' => 'الكود مستخدم من قبل',
            'code.alpha_dash' => 'الكود يجب أن يكون حروف أو أرقام أو شرطة فقط',
        ]);

        $data = [];

        /*
         * صلاحيات تعديل على مستوى الحقول.
         * المستخدم لا يستطيع تعديل الحقل إلا إذا كان لديه صلاحية الحقل نفسه.
         */
        if (auth()->user()->hasPermission('salary_payment_methods.edit.name_ar')) {
            $data['name_ar'] = $request->name_ar;
        }

        if (auth()->user()->hasPermission('salary_payment_methods.edit.name_en')) {
            $data['name_en'] = $request->name_en;
        }

        if (auth()->user()->hasPermission('salary_payment_methods.edit.code')) {
            $data['code'] = $request->code;
        }

        if (auth()->user()->hasPermission('salary_payment_methods.edit.is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if (auth()->user()->hasPermission('salary_payment_methods.edit.sort_order')) {
            $data['sort_order'] = $request->sort_order ?? 0;
        }

        if (auth()->user()->hasPermission('salary_payment_methods.edit.notes')) {
            $data['notes'] = $request->notes;
        }

        if (empty($data)) {
            return back()->with('error', 'لا تملك صلاحية تعديل أي حقل في طريقة صرف الراتب');
        }

        $salaryPaymentMethod->update($data);

        return redirect()
            ->route('salary-payment-methods.index')
            ->with('success', 'تم تعديل طريقة صرف الراتب بنجاح');
    }

    public function destroy(SalaryPaymentMethod $salaryPaymentMethod)
    {
        abort_if(!auth()->user()->hasPermission('salary_payment_methods.delete'), 403);

        if ($salaryPaymentMethod->employees()->exists()) {
            return back()->with('error', 'لا يمكن حذف طريقة صرف مرتبطة بموظفين. يمكن تعطيلها بدل الحذف.');
        }

        $salaryPaymentMethod->delete();

        return redirect()
            ->route('salary-payment-methods.index')
            ->with('success', 'تم حذف طريقة صرف الراتب بنجاح');
    }
}
