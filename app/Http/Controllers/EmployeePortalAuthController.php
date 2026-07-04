<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class EmployeePortalAuthController extends Controller
{
    public function showRegister()
    {
        return view('employee_portal.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'iqama_number' => ['required', 'string', 'max:50'],
            'email_action' => ['nullable', 'in:use_existing,replace'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'iqama_number.required' => 'رقم الإقامة مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 6 أحرف',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق',
        ]);

        $employee = $this->findEmployeeByIqama($request->iqama_number);

        if (!$employee) {
            return back()->withInput()->with('error', 'رقم الإقامة غير موجود في سجلات الموظفين');
        }

        $currentEmail = strtolower(trim((string) ($employee->email ?? '')));

        /*
         * إذا كان للموظف بريد محفوظ:
         * لا نرسل الرمز ولا نستبدل البريد مباشرة.
         * يجب أن يرجع المستخدم للصفحة ويختار:
         * استخدام البريد الحالي أو استبداله ببريد جديد.
         */
        if ($currentEmail && !$request->boolean('confirm_email_choice')) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('existing_employee_email', $currentEmail)
                ->with('info', 'يوجد بريد إلكتروني مسجل لهذا الموظف. اختر استخدامه أو استبداله قبل إرسال رمز التحقق.');
        }

        /*
         * البريد النهائي المطلوب التحقق منه.
         * ملاحظة مهمة:
         * إذا كان البريد جديدًا لا نحفظه مباشرة في employees.email.
         * نحفظه مؤقتًا في portal_pending_email حتى يتم إدخال رمز التحقق بنجاح.
         */
        if ($currentEmail && $request->email_action === 'use_existing') {
            $emailToVerify = $currentEmail;
        } else {
            if (!$request->email) {
                return back()
                    ->withInput($request->except(['password', 'password_confirmation']))
                    ->with('existing_employee_email', $currentEmail)
                    ->with('error', 'البريد الإلكتروني الجديد مطلوب عند اختيار استبدال البريد');
            }

            $emailToVerify = strtolower(trim($request->email));
        }

        $emailUsed = Employee::query()
            ->where('email', $emailToVerify)
            ->where('id', '!=', $employee->id)
            ->exists();

        if ($emailUsed) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('existing_employee_email', $currentEmail)
                ->with('error', 'هذا البريد الإلكتروني مستخدم في حساب موظف آخر');
        }

        if ($employee->portal_password && $employee->portal_email_verified_at) {
            return redirect()
                ->route('unified-login', ['account' => 'employee'])
                ->with('error', 'هذا الموظف مسجل مسبقاً، يرجى تسجيل الدخول أو استخدام نسيت كلمة المرور');
        }

        /*
         * لو كان الموظف لا يملك بريد سابقًا:
         * أيضاً لا نعتمد البريد إلا بعد التحقق، لكن نحفظه مؤقتًا.
         *
         * لو كان البريد الحالي نفسه:
         * نحفظه مؤقتًا ونرسل له رمز التحقق.
         */
        $employee->forceFill([
            'portal_pending_email' => $emailToVerify,
            'portal_email_verified_at' => null,
            'portal_password' => Hash::make($request->password),
            'portal_registered_at' => now(),
        ])->save();

        if (!$this->sendEmailVerificationCode($employee, $emailToVerify)) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('existing_employee_email', $currentEmail)
                ->with('error', 'تعذر إرسال رمز التحقق إلى البريد الإلكتروني. تأكد من إعدادات البريد MAIL في ملف .env');
        }

        session([
            'employee_portal_pending_verification_id' => $employee->id,
        ]);

        return redirect()
            ->route('employee-portal.verify-email')
            ->with('success', 'تم إرسال رمز تحقق إلى البريد: ' . $emailToVerify);
    }

    public function showVerifyEmail()
    {
        if (!session('employee_portal_pending_verification_id')) {
            return redirect()->route('unified-login', ['account' => 'employee']);
        }

        return view('employee_portal.auth.verify_email');
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'رمز التحقق مطلوب',
            'code.digits' => 'رمز التحقق يجب أن يكون 6 أرقام',
        ]);

        $employee = Employee::find(session('employee_portal_pending_verification_id'));

        if (!$employee) {
            return redirect()
                ->route('unified-login', ['account' => 'employee'])
                ->with('error', 'انتهت جلسة التحقق، يرجى تسجيل الدخول مرة أخرى');
        }

        if (!$employee->portal_email_verification_code || !$employee->portal_email_verification_expires_at) {
            return back()->with('error', 'لا يوجد رمز تحقق فعال، أعد إرسال الرمز');
        }

        if (now()->greaterThan($employee->portal_email_verification_expires_at)) {
            return back()->with('error', 'انتهت صلاحية رمز التحقق، أعد إرسال الرمز');
        }

        if (!Hash::check($request->code, $employee->portal_email_verification_code)) {
            return back()->with('error', 'رمز التحقق غير صحيح');
        }

        /*
         * هنا فقط يتم اعتماد البريد الجديد واستبدال القديم.
         * قبل نجاح الرمز يبقى employees.email كما هو.
         */
        $emailToApply = $employee->portal_pending_email ?: $employee->email;

        $employee->forceFill([
            'email' => $emailToApply,
            'portal_pending_email' => null,
            'portal_email_verified_at' => now(),
            'portal_email_verification_code' => null,
            'portal_email_verification_expires_at' => null,
            'portal_last_login_at' => now(),
        ])->save();

        session()->forget('employee_portal_pending_verification_id');
        session(['employee_portal_id' => $employee->id]);

        return redirect()
            ->route('employee-portal.leave-requests.index')
            ->with('success', 'تم التحقق من البريد الإلكتروني بنجاح');
    }

    public function resendVerificationCode()
    {
        $employee = Employee::find(session('employee_portal_pending_verification_id'));

        if (!$employee) {
            return redirect()
                ->route('unified-login', ['account' => 'employee'])
                ->with('error', 'انتهت جلسة التحقق، يرجى تسجيل الدخول مرة أخرى');
        }

        $emailToVerify = $employee->portal_pending_email ?: $employee->email;

        if (!$this->sendEmailVerificationCode($employee, $emailToVerify)) {
            return back()->with('error', 'تعذر إرسال رمز التحقق. تأكد من إعدادات البريد MAIL في ملف .env');
        }

        return back()->with('success', 'تم إرسال رمز تحقق جديد إلى بريدك الإلكتروني');
    }

    public function showLogin()
    {
        return redirect()->route('unified-login', ['account' => 'employee']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'iqama_number' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [
            'iqama_number.required' => 'رقم الإقامة مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $employee = $this->findEmployeeByIqama($request->iqama_number);

        if (!$employee || !$employee->portal_password || !Hash::check($request->password, $employee->portal_password)) {
            return back()->withInput()->with('error', 'رقم الإقامة أو كلمة المرور غير صحيحة');
        }

        if (!$employee->email && !$employee->portal_pending_email) {
            return redirect()
                ->route('employee-portal.register')
                ->with('error', 'يجب إضافة بريد إلكتروني لحساب الموظف قبل الدخول');
        }

        if (!$employee->portal_email_verified_at) {
            $emailToVerify = $employee->portal_pending_email ?: $employee->email;

            if (!$this->sendEmailVerificationCode($employee, $emailToVerify)) {
                return back()->with('error', 'تعذر إرسال رمز التحقق. تأكد من إعدادات البريد MAIL في ملف .env');
            }

            session([
                'employee_portal_pending_verification_id' => $employee->id,
            ]);

            return redirect()
                ->route('employee-portal.verify-email')
                ->with('success', 'يجب التحقق من البريد الإلكتروني أولاً. تم إرسال رمز جديد إلى: ' . $emailToVerify);
        }

        $employee->forceFill([
            'portal_last_login_at' => now(),
        ])->save();

        session(['employee_portal_id' => $employee->id]);

        return redirect()->route('employee-portal.leave-requests.index');
    }

    public function showForgotPassword()
    {
        return view('employee_portal.auth.forgot_password');
    }

    public function sendPasswordResetCode(Request $request)
    {
        $request->validate([
            'iqama_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
        ], [
            'iqama_number.required' => 'رقم الإقامة مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
        ]);

        $employee = $this->findEmployeeByIqama($request->iqama_number);

        if (!$employee || !$employee->email || strtolower($employee->email) !== strtolower($request->email)) {
            return back()->withInput()->with('error', 'رقم الإقامة أو البريد الإلكتروني غير صحيح');
        }

        if (!$this->sendPasswordResetCodeToEmployee($employee)) {
            return back()->withInput()->with('error', 'تعذر إرسال رمز إعادة التعيين. تأكد من إعدادات البريد MAIL في ملف .env');
        }

        session([
            'employee_portal_password_reset_id' => $employee->id,
        ]);

        return redirect()
            ->route('employee-portal.reset-password')
            ->with('success', 'تم إرسال رمز إعادة تعيين كلمة المرور إلى البريد: ' . $employee->email);
    }

    public function showResetPassword()
    {
        if (!session('employee_portal_password_reset_id')) {
            return redirect()->route('employee-portal.forgot-password');
        }

        return view('employee_portal.auth.reset_password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'code.required' => 'رمز التحقق مطلوب',
            'code.digits' => 'رمز التحقق يجب أن يكون 6 أرقام',
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 6 أحرف',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق',
        ]);

        $employee = Employee::find(session('employee_portal_password_reset_id'));

        if (!$employee) {
            return redirect()->route('employee-portal.forgot-password')->with('error', 'انتهت جلسة إعادة التعيين');
        }

        if (!$employee->portal_password_reset_code || !$employee->portal_password_reset_expires_at) {
            return back()->with('error', 'لا يوجد رمز إعادة تعيين فعال');
        }

        if (now()->greaterThan($employee->portal_password_reset_expires_at)) {
            return back()->with('error', 'انتهت صلاحية الرمز، أعد إرسال رمز جديد');
        }

        if (!Hash::check($request->code, $employee->portal_password_reset_code)) {
            return back()->with('error', 'رمز التحقق غير صحيح');
        }

        $employee->forceFill([
            'portal_password' => Hash::make($request->password),
            'portal_password_reset_code' => null,
            'portal_password_reset_expires_at' => null,
        ])->save();

        session()->forget('employee_portal_password_reset_id');

        return redirect()
            ->route('unified-login', ['account' => 'employee'])
            ->with('success', 'تم تغيير كلمة المرور بنجاح، يمكنك تسجيل الدخول الآن');
    }

    public function logout()
    {
        session()->forget('employee_portal_id');
        session()->forget('employee_portal_pending_verification_id');
        session()->forget('employee_portal_password_reset_id');

        return redirect()
            ->route('unified-login', ['account' => 'employee'])
            ->with('success', 'تم تسجيل الخروج بنجاح');
    }

    private function sendEmailVerificationCode(Employee $employee, ?string $emailToVerify = null): bool
    {
        $emailToVerify = $emailToVerify ?: $employee->portal_pending_email ?: $employee->email;

        if (!$emailToVerify) {
            return false;
        }

        $code = (string) random_int(100000, 999999);

        $employee->forceFill([
            'portal_email_verification_code' => Hash::make($code),
            'portal_email_verification_expires_at' => now()->addMinutes(10),
        ])->save();

        return $this->sendCodeEmail(
            $emailToVerify,
            'رمز تحقق بوابة الموظف',
            "رمز التحقق الخاص بك هو: {$code}\n\nالرمز صالح لمدة 10 دقائق."
        );
    }

    private function sendPasswordResetCodeToEmployee(Employee $employee): bool
    {
        if (!$employee->email) {
            return false;
        }

        $code = (string) random_int(100000, 999999);

        $employee->forceFill([
            'portal_password_reset_code' => Hash::make($code),
            'portal_password_reset_expires_at' => now()->addMinutes(10),
        ])->save();

        return $this->sendCodeEmail(
            $employee->email,
            'رمز إعادة تعيين كلمة مرور بوابة الموظف',
            "رمز إعادة تعيين كلمة المرور هو: {$code}\n\nالرمز صالح لمدة 10 دقائق."
        );
    }

    private function sendCodeEmail(string $to, string $subject, string $body): bool
    {
        try {
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            return true;
        } catch (\Throwable $exception) {
            Log::error('Employee portal email failed', [
                'to' => $to,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function findEmployeeByIqama(string $iqamaNumber): ?Employee
    {
        $employee = Employee::whereHas('iqamas', function ($query) use ($iqamaNumber) {
            $query->where('iqama_number', $iqamaNumber);
        })->first();

        if ($employee) {
            return $employee;
        }

        foreach (['iqama_number', 'iqama_no', 'residency_number', 'national_id'] as $column) {
            if (Schema::hasColumn('employees', $column)) {
                return Employee::where($column, $iqamaNumber)->first();
            }
        }

        return null;
    }
}
