<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Nationality;
use App\Models\EmployeeIqama;
use App\Models\EmployeePassport;
use App\Models\EmployeeHealthCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('employees.view'), 403);

        $query = Employee::with([
            'department',
            'position',
            'nationality',
            'latestIqama',
            'latestPassport',
            'latestHealthCard',
            'directManagerUser',
        ]);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('second_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('iban', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->nationality_id) {
            $query->where('nationality_id', $request->nationality_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $employees = $query->latest()->paginate(20);

        $departments = Department::orderBy('name')->get();
        $nationalities = Nationality::where('is_active', 1)->orderBy('name_ar')->get();

        return view('employees.index', compact(
            'employees',
            'departments',
            'nationalities'
        ));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('employees.create'), 403);

        $departments = Department::where('is_active', 1)->orderBy('name')->get();
        $positions = Position::where('is_active', 1)->orderBy('title')->get();
        $nationalities = Nationality::where('is_active', 1)->orderBy('name_ar')->get();
        $directManagers = User::orderBy('name')->get();

        return view('employees.create', compact(
            'departments',
            'positions',
            'nationalities',
            'directManagers'
        ));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('employees.create'), 403);

        $this->abortIfUnauthorizedPhotoUpload($request, [
            'iqama_photo' => 'employee_iqamas.photo.create',
            'passport_photo' => 'employee_passports.photo.create',
            'health_card_photo' => 'employee_health_cards.photo.create',
        ]);

        $request->validate([
            'employee_number' => 'nullable|string|max:255',

            'first_name' => 'required|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',

            'email' => 'nullable|email|max:255|unique:employees,email',
            'phone' => 'nullable|string|max:30',

            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'direct_manager_user_id' => 'nullable|exists:users,id',

            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date|after_or_equal:hire_date',
            'termination_reason' => 'nullable|string|max:255',

            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',

            'bank_name' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:34',

            'status' => 'nullable|in:active,inactive,terminated',
            'notes' => 'nullable|string',

            'iqama_number' => 'nullable|string|max:255|unique:employee_iqamas,iqama_number',
            'iqama_issue_date' => 'nullable|date',
            'iqama_expiry_date' => 'nullable|required_with:iqama_number|date|after_or_equal:iqama_issue_date',
            'sponsor_name' => 'nullable|string|max:255',
            'iqama_notes' => 'nullable|string',
            'iqama_photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',

            'passport_number' => 'nullable|string|max:255|unique:employee_passports,passport_number',
            'passport_issue_date' => 'nullable|date',
            'passport_expiry_date' => 'nullable|required_with:passport_number|date|after_or_equal:passport_issue_date',
            'passport_issue_place' => 'nullable|string|max:255',
            'passport_country' => 'nullable|string|max:255',
            'passport_notes' => 'nullable|string',
            'passport_photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',

            'health_card_number' => 'nullable|string|max:255|unique:employee_health_cards,card_number',
            'health_card_issue_date' => 'nullable|date',
            'health_card_expiry_date' => 'nullable|required_with:health_card_number|date|after_or_equal:health_card_issue_date',
            'health_card_issuer' => 'nullable|string|max:255',
            'health_card_notes' => 'nullable|string',
            'health_card_photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
        ], [
            'employee_number.unique' => 'رقم الموظف مستخدم من قبل',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'first_name.required' => 'الاسم الأول مطلوب',
            'department_id.required' => 'القسم مطلوب',
            'position_id.required' => 'الوظيفة مطلوبة',
            'direct_manager_user_id.exists' => 'المدير المباشر المحدد غير صحيح',
            'hire_date.required' => 'تاريخ المباشرة مطلوب',
            'basic_salary.required' => 'الراتب الأساسي مطلوب',
            'termination_date.after_or_equal' => 'تاريخ انتهاء الخدمة يجب أن يكون بعد أو يساوي تاريخ المباشرة',

            'iqama_number.unique' => 'رقم الإقامة مستخدم من قبل',
            'iqama_expiry_date.required_with' => 'تاريخ انتهاء الإقامة مطلوب عند إدخال رقم الإقامة',
            'iqama_expiry_date.after_or_equal' => 'تاريخ انتهاء الإقامة يجب أن يكون بعد أو يساوي تاريخ الإصدار',

            'passport_number.unique' => 'رقم الجواز مستخدم من قبل',
            'passport_expiry_date.required_with' => 'تاريخ انتهاء الجواز مطلوب عند إدخال رقم الجواز',
            'passport_expiry_date.after_or_equal' => 'تاريخ انتهاء الجواز يجب أن يكون بعد أو يساوي تاريخ الإصدار',

            'health_card_number.unique' => 'رقم الكرت الصحي مستخدم من قبل',
            'health_card_expiry_date.required_with' => 'تاريخ انتهاء الكرت الصحي مطلوب عند إدخال رقم الكرت',
            'health_card_expiry_date.after_or_equal' => 'تاريخ انتهاء الكرت الصحي يجب أن يكون بعد أو يساوي تاريخ الإصدار',

            'iqama_photo.mimes' => 'صورة الإقامة يجب أن تكون بصيغة jpg أو jpeg أو png أو webp أو pdf',
            'iqama_photo.max' => 'حجم صورة الإقامة يجب ألا يتجاوز 4 ميجا',
            'passport_photo.mimes' => 'صورة الجواز يجب أن تكون بصيغة jpg أو jpeg أو png أو webp أو pdf',
            'passport_photo.max' => 'حجم صورة الجواز يجب ألا يتجاوز 4 ميجا',
            'health_card_photo.mimes' => 'صورة الكرت الصحي يجب أن تكون بصيغة jpg أو jpeg أو png أو webp أو pdf',
            'health_card_photo.max' => 'حجم صورة الكرت الصحي يجب ألا يتجاوز 4 ميجا',
        ]);

        DB::transaction(function () use ($request) {
            $employee = Employee::create([
                'employee_number' => $this->resolveEmployeeNumber($request->employee_number),

                'first_name' => $request->first_name,
                'second_name' => $request->second_name,
                'last_name' => $request->last_name,

                'email' => $request->email,
                'phone' => $request->phone,

                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'nationality_id' => $request->nationality_id,
                'direct_manager_user_id' => $request->direct_manager_user_id,

                'hire_date' => $request->hire_date,
                'termination_date' => $request->termination_date,
                'termination_reason' => $request->termination_reason,

                'basic_salary' => $request->basic_salary ?? 0,
                'housing_allowance' => $request->housing_allowance ?? 0,
                'transport_allowance' => $request->transport_allowance ?? 0,
                'food_allowance' => $request->food_allowance ?? 0,
                'other_allowance' => $request->other_allowance ?? 0,

                'bank_name' => $request->bank_name,
                'iban' => $request->iban,

                'status' => $request->status ?? 'active',
                'notes' => $request->notes,
            ]);

            if (
                auth()->user()->hasPermission('employee_iqamas.create') &&
                $request->filled('iqama_number')
            ) {
                EmployeeIqama::create([
                    'employee_id' => $employee->id,
                    'iqama_number' => $request->iqama_number,
                    'issue_date' => $request->iqama_issue_date,
                    'expiry_date' => $request->iqama_expiry_date,
                    'sponsor_name' => $request->sponsor_name,
                    'notes' => $request->iqama_notes,
                    'photo' => auth()->user()->hasPermission('employee_iqamas.photo.create')
                        ? $this->storeDocumentPhoto($request, 'iqama_photo', null, 'employee-documents/iqamas')
                        : null,
                ]);
            }

            if (
                auth()->user()->hasPermission('employee_passports.create') &&
                $request->filled('passport_number')
            ) {
                EmployeePassport::create([
                    'employee_id' => $employee->id,
                    'passport_number' => $request->passport_number,
                    'issue_date' => $request->passport_issue_date,
                    'expiry_date' => $request->passport_expiry_date,
                    'issue_place' => $request->passport_issue_place,
                    'country' => $request->passport_country,
                    'notes' => $request->passport_notes,
                    'photo' => auth()->user()->hasPermission('employee_passports.photo.create')
                        ? $this->storeDocumentPhoto($request, 'passport_photo', null, 'employee-documents/passports')
                        : null,
                ]);
            }

            if (
                auth()->user()->hasPermission('employee_health_cards.create') &&
                $request->filled('health_card_number')
            ) {
                EmployeeHealthCard::create([
                    'employee_id' => $employee->id,
                    'card_number' => $request->health_card_number,
                    'issue_date' => $request->health_card_issue_date,
                    'expiry_date' => $request->health_card_expiry_date,
                    'issuer' => $request->health_card_issuer,
                    'notes' => $request->health_card_notes,
                    'photo' => auth()->user()->hasPermission('employee_health_cards.photo.create')
                        ? $this->storeDocumentPhoto($request, 'health_card_photo', null, 'employee-documents/health-cards')
                        : null,
                ]);
            }
        });

        return redirect()
            ->route('employees.index')
            ->with('success', 'تم إضافة الموظف بنجاح');
    }

    public function show(Employee $employee)
    {
        abort_if(!auth()->user()->hasPermission('employees.show'), 403);

        $employee->load([
            'department',
            'position',
            'nationality',
            'latestIqama',
            'latestPassport',
            'latestHealthCard',
            'iqamas',
            'passports',
            'healthCards',
            'salaryHistories.changedBy',
            'directManagerUser',
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        abort_if(!auth()->user()->hasPermission('employees.edit'), 403);

        $employee->load([
            'latestIqama',
            'latestPassport',
            'latestHealthCard',
            'directManagerUser',
        ]);

        $departments = Department::where('is_active', 1)->orderBy('name')->get();
        $positions = Position::where('is_active', 1)->orderBy('title')->get();
        $nationalities = Nationality::where('is_active', 1)->orderBy('name_ar')->get();
        $directManagers = User::orderBy('name')->get();

        return view('employees.edit', compact(
            'employee',
            'departments',
            'positions',
            'nationalities',
            'directManagers'
        ));
    }

    public function update(Request $request, Employee $employee)
    {
        abort_if(!auth()->user()->hasPermission('employees.edit'), 403);

        $this->abortIfUnauthorizedPhotoUpload($request, [
            'iqama_photo' => 'employee_iqamas.photo.edit',
            'passport_photo' => 'employee_passports.photo.edit',
            'health_card_photo' => 'employee_health_cards.photo.edit',
        ]);

        $latestIqama = EmployeeIqama::where('employee_id', $employee->id)->latest('id')->first();
        $latestPassport = EmployeePassport::where('employee_id', $employee->id)->latest('id')->first();
        $latestHealthCard = EmployeeHealthCard::where('employee_id', $employee->id)->latest('id')->first();

        $latestIqamaId = $latestIqama?->id;
        $latestPassportId = $latestPassport?->id;
        $latestHealthCardId = $latestHealthCard?->id;

        $request->validate([
            'employee_number' => 'nullable|string|max:255|unique:employees,employee_number,' . $employee->id,

            'first_name' => 'nullable|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',

            'email' => 'nullable|email|max:255|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:30',

            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'direct_manager_user_id' => 'nullable|exists:users,id',

            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date|after_or_equal:hire_date',
            'termination_reason' => 'nullable|string|max:255',

            'basic_salary' => 'nullable|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',

            'bank_name' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:34',

            'status' => 'nullable|in:active,inactive,terminated',
            'notes' => 'nullable|string',

            'iqama_number' => 'nullable|string|max:255|unique:employee_iqamas,iqama_number,' . $latestIqamaId,
            'iqama_issue_date' => 'nullable|date',
            'iqama_expiry_date' => 'nullable|required_with:iqama_number|date|after_or_equal:iqama_issue_date',
            'sponsor_name' => 'nullable|string|max:255',
            'iqama_notes' => 'nullable|string',
            'iqama_photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',

            'passport_number' => 'nullable|string|max:255|unique:employee_passports,passport_number,' . $latestPassportId,
            'passport_issue_date' => 'nullable|date',
            'passport_expiry_date' => 'nullable|required_with:passport_number|date|after_or_equal:passport_issue_date',
            'passport_issue_place' => 'nullable|string|max:255',
            'passport_country' => 'nullable|string|max:255',
            'passport_notes' => 'nullable|string',
            'passport_photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',

            'health_card_number' => 'nullable|string|max:255|unique:employee_health_cards,card_number,' . $latestHealthCardId,
            'health_card_issue_date' => 'nullable|date',
            'health_card_expiry_date' => 'nullable|required_with:health_card_number|date|after_or_equal:health_card_issue_date',
            'health_card_issuer' => 'nullable|string|max:255',
            'health_card_notes' => 'nullable|string',
            'health_card_photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
        ], [
            'employee_number.unique' => 'رقم الموظف مستخدم من قبل',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'direct_manager_user_id.exists' => 'المدير المباشر المحدد غير صحيح',

            'iqama_number.unique' => 'رقم الإقامة مستخدم من قبل',
            'iqama_expiry_date.required_with' => 'تاريخ انتهاء الإقامة مطلوب عند إدخال رقم الإقامة',
            'iqama_expiry_date.after_or_equal' => 'تاريخ انتهاء الإقامة يجب أن يكون بعد أو يساوي تاريخ الإصدار',

            'passport_number.unique' => 'رقم الجواز مستخدم من قبل',
            'passport_expiry_date.required_with' => 'تاريخ انتهاء الجواز مطلوب عند إدخال رقم الجواز',
            'passport_expiry_date.after_or_equal' => 'تاريخ انتهاء الجواز يجب أن يكون بعد أو يساوي تاريخ الإصدار',

            'health_card_number.unique' => 'رقم الكرت الصحي مستخدم من قبل',
            'health_card_expiry_date.required_with' => 'تاريخ انتهاء الكرت الصحي مطلوب عند إدخال رقم الكرت',
            'health_card_expiry_date.after_or_equal' => 'تاريخ انتهاء الكرت الصحي يجب أن يكون بعد أو يساوي تاريخ الإصدار',

            'iqama_photo.mimes' => 'صورة الإقامة يجب أن تكون بصيغة jpg أو jpeg أو png أو webp أو pdf',
            'iqama_photo.max' => 'حجم صورة الإقامة يجب ألا يتجاوز 4 ميجا',
            'passport_photo.mimes' => 'صورة الجواز يجب أن تكون بصيغة jpg أو jpeg أو png أو webp أو pdf',
            'passport_photo.max' => 'حجم صورة الجواز يجب ألا يتجاوز 4 ميجا',
            'health_card_photo.mimes' => 'صورة الكرت الصحي يجب أن تكون بصيغة jpg أو jpeg أو png أو webp أو pdf',
            'health_card_photo.max' => 'حجم صورة الكرت الصحي يجب ألا يتجاوز 4 ميجا',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $data = [];

            if (auth()->user()->hasPermission('employees.edit.employee_number')) {
                $data['employee_number'] = $request->employee_number;
            }

            if (auth()->user()->hasPermission('employees.edit.name')) {
                $data['first_name'] = $request->first_name;
                $data['second_name'] = $request->second_name;
                $data['last_name'] = $request->last_name;
            }

            if (auth()->user()->hasPermission('employees.edit.email')) {
                $oldEmail = strtolower(trim((string) $employee->email));
                $newEmail = strtolower(trim((string) $request->email));

                $data['email'] = $request->email;

                /*
                 * إذا تم تغيير البريد الرسمي من لوحة الإدارة:
                 * نلغي توثيق البريد السابق حتى يتحقق الموظف من البريد الجديد عند الدخول.
                 */
                if ($oldEmail !== $newEmail) {
                    $data['portal_email_verified_at'] = null;
                    $data['portal_pending_email'] = null;
                    $data['portal_email_verification_code'] = null;
                    $data['portal_email_verification_expires_at'] = null;
                }
            }

            if (auth()->user()->hasPermission('employees.edit.phone')) {
                $data['phone'] = $request->phone;
            }

            if (auth()->user()->hasPermission('employees.edit.department_id')) {
                $data['department_id'] = $request->department_id;
            }

            if (auth()->user()->hasPermission('employees.edit.position_id')) {
                $data['position_id'] = $request->position_id;
            }

            if (auth()->user()->hasPermission('employees.edit.nationality_id')) {
                $data['nationality_id'] = $request->nationality_id;
            }

            // المدير المباشر للموظف
            $data['direct_manager_user_id'] = $request->direct_manager_user_id;

            if (auth()->user()->hasPermission('employees.edit.hire_date')) {
                $data['hire_date'] = $request->hire_date;
            }

            if (auth()->user()->hasPermission('employees.edit.termination_date')) {
                $data['termination_date'] = $request->termination_date;
            }

            if (auth()->user()->hasPermission('employees.edit.termination_reason')) {
                $data['termination_reason'] = $request->termination_reason;
            }

            if (auth()->user()->hasPermission('employees.edit.basic_salary')) {
                $data['basic_salary'] = $request->basic_salary;
            }

            if (auth()->user()->hasPermission('employees.edit.housing_allowance')) {
                $data['housing_allowance'] = $request->housing_allowance ?? 0;
            }

            if (auth()->user()->hasPermission('employees.edit.transport_allowance')) {
                $data['transport_allowance'] = $request->transport_allowance ?? 0;
            }

            if (auth()->user()->hasPermission('employees.edit.food_allowance')) {
                $data['food_allowance'] = $request->food_allowance ?? 0;
            }

            if (auth()->user()->hasPermission('employees.edit.other_allowance')) {
                $data['other_allowance'] = $request->other_allowance ?? 0;
            }

            if (auth()->user()->hasPermission('employees.edit.bank_name')) {
                $data['bank_name'] = $request->bank_name;
            }

            if (auth()->user()->hasPermission('employees.edit.iban')) {
                $data['iban'] = $request->iban;
            }

            if (auth()->user()->hasPermission('employees.edit.status')) {
                $data['status'] = $request->status;
            }

            if (auth()->user()->hasPermission('employees.edit.notes')) {
                $data['notes'] = $request->notes;
            }

            $employee->update($data);

            if (
                auth()->user()->hasPermission('employee_iqamas.edit') &&
                $request->filled('iqama_number')
            ) {
                $latestIqama = EmployeeIqama::where('employee_id', $employee->id)
                    ->latest('id')
                    ->first();

                $iqamaPhoto = auth()->user()->hasPermission('employee_iqamas.photo.edit')
                    ? $this->storeDocumentPhoto(
                        $request,
                        'iqama_photo',
                        $latestIqama?->photo,
                        'employee-documents/iqamas'
                    )
                    : $latestIqama?->photo;

                EmployeeIqama::updateOrCreate(
                    ['id' => $latestIqama?->id],
                    [
                        'employee_id' => $employee->id,
                        'iqama_number' => $request->iqama_number,
                        'issue_date' => $request->iqama_issue_date,
                        'expiry_date' => $request->iqama_expiry_date,
                        'sponsor_name' => $request->sponsor_name,
                        'notes' => $request->iqama_notes,
                        'photo' => $iqamaPhoto,
                    ]
                );
            }

            if (
                auth()->user()->hasPermission('employee_passports.edit') &&
                $request->filled('passport_number')
            ) {
                $latestPassport = EmployeePassport::where('employee_id', $employee->id)
                    ->latest('id')
                    ->first();

                $passportPhoto = auth()->user()->hasPermission('employee_passports.photo.edit')
                    ? $this->storeDocumentPhoto(
                        $request,
                        'passport_photo',
                        $latestPassport?->photo,
                        'employee-documents/passports'
                    )
                    : $latestPassport?->photo;

                EmployeePassport::updateOrCreate(
                    ['id' => $latestPassport?->id],
                    [
                        'employee_id' => $employee->id,
                        'passport_number' => $request->passport_number,
                        'issue_date' => $request->passport_issue_date,
                        'expiry_date' => $request->passport_expiry_date,
                        'issue_place' => $request->passport_issue_place,
                        'country' => $request->passport_country,
                        'notes' => $request->passport_notes,
                        'photo' => $passportPhoto,
                    ]
                );
            }

            if (
                auth()->user()->hasPermission('employee_health_cards.edit') &&
                $request->filled('health_card_number')
            ) {
                $latestHealthCard = EmployeeHealthCard::where('employee_id', $employee->id)
                    ->latest('id')
                    ->first();

                $healthCardPhoto = auth()->user()->hasPermission('employee_health_cards.photo.edit')
                    ? $this->storeDocumentPhoto(
                        $request,
                        'health_card_photo',
                        $latestHealthCard?->photo,
                        'employee-documents/health-cards'
                    )
                    : $latestHealthCard?->photo;

                EmployeeHealthCard::updateOrCreate(
                    ['id' => $latestHealthCard?->id],
                    [
                        'employee_id' => $employee->id,
                        'card_number' => $request->health_card_number,
                        'issue_date' => $request->health_card_issue_date,
                        'expiry_date' => $request->health_card_expiry_date,
                        'issuer' => $request->health_card_issuer,
                        'notes' => $request->health_card_notes,
                        'photo' => $healthCardPhoto,
                    ]
                );
            }
        });

        return redirect()
            ->route('employees.index')
            ->with('success', 'تم تعديل بيانات الموظف بنجاح');
    }

    public function destroy(Employee $employee)
    {
        abort_if(!auth()->user()->hasPermission('employees.delete'), 403);

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'تم حذف الموظف بنجاح');
    }

    public function createIqama(Employee $employee)
    {
        abort_if(!auth()->user()->hasPermission('employee_iqamas.create'), 403);

        return view('employees.iqamas.create', compact('employee'));
    }

    public function storeIqama(Request $request, Employee $employee)
    {
        abort_if(!auth()->user()->hasPermission('employee_iqamas.create'), 403);

        $this->abortIfUnauthorizedPhotoUpload($request, [
            'photo' => 'employee_iqamas.photo.create',
        ]);

        $request->validate([
            'iqama_number' => 'required|string|max:255|unique:employee_iqamas,iqama_number',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'sponsor_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
        ], [
            'iqama_number.required' => 'رقم الإقامة مطلوب',
            'iqama_number.unique' => 'رقم الإقامة مستخدم من قبل',
            'expiry_date.required' => 'تاريخ انتهاء الإقامة مطلوب',
            'expiry_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ الإصدار',
            'photo.mimes' => 'صورة الإقامة يجب أن تكون بصيغة jpg أو jpeg أو png أو webp أو pdf',
            'photo.max' => 'حجم صورة الإقامة يجب ألا يتجاوز 4 ميجا',
        ]);

        EmployeeIqama::create([
            'employee_id' => $employee->id,
            'iqama_number' => $request->iqama_number,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'sponsor_name' => $request->sponsor_name,
            'notes' => $request->notes,
            'photo' => auth()->user()->hasPermission('employee_iqamas.photo.create')
                ? $this->storeDocumentPhoto($request, 'photo', null, 'employee-documents/iqamas')
                : null,
        ]);

        return redirect()
            ->route('employees.show', $employee->id)
            ->with('success', 'تم إضافة الإقامة بنجاح');
    }


    private function abortIfUnauthorizedPhotoUpload(Request $request, array $fieldPermissions): void
    {
        foreach ($fieldPermissions as $fieldName => $permission) {
            if ($request->hasFile($fieldName) && !auth()->user()->hasPermission($permission)) {
                abort(403, 'ليس لديك صلاحية رفع أو تعديل صورة الوثيقة');
            }
        }
    }


    private function resolveEmployeeNumber(?string $requestedNumber): string
    {
        $requestedNumber = trim((string) $requestedNumber);

        /*
         * إذا ترك المستخدم الرقم فارغًا أو أدخل رقمًا موجودًا مسبقًا
         * نقوم بتوليد رقم وظيفي جديد تلقائيًا بدل ظهور خطأ Duplicate entry.
         */
        if ($requestedNumber !== '' && !Employee::where('employee_number', $requestedNumber)->exists()) {
            return $requestedNumber;
        }

        return $this->generateNextEmployeeNumber();
    }

    private function generateNextEmployeeNumber(): string
    {
        /*
         * نستخدم قفل داخل نفس transaction حتى لا يتولد نفس الرقم لموظفين في نفس اللحظة.
         */
        $lastEmployee = Employee::whereNotNull('employee_number')
            ->where('employee_number', 'like', 'EMP-%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $lastNumber = 0;

        if ($lastEmployee && preg_match('/EMP-(\d+)/', (string) $lastEmployee->employee_number, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        do {
            $lastNumber++;
            $newNumber = 'EMP-' . str_pad((string) $lastNumber, 6, '0', STR_PAD_LEFT);
        } while (Employee::where('employee_number', $newNumber)->exists());

        return $newNumber;
    }

    private function storeDocumentPhoto(Request $request, string $fieldName, ?string $oldPhoto, string $folder): ?string
    {
        if (!$request->hasFile($fieldName)) {
            return $oldPhoto;
        }

        if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return $request->file($fieldName)->store($folder, 'public');
    }

}
