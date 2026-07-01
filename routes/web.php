<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Http\Request;
use App\Http\Controllers\DocumentTrackingController;
use App\Http\Controllers\NationalityController;
use App\Http\Controllers\LeaveBalanceController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\LeavePolicyController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\OfficialHolidayController;
use App\Http\Controllers\LeaveReportController;
use App\Http\Controllers\LeaveTransactionController;
use App\Http\Controllers\EmployeePortalAuthController;
use App\Http\Controllers\EmployeePortalLeaveRequestController;
use App\Http\Controllers\ManagerLeaveApprovalController;
use App\Http\Controllers\HrLeaveApprovalController;
use App\Http\Controllers\LeaveReportsHubController;
use App\Http\Controllers\SalaryAdvanceController;
use App\Http\Controllers\EmployeePortalSalaryAdvanceRequestController;
use App\Http\Controllers\ManagerSalaryAdvanceApprovalController;
use App\Http\Controllers\HrSalaryAdvanceApprovalController;
use App\Http\Controllers\EmployeeDeductionController;
use App\Http\Controllers\EmployeeSuspensionController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PayrollReportController;
use App\Http\Controllers\SalaryPaymentMethodController;
use App\Http\Controllers\PayrollGroupController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\DeductionTypeController;
use App\Http\Controllers\PayrollSettingController;
use App\Http\Controllers\PayrollPeriodLogController;
use App\Http\Controllers\PayrollBankTransferController;
use App\Http\Controllers\PayrollReportsHubController;
use App\Http\Controllers\PayrollBankTransferBatchController;
use App\Http\Controllers\SalaryAdvanceRequestAdminController;






Route::redirect('/', '/login');

Route::get('/dashboard', function () {

    $employeesCount = \App\Models\Employee::count();
    $activeEmployees = \App\Models\Employee::where('status', 'active')->count();
    $departmentsCount = \App\Models\Department::count();
    $positionsCount = \App\Models\Position::count();

    $todayAttendances = \App\Models\Attendance::whereDate('attendance_date', today())->count();
    $pendingLeaves = \App\Models\LeaveRequest::where('status', 'pending')->count();
    $paidPayrolls = \App\Models\Payroll::where('status', 'paid')->count();
    $draftPayrolls = \App\Models\Payroll::where('status', 'draft')->count();

    $employeesByDepartment = \App\Models\Department::withCount('employees')->get();

    $attendanceStatuses = \App\Models\Attendance::selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

    $leaveStatuses = \App\Models\LeaveRequest::selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

    $payrollTotals = \App\Models\Payroll::selectRaw('month, SUM(net_salary) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Documents Alerts Stats
    |--------------------------------------------------------------------------
    | These counts are used in the dashboard to show documents that are
    | near expiry or already expired.
    */

    $documentsStats = [
        'iqamas_near_expiry' => \App\Models\EmployeeIqama::where('document_status', 'near_expiry')->count(),
        'passports_near_expiry' => \App\Models\EmployeePassport::where('document_status', 'near_expiry')->count(),
        'health_cards_near_expiry' => \App\Models\EmployeeHealthCard::where('document_status', 'near_expiry')->count(),

        'iqamas_expired' => \App\Models\EmployeeIqama::where('document_status', 'expired')->count(),
        'passports_expired' => \App\Models\EmployeePassport::where('document_status', 'expired')->count(),
        'health_cards_expired' => \App\Models\EmployeeHealthCard::where('document_status', 'expired')->count(),
        'iqamas_valid' => \App\Models\EmployeeIqama::where('document_status', 'valid')->count(),
        'passports_valid' => \App\Models\EmployeePassport::where('document_status', 'valid')->count(),
        'health_cards_valid' => \App\Models\EmployeeHealthCard::where('document_status', 'valid')->count(),
    ];


    $documentsStats['total_near_expiry'] =
        $documentsStats['iqamas_near_expiry'] +
        $documentsStats['passports_near_expiry'] +
        $documentsStats['health_cards_near_expiry'];

    $documentsStats['total_expired'] =
        $documentsStats['iqamas_expired'] +
        $documentsStats['passports_expired'] +
        $documentsStats['health_cards_expired'];

    return view('dashboard', compact(
        'employeesCount',
        'activeEmployees',
        'departmentsCount',
        'positionsCount',
        'todayAttendances',
        'pendingLeaves',
        'paidPayrolls',
        'draftPayrolls',
        'employeesByDepartment',
        'attendanceStatuses',
        'leaveStatuses',
        'payrollTotals',
        'documentsStats'
    ));

})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Employee Portal Routes
|--------------------------------------------------------------------------
| هذه الروابط خاصة بالموظف ولا تعتمد على تسجيل دخول الأدمن
|--------------------------------------------------------------------------
*/

Route::prefix('employee-portal')->name('employee-portal.')->group(function () {
    Route::get('/register', [EmployeePortalAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [EmployeePortalAuthController::class, 'register'])->name('register.store');

    Route::get('/verify-email', [EmployeePortalAuthController::class, 'showVerifyEmail'])->name('verify-email');
    Route::post('/verify-email', [EmployeePortalAuthController::class, 'verifyEmail'])->name('verify-email.store');
    Route::post('/verify-email/resend', [EmployeePortalAuthController::class, 'resendVerificationCode'])->name('verify-email.resend');

    Route::get('/login', [EmployeePortalAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [EmployeePortalAuthController::class, 'login'])->name('login.store');

    Route::get('/forgot-password', [EmployeePortalAuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [EmployeePortalAuthController::class, 'sendPasswordResetCode'])->name('forgot-password.send');

    Route::get('/reset-password', [EmployeePortalAuthController::class, 'showResetPassword'])->name('reset-password');
    Route::post('/reset-password', [EmployeePortalAuthController::class, 'resetPassword'])->name('reset-password.store');

    Route::post('/logout', [EmployeePortalAuthController::class, 'logout'])->name('logout');

    Route::get('/leave-requests', [EmployeePortalLeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('/leave-requests/create', [EmployeePortalLeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/leave-requests', [EmployeePortalLeaveRequestController::class, 'store'])->name('leave-requests.store');

    /*
    |--------------------------------------------------------------------------
    | Salary Advance Requests - Employee Portal
    |--------------------------------------------------------------------------
    */
    Route::get('/salary-advance-requests', [EmployeePortalSalaryAdvanceRequestController::class, 'index'])->name('salary-advance-requests.index');
    Route::get('/salary-advance-requests/create', [EmployeePortalSalaryAdvanceRequestController::class, 'create'])->name('salary-advance-requests.create');
    Route::post('/salary-advance-requests', [EmployeePortalSalaryAdvanceRequestController::class, 'store'])->name('salary-advance-requests.store');
    Route::get('/salary-advance-requests/{salaryAdvanceRequest}', [EmployeePortalSalaryAdvanceRequestController::class, 'show'])->name('salary-advance-requests.show');
    Route::post('/salary-advance-requests/{salaryAdvanceRequest}/cancel', [EmployeePortalSalaryAdvanceRequestController::class, 'cancel'])->name('salary-advance-requests.cancel');

    /*
    |--------------------------------------------------------------------------
    | Employee Portal Leave Request Show Route

    */
    Route::get('/leave-requests/{leaveRequest}', [EmployeePortalLeaveRequestController::class, 'show'])
        ->name('leave-requests.show');

    Route::post('/leave-requests/{leaveRequest}/cancel', [EmployeePortalLeaveRequestController::class, 'cancel'])
        ->name('leave-requests.cancel');
});


Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Departments
    |--------------------------------------------------------------------------
    */

    Route::get('/departments', [DepartmentController::class, 'index'])
        ->middleware('permission:departments.view')
        ->name('departments.index');

    Route::get('/departments/create', [DepartmentController::class, 'create'])
        ->middleware('permission:departments.create')
        ->name('departments.create');

    Route::post('/departments', [DepartmentController::class, 'store'])
        ->middleware('permission:departments.create')
        ->name('departments.store');

    Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])
        ->middleware('permission:departments.edit')
        ->name('departments.edit');

    Route::put('/departments/{department}', [DepartmentController::class, 'update'])
        ->middleware('permission:departments.edit')
        ->name('departments.update');

    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])
        ->middleware('permission:departments.delete')
        ->name('departments.destroy');


    /*
    |--------------------------------------------------------------------------
    | Positions
    |--------------------------------------------------------------------------
    */

    Route::get('/positions', [PositionController::class, 'index'])
        ->middleware('permission:positions.view')
        ->name('positions.index');

    Route::get('/positions/create', [PositionController::class, 'create'])
        ->middleware('permission:positions.create')
        ->name('positions.create');

    Route::post('/positions', [PositionController::class, 'store'])
        ->middleware('permission:positions.create')
        ->name('positions.store');

    Route::get('/positions/{position}/edit', [PositionController::class, 'edit'])
        ->middleware('permission:positions.edit')
        ->name('positions.edit');

    Route::put('/positions/{position}', [PositionController::class, 'update'])
        ->middleware('permission:positions.edit')
        ->name('positions.update');

    Route::delete('/positions/{position}', [PositionController::class, 'destroy'])
        ->middleware('permission:positions.delete')
        ->name('positions.destroy');


    /*
    |--------------------------------------------------------------------------
    | Nationalities
    |--------------------------------------------------------------------------
    */

    Route::get('/nationalities', [NationalityController::class, 'index'])
        ->middleware('permission:nationalities.view')
        ->name('nationalities.index');

    Route::get('/nationalities/create', [NationalityController::class, 'create'])
        ->middleware('permission:nationalities.create')
        ->name('nationalities.create');

    Route::post('/nationalities', [NationalityController::class, 'store'])
        ->middleware('permission:nationalities.create')
        ->name('nationalities.store');

    Route::get('/nationalities/{nationality}/edit', [NationalityController::class, 'edit'])
        ->middleware('permission:nationalities.edit')
        ->name('nationalities.edit');

    Route::put('/nationalities/{nationality}', [NationalityController::class, 'update'])
        ->middleware('permission:nationalities.edit')
        ->name('nationalities.update');

    Route::patch('/nationalities/{nationality}/toggle-status', [NationalityController::class, 'toggleStatus'])
        ->middleware('permission:nationalities.edit')
        ->name('nationalities.toggle-status');

    Route::delete('/nationalities/{nationality}', [NationalityController::class, 'destroy'])
        ->middleware('permission:nationalities.delete')
        ->name('nationalities.destroy');

    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('permission:employees.view')
        ->name('employees.index');

    Route::get('/employees/create', [EmployeeController::class, 'create'])
        ->middleware('permission:employees.create')
        ->name('employees.create');

    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('permission:employees.create')
        ->name('employees.store');

    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])
        ->middleware('permission:employees.show')
        ->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])
        ->middleware('permission:employees.edit')
        ->name('employees.edit');

    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])
        ->middleware('permission:employees.edit')
        ->name('employees.update');

    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])
        ->middleware('permission:employees.delete')
        ->name('employees.destroy');



    /*
|--------------------------------------------------------------------------
| Employee Iqamas
|--------------------------------------------------------------------------
*/

    Route::get('/employees/{employee}/iqamas/create', [EmployeeController::class, 'createIqama'])
        ->middleware('permission:employees.edit')
        ->name('employees.iqamas.create');

    Route::post('/employees/{employee}/iqamas', [EmployeeController::class, 'storeIqama'])
        ->middleware('permission:employees.edit')
        ->name('employees.iqamas.store');
    /*
    |--------------------------------------------------------------------------
    | Attendances
    |--------------------------------------------------------------------------
    */

    Route::get('/attendances', [AttendanceController::class, 'index'])
        ->middleware('permission:attendances.view')
        ->name('attendances.index');

    Route::get('/attendances/create', [AttendanceController::class, 'create'])
        ->middleware('permission:attendances.create')
        ->name('attendances.create');

    Route::post('/attendances', [AttendanceController::class, 'store'])
        ->middleware('permission:attendances.create')
        ->name('attendances.store');

    Route::get('/attendances/{attendance}/edit', [AttendanceController::class, 'edit'])
        ->middleware('permission:attendances.edit')
        ->name('attendances.edit');

    Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])
        ->middleware('permission:attendances.edit')
        ->name('attendances.update');

    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])
        ->middleware('permission:attendances.delete')
        ->name('attendances.destroy');



    /*
|--------------------------------------------------------------------------
| Document Tracking
|--------------------------------------------------------------------------
*/

    Route::get('/documents-tracking', [DocumentTrackingController::class, 'index'])
        ->middleware('permission:documents.view')
        ->name('documents.index');

    Route::get('/documents-tracking/export', [DocumentTrackingController::class, 'export'])
        ->middleware('permission:documents.export')
        ->name('documents.export');

    Route::get('/documents-tracking/refresh', [DocumentTrackingController::class, 'refresh'])
        ->middleware('permission:documents.view')
        ->name('documents.refresh');
    /*
    |--------------------------------------------------------------------------
    | Leave Requests
    |--------------------------------------------------------------------------
    */

    Route::get('/leave-requests', [LeaveRequestController::class, 'index'])
        ->middleware('permission:leave_requests.view')
        ->name('leave-requests.index');

    Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])
        ->middleware('permission:leave_requests.create')
        ->name('leave-requests.create');

    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])
        ->middleware('permission:leave_requests.create')
        ->name('leave-requests.store');

    Route::get('/leave-requests/{leaveRequest}/edit', [LeaveRequestController::class, 'edit'])
        ->middleware('permission:leave_requests.edit')
        ->name('leave-requests.edit');

    Route::put('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'update'])
        ->middleware('permission:leave_requests.edit')
        ->name('leave-requests.update');

    Route::post('/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])
        ->middleware('permission:leave_requests.approve')
        ->name('leave-requests.approve');

    Route::post('/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])
        ->middleware('permission:leave_requests.reject')
        ->name('leave-requests.reject');

    Route::delete('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'destroy'])
        ->middleware('permission:leave_requests.delete')
        ->name('leave-requests.destroy');
    /*
        |--------------------------------------------------------------------------
        | الغاء طلب اجازه معتمده
        |--------------------------------------------------------------------------
        */

    Route::post('/leave-requests/{leaveRequest}/cancel-approved', [LeaveRequestController::class, 'cancelApproved'])
        ->middleware('permission:leave_requests.cancel')
        ->name('leave-requests.cancel-approved');

    /*
            |--------------------------------------------------------------------------
            | الإجازات الرسمية
            |--------------------------------------------------------------------------
            */



    Route::get('/official-holidays', [OfficialHolidayController::class, 'index'])
        ->middleware('permission:official_holidays.view')
        ->name('official-holidays.index');

    Route::get('/official-holidays/create', [OfficialHolidayController::class, 'create'])
        ->middleware('permission:official_holidays.create')
        ->name('official-holidays.create');

    Route::post('/official-holidays', [OfficialHolidayController::class, 'store'])
        ->middleware('permission:official_holidays.create')
        ->name('official-holidays.store');

    Route::get('/official-holidays/{officialHoliday}/edit', [OfficialHolidayController::class, 'edit'])
        ->middleware('permission:official_holidays.edit')
        ->name('official-holidays.edit');

    Route::put('/official-holidays/{officialHoliday}', [OfficialHolidayController::class, 'update'])
        ->middleware('permission:official_holidays.edit')
        ->name('official-holidays.update');

    Route::delete('/official-holidays/{officialHoliday}', [OfficialHolidayController::class, 'destroy'])
        ->middleware('permission:official_holidays.delete')
        ->name('official-holidays.destroy');

    /*
                |--------------------------------------------------------------------------
                تقرير الاجازات
                |--------------------------------------------------------------------------
                */




    Route::get('/leave-reports', [LeaveReportController::class, 'index'])
        ->middleware('permission:leave_reports.view')
        ->name('leave-reports.index');

    Route::get('/leave-reports/export-excel', [LeaveReportController::class, 'exportExcel'])
        ->middleware('permission:leave_reports.export')
        ->name('leave-reports.export-excel');

    Route::get('/leave-reports/print-pdf', [LeaveReportController::class, 'printPdf'])
        ->middleware('permission:leave_reports.export')
        ->name('leave-reports.print-pdf');

    /*
                    |--------------------------------------------------------------------------
                    سجل حركات الاجازات
                    |--------------------------------------------------------------------------
                    */
    Route::get('/leave-transactions', [LeaveTransactionController::class, 'index'])
        ->middleware('permission:leave_transactions.view')
        ->name('leave-transactions.index');

    Route::get('/leave-transactions/export', [LeaveTransactionController::class, 'export'])
        ->middleware('permission:leave_transactions.export')
        ->name('leave-transactions.export');

    /*
    |--------------------------------------------------------------------------
    | Payrolls
    |--------------------------------------------------------------------------
    */

    Route::get('/payrolls', [PayrollController::class, 'index'])
        ->middleware('permission:payrolls.view')
        ->name('payrolls.index');

    Route::get('/payrolls/create', [PayrollController::class, 'create'])
        ->middleware('permission:payrolls.create')
        ->name('payrolls.create');

    Route::post('/payrolls', [PayrollController::class, 'store'])
        ->middleware('permission:payrolls.create')
        ->name('payrolls.store');

    Route::get('/payrolls/{payroll}/edit', [PayrollController::class, 'edit'])
        ->middleware('permission:payrolls.edit')
        ->name('payrolls.edit');

    Route::put('/payrolls/{payroll}', [PayrollController::class, 'update'])
        ->middleware('permission:payrolls.edit')
        ->name('payrolls.update');

    Route::delete('/payrolls/{payroll}', [PayrollController::class, 'destroy'])
        ->middleware('permission:payrolls.delete')
        ->name('payrolls.destroy');


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission:reports.view')
        ->name('reports.index');


    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('users.index');

    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:users.create')
        ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('users.store');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users.edit')
        ->name('users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:users.edit')
        ->name('users.update');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('users.destroy');


    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.view')
        ->name('roles.index');

    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('permission:roles.create')
        ->name('roles.create');

    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:roles.create')
        ->name('roles.store');

    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:roles.edit')
        ->name('roles.edit');

    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:roles.edit')
        ->name('roles.update');

    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:roles.delete')
        ->name('roles.destroy');


    /*
       |--------------------------------------------------------------------------
       احتساب الاجازات
       |--------------------------------------------------------------------------
       */

    Route::get('/leave-balances', [LeaveBalanceController::class, 'index'])
        ->middleware('permission:leave_balances.view')
        ->name('leave-balances.index');

    Route::get('/leave-balances/{employee}', [LeaveBalanceController::class, 'show'])
        ->middleware('permission:leave_balances.view')
        ->name('leave-balances.show');

    Route::post('/leave-balances/recalculate', function () {
        abort_if(!auth()->user()->hasPermission('leave_balances.recalculate'), 403);

        Artisan::call('leaves:calculate-balances', [
            '--force' => true,
        ]);

        return redirect()
            ->route('leave-balances.index')
            ->with('success', 'تمت إعادة احتساب أرصدة الإجازات بنجاح');
    })->name('leave-balances.recalculate');

    /*
          |--------------------------------------------------------------------------
         سياسات الإجازات
          |--------------------------------------------------------------------------
          */



    Route::get('/leave-policies', [LeavePolicyController::class, 'index'])
        ->middleware('permission:leave_policies.view')
        ->name('leave-policies.index');

    Route::get('/leave-policies/create', [LeavePolicyController::class, 'create'])
        ->middleware('permission:leave_policies.create')
        ->name('leave-policies.create');

    Route::post('/leave-policies', [LeavePolicyController::class, 'store'])
        ->middleware('permission:leave_policies.create')
        ->name('leave-policies.store');

    Route::get('/leave-policies/{leavePolicy}/edit', [LeavePolicyController::class, 'edit'])
        ->middleware('permission:leave_policies.edit')
        ->name('leave-policies.edit');

    Route::put('/leave-policies/{leavePolicy}', [LeavePolicyController::class, 'update'])
        ->middleware('permission:leave_policies.edit')
        ->name('leave-policies.update');

    Route::patch('/leave-policies/{leavePolicy}/activate', [LeavePolicyController::class, 'activate'])
        ->middleware('permission:leave_policies.edit')
        ->name('leave-policies.activate');

    /*
             |--------------------------------------------------------------------------
            انواع الإجازات
             |--------------------------------------------------------------------------
             */



    Route::get('/leave-types', [LeaveTypeController::class, 'index'])
        ->middleware('permission:leave_types.view')
        ->name('leave-types.index');

    Route::get('/leave-types/create', [LeaveTypeController::class, 'create'])
        ->middleware('permission:leave_types.create')
        ->name('leave-types.create');

    Route::post('/leave-types', [LeaveTypeController::class, 'store'])
        ->middleware('permission:leave_types.create')
        ->name('leave-types.store');

    Route::get('/leave-types/{leaveType}/edit', [LeaveTypeController::class, 'edit'])
        ->middleware('permission:leave_types.edit')
        ->name('leave-types.edit');

    Route::put('/leave-types/{leaveType}', [LeaveTypeController::class, 'update'])
        ->middleware('permission:leave_types.edit')
        ->name('leave-types.update');

    Route::patch('/leave-types/{leaveType}/toggle-status', [LeaveTypeController::class, 'toggleStatus'])
        ->middleware('permission:leave_types.edit')
        ->name('leave-types.toggle-status');

    Route::delete('/leave-types/{leaveType}', [LeaveTypeController::class, 'destroy'])
        ->middleware('permission:leave_types.delete')
        ->name('leave-types.destroy');



    /*
                 |--------------------------------------------------------------------------
            تسجيل موظف
                 |--------------------------------------------------------------------------
                 */
    /*
                     |--------------------------------------------------------------------------
                    موافقه المدير المباشر على طلب الاجازه
                     |--------------------------------------------------------------------------
                     */


    Route::get('/manager-leave-approvals', [ManagerLeaveApprovalController::class, 'index'])
        ->middleware('permission:leave_requests.manager_approval')
        ->name('manager-leave-approvals.index');

    Route::post('/manager-leave-approvals/{leaveRequest}/approve', [ManagerLeaveApprovalController::class, 'approve'])
        ->middleware('permission:leave_requests.manager_approval')
        ->name('manager-leave-approvals.approve');

    Route::post('/manager-leave-approvals/{leaveRequest}/reject', [ManagerLeaveApprovalController::class, 'reject'])
        ->middleware('permission:leave_requests.manager_approval')
        ->name('manager-leave-approvals.reject');


    /*
                |--------------------------------------------------------------------------
               موافقه الموارد على طلب الاجازه
                |--------------------------------------------------------------------------
                */


    Route::get('/hr-leave-approvals', [HrLeaveApprovalController::class, 'index'])
        ->middleware('permission:leave_requests.hr_approval')
        ->name('hr-leave-approvals.index');

    Route::post('/hr-leave-approvals/{leaveRequest}/approve', [HrLeaveApprovalController::class, 'approve'])
        ->middleware('permission:leave_requests.hr_approval')
        ->name('hr-leave-approvals.approve');

    Route::post('/hr-leave-approvals/{leaveRequest}/reject', [HrLeaveApprovalController::class, 'reject'])
        ->middleware('permission:leave_requests.hr_approval')
        ->name('hr-leave-approvals.reject');
    /*
|--------------------------------------------------------------------------
| تقارير إدارة الإجازات
|--------------------------------------------------------------------------

*/

    Route::get('/leave-reports-hub', [LeaveReportsHubController::class, 'index'])
        ->name('leave-reports.hub');


    Route::get('/leave-reports-hub/export/{reportKey}', [LeaveReportsHubController::class, 'exportExcel'])
        ->name('leave-reports.hub.export');




    /*
    |--------------------------------------------------------------------------
    | Payroll Phase 2: Deductions & Suspensions
    |--------------------------------------------------------------------------
    |
    */

    Route::get('/employee-deductions', [EmployeeDeductionController::class, 'index'])->middleware('permission:employee_deductions.view')->name('employee-deductions.index');
    Route::get('/employee-deductions/create', [EmployeeDeductionController::class, 'create'])->middleware('permission:employee_deductions.create')->name('employee-deductions.create');
    Route::post('/employee-deductions', [EmployeeDeductionController::class, 'store'])->middleware('permission:employee_deductions.create')->name('employee-deductions.store');
    Route::post('/employee-deductions/{employeeDeduction}/approve', [EmployeeDeductionController::class, 'approve'])->middleware('permission:employee_deductions.approve')->name('employee-deductions.approve');
    Route::post('/employee-deductions/{employeeDeduction}/cancel', [EmployeeDeductionController::class, 'cancel'])->middleware('permission:employee_deductions.cancel')->name('employee-deductions.cancel');

    Route::get('/employee-suspensions', [EmployeeSuspensionController::class, 'index'])->middleware('permission:employee_suspensions.view')->name('employee-suspensions.index');
    Route::get('/employee-suspensions/create', [EmployeeSuspensionController::class, 'create'])->middleware('permission:employee_suspensions.create')->name('employee-suspensions.create');
    Route::post('/employee-suspensions', [EmployeeSuspensionController::class, 'store'])->middleware('permission:employee_suspensions.create')->name('employee-suspensions.store');
    Route::post('/employee-suspensions/{employeeSuspension}/resume', [EmployeeSuspensionController::class, 'resume'])->middleware('permission:employee_suspensions.resume')->name('employee-suspensions.resume');
    Route::post('/employee-suspensions/{employeeSuspension}/cancel', [EmployeeSuspensionController::class, 'cancel'])->middleware('permission:employee_suspensions.cancel')->name('employee-suspensions.cancel');



    /*
    |--------------------------------------------------------------------------
    | Payroll Phase 3 V3: Salary Advances Select Deduction Months
    |--------------------------------------------------------------------------

    */

    Route::get('/salary-advances', [SalaryAdvanceController::class, 'index'])->middleware('permission:salary_advances.view')->name('salary-advances.index');
    Route::get('/salary-advances/create', [SalaryAdvanceController::class, 'create'])->middleware('permission:salary_advances.create')->name('salary-advances.create');
    Route::post('/salary-advances', [SalaryAdvanceController::class, 'store'])->middleware('permission:salary_advances.create')->name('salary-advances.store');
    Route::get('/salary-advances/{salaryAdvance}', [SalaryAdvanceController::class, 'show'])->middleware('permission:salary_advances.view')->name('salary-advances.show');
    Route::get('/salary-advances/{salaryAdvance}/edit', [SalaryAdvanceController::class, 'edit'])->middleware('permission:salary_advances.edit')->name('salary-advances.edit');
    Route::put('/salary-advances/{salaryAdvance}', [SalaryAdvanceController::class, 'update'])->middleware('permission:salary_advances.edit')->name('salary-advances.update');
    Route::post('/salary-advances/{salaryAdvance}/approve', [SalaryAdvanceController::class, 'approve'])->middleware('permission:salary_advances.approve')->name('salary-advances.approve');
    Route::post('/salary-advances/{salaryAdvance}/cancel', [SalaryAdvanceController::class, 'cancel'])->middleware('permission:salary_advances.cancel')->name('salary-advances.cancel');

    Route::get('/manager-salary-advance-approvals', [ManagerSalaryAdvanceApprovalController::class, 'index'])
        ->middleware('permission:salary_advance_requests.manager_approval')
        ->name('manager-salary-advance-approvals.index');

    Route::post('/manager-salary-advance-approvals/{salaryAdvanceRequest}/approve', [ManagerSalaryAdvanceApprovalController::class, 'approve'])
        ->middleware('permission:salary_advance_requests.manager_approval')
        ->name('manager-salary-advance-approvals.approve');

    Route::post('/manager-salary-advance-approvals/{salaryAdvanceRequest}/reject', [ManagerSalaryAdvanceApprovalController::class, 'reject'])
        ->middleware('permission:salary_advance_requests.manager_approval')
        ->name('manager-salary-advance-approvals.reject');

    Route::get('/hr-salary-advance-approvals', [HrSalaryAdvanceApprovalController::class, 'index'])
        ->middleware('permission:salary_advance_requests.hr_approval')
        ->name('hr-salary-advance-approvals.index');

    Route::post('/hr-salary-advance-approvals/{salaryAdvanceRequest}/approve', [HrSalaryAdvanceApprovalController::class, 'approve'])
        ->middleware('permission:salary_advance_requests.hr_approval')
        ->name('hr-salary-advance-approvals.approve');

    Route::post('/hr-salary-advance-approvals/{salaryAdvanceRequest}/reject', [HrSalaryAdvanceApprovalController::class, 'reject'])
        ->middleware('permission:salary_advance_requests.hr_approval')
        ->name('hr-salary-advance-approvals.reject');



    /*
    |--------------------------------------------------------------------------
    | Payroll Phase 4: Payroll Periods & Salary Calculation
    |--------------------------------------------------------------------------

    */

    Route::get('/payroll-periods', [PayrollPeriodController::class, 'index'])
        ->middleware('permission:payroll_periods.view')
        ->name('payroll-periods.index');

    Route::get('/payroll-periods/create', [PayrollPeriodController::class, 'create'])
        ->middleware('permission:payroll_periods.create')
        ->name('payroll-periods.create');

    Route::post('/payroll-periods', [PayrollPeriodController::class, 'store'])
        ->middleware('permission:payroll_periods.create')
        ->name('payroll-periods.store');

    Route::get('/payroll-periods/{payrollPeriod}', [PayrollPeriodController::class, 'show'])
        ->middleware('permission:payroll_periods.view')
        ->name('payroll-periods.show');

    Route::post('/payroll-periods/{payrollPeriod}/calculate', [PayrollPeriodController::class, 'calculate'])
        ->middleware('permission:payroll_periods.calculate')
        ->name('payroll-periods.calculate');

    Route::post('/payroll-periods/{payrollPeriod}/approve', [PayrollPeriodController::class, 'approve'])
        ->middleware('permission:payroll_periods.approve')
        ->name('payroll-periods.approve');

    Route::post('/payroll-periods/{payrollPeriod}/mark-paid', [PayrollPeriodController::class, 'markAsPaid'])
        ->middleware('permission:payroll_periods.pay')
        ->name('payroll-periods.mark-paid');

    Route::delete('/payroll-periods/{payrollPeriod}', [PayrollPeriodController::class, 'destroy'])
        ->middleware('permission:payroll_periods.delete')
        ->name('payroll-periods.destroy');



    /*
    |--------------------------------------------------------------------------
    | Payroll Reports & Payslips Routes
    |--------------------------------------------------------------------------

    */

    Route::get('/payroll-reports', [PayrollReportController::class, 'index'])
        ->middleware('permission:payroll_reports.view')
        ->name('payroll-reports.index');

    Route::get('/payroll-reports/periods/{payrollPeriod}', [PayrollReportController::class, 'show'])
        ->middleware('permission:payroll_reports.view')
        ->name('payroll-reports.show');

    Route::get('/payroll-reports/periods/{payrollPeriod}/export-excel', [PayrollReportController::class, 'exportExcel'])
        ->middleware('permission:payroll_reports.export')
        ->name('payroll-reports.export-excel');

    Route::get('/payroll-reports/periods/{payrollPeriod}/print-pdf', [PayrollReportController::class, 'printPdf'])
        ->middleware('permission:payroll_reports.export')
        ->name('payroll-reports.print-pdf');

    Route::get('/payroll-reports/items/{payrollItem}/payslip', [PayrollReportController::class, 'payslip'])
        ->middleware('permission:payroll_reports.payslip')
        ->name('payroll-reports.payslip');




    /*
    |--------------------------------------------------------------------------
    | Salary Payment Methods Routes
    |--------------------------------------------------------------------------

    */

    Route::resource('salary-payment-methods', SalaryPaymentMethodController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:salary_payment_methods.view',
            'create' => 'permission:salary_payment_methods.create',
            'store' => 'permission:salary_payment_methods.create',
            'edit' => 'permission:salary_payment_methods.edit',
            'update' => 'permission:salary_payment_methods.edit',
            'destroy' => 'permission:salary_payment_methods.delete',
        ]);

    /*
       |--------------------------------------------------------------------------
       | مركز التكلفه ومجموعات الرواتب

       |--------------------------------------------------------------------------
       */


    Route::resource('payroll-groups', PayrollGroupController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:payroll_groups.view',
            'create' => 'permission:payroll_groups.create',
            'store' => 'permission:payroll_groups.create',
            'edit' => 'permission:payroll_groups.edit',
            'update' => 'permission:payroll_groups.edit',
            'destroy' => 'permission:payroll_groups.delete',
        ]);

    Route::resource('cost-centers', CostCenterController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:cost_centers.view',
            'create' => 'permission:cost_centers.create',
            'store' => 'permission:cost_centers.create',
            'edit' => 'permission:cost_centers.edit',
            'update' => 'permission:cost_centers.edit',
            'destroy' => 'permission:cost_centers.delete',
        ]);




    /*
       |--------------------------------------------------------------------------
       | deduction_types
       |--------------------------------------------------------------------------
       */


    Route::resource('deduction-types', DeductionTypeController::class)
        ->except(['show']);


    Route::get('employee-deductions/{employeeDeduction}/edit', [EmployeeDeductionController::class, 'edit'])
        ->name('employee-deductions.edit');

    Route::put('employee-deductions/{employeeDeduction}', [EmployeeDeductionController::class, 'update'])
        ->name('employee-deductions.update');

    /*
       |--------------------------------------------------------------------------
       | payroll_settings
       |--------------------------------------------------------------------------
       */

    Route::get('payroll-settings', [PayrollSettingController::class, 'edit'])
        ->name('payroll-settings.edit');

    Route::put('payroll-settings', [PayrollSettingController::class, 'update'])
        ->name('payroll-settings.update');

    /*
       |--------------------------------------------------------------------------
       |إلغاء اعتماد مسير الرواتب
       |--------------------------------------------------------------------------
       */
// إلغاء اعتماد مسير الرواتب - فقط قبل الصرف
    Route::post('/payroll-periods/{payrollPeriod}/cancel-approval', [PayrollPeriodController::class, 'cancelApproval'])
        ->name('payroll-periods.cancel-approval');


    /*
       |--------------------------------------------------------------------------
       | logs PayrollPeriod
       |--------------------------------------------------------------------------
       */

    Route::get('/payroll-period-logs', [PayrollPeriodLogController::class, 'index'])
        ->name('payroll-period-logs.index');
    Route::get('/payroll-period-logs', [PayrollPeriodLogController::class, 'index'])
        ->name('payroll-period-logs.index');

    Route::get('/payroll-period-logs/export-excel', [PayrollPeriodLogController::class, 'exportExcel'])
        ->name('payroll-period-logs.export-excel');

    Route::get('/payroll-period-logs/print-pdf', [PayrollPeriodLogController::class, 'printPdf'])
        ->name('payroll-period-logs.print-pdf');

    /*
       |--------------------------------------------------------------------------
       | payroll_bank_transfers
       |--------------------------------------------------------------------------
       */


    Route::get('/payroll-bank-transfers', [PayrollBankTransferController::class, 'index'])
        ->name('payroll-bank-transfers.index');

    Route::get('/payroll-bank-transfers/{payrollPeriod}/export-excel', [PayrollBankTransferController::class, 'exportExcel'])
        ->name('payroll-bank-transfers.export-excel');
    Route::get('/payroll-bank-transfers/{payrollPeriod}/export-csv', [PayrollBankTransferController::class, 'exportCsv'])
        ->name('payroll-bank-transfers.export-csv');
    Route::get('/payroll-bank-transfers/{payrollPeriod}/print-pdf', [PayrollBankTransferController::class, 'printPdf'])
        ->name('payroll-bank-transfers.print-pdf');


    /*
           |--------------------------------------------------------------------------
           |SalaryAdvanceRequest
           |--------------------------------------------------------------------------
           */

    Route::get('/salary-advance-requests', [SalaryAdvanceRequestAdminController::class, 'index'])
        ->middleware('permission:salary_advance_requests.view_all')
        ->name('salary-advance-requests.index');

    Route::get('/salary-advance-requests/{salaryAdvanceRequest}', [SalaryAdvanceRequestAdminController::class, 'show'])
        ->middleware('permission:salary_advance_requests.view_all')
        ->name('salary-advance-requests.show');



    /*
       |--------------------------------------------------------------------------
       |payroll_bank_transfer_batches
       |--------------------------------------------------------------------------
       */

    Route::get('/payroll-bank-transfer-batches', [PayrollBankTransferBatchController::class, 'index'])
        ->name('payroll-bank-transfer-batches.index');

    Route::post('/payroll-bank-transfer-batches', [PayrollBankTransferBatchController::class, 'store'])
        ->name('payroll-bank-transfer-batches.store');

    Route::post('/payroll-bank-transfer-batches/{batch}/mark-sent', [PayrollBankTransferBatchController::class, 'markSent'])
        ->name('payroll-bank-transfer-batches.mark-sent');

    Route::post('/payroll-bank-transfer-batches/{batch}/confirm', [PayrollBankTransferBatchController::class, 'confirm'])
        ->name('payroll-bank-transfer-batches.confirm');

    Route::post('/payroll-bank-transfer-batches/{batch}/cancel', [PayrollBankTransferBatchController::class, 'cancel'])
        ->name('payroll-bank-transfer-batches.cancel');

    Route::get('/payroll-bank-transfer-batches', [PayrollBankTransferBatchController::class, 'index'])
        ->name('payroll-bank-transfer-batches.index');

    Route::post('/payroll-bank-transfer-batches', [PayrollBankTransferBatchController::class, 'store'])
        ->name('payroll-bank-transfer-batches.store');

    Route::get('/payroll-bank-transfer-batches/{batch}', [PayrollBankTransferBatchController::class, 'show'])
        ->name('payroll-bank-transfer-batches.show');

    Route::get('/payroll-bank-transfer-batches/{batch}/export-excel', [PayrollBankTransferBatchController::class, 'exportExcel'])
        ->name('payroll-bank-transfer-batches.export-excel');

    Route::get('/payroll-bank-transfer-batches/{batch}/export-csv', [PayrollBankTransferBatchController::class, 'exportCsv'])
        ->name('payroll-bank-transfer-batches.export-csv');

    Route::get('/payroll-bank-transfer-batches/{batch}/print-pdf', [PayrollBankTransferBatchController::class, 'printPdf'])
        ->name('payroll-bank-transfer-batches.print-pdf');

    Route::post('/payroll-bank-transfer-batches/{batch}/mark-sent', [PayrollBankTransferBatchController::class, 'markSent'])
        ->name('payroll-bank-transfer-batches.mark-sent');

    Route::post('/payroll-bank-transfer-batches/{batch}/confirm', [PayrollBankTransferBatchController::class, 'confirm'])
        ->name('payroll-bank-transfer-batches.confirm');

    Route::post('/payroll-bank-transfer-batches/{batch}/cancel', [PayrollBankTransferBatchController::class, 'cancel'])
        ->name('payroll-bank-transfer-batches.cancel');



    Route::get('/payroll-reports-hub', [PayrollReportsHubController::class, 'index'])
        ->name('payroll-reports-hub.index');

    Route::get('/payroll-reports-hub/export-excel', [PayrollReportsHubController::class, 'exportExcel'])
        ->name('payroll-reports-hub.export-excel');

    Route::get('/payroll-reports-hub/print-pdf', [PayrollReportsHubController::class, 'printPdf'])
        ->name('payroll-reports-hub.print-pdf');

    /*
   |--------------------------------------------------------------------------
   | audit-logs
   |--------------------------------------------------------------------------
   */

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('permission:audit_logs.view')
        ->name('audit-logs.index');
    Route::post('/audit-logs/export-action', function (Request $request) {
        audit_log(
            'export',
            $request->module ?? 'unknown',
            null,
            null,
            'تم تصدير بيانات من النظام',
            null,
            [
                'type' => $request->type,
                'page' => $request->page,
            ]
        );

        return response()->json([
            'success' => true,
        ]);
    })->name('audit-logs.export-action');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])
        ->middleware('permission:audit_logs.view')
        ->name('audit-logs.show');
});

require __DIR__.'/auth.php';
