<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeLeaveTransaction;
use App\Models\LeavePolicy;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\OfficialHoliday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.view'), 403);

        $query = LeaveRequest::with([
            'employee.department',
            'employee.currentLeaveBalance',
            'leaveType',
            'approvedBy',
            'rejectedBy',
            'directManagerApprovedBy',
            'directManagerRejectedBy',
            'hrApprovedBy',
            'hrRejectedBy',
        ]);

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->leave_type_id) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->date_from) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaveRequests = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
            'cancelled' => LeaveRequest::where('status', 'cancelled')->count(),
        ];

        $employees = Employee::with('currentLeaveBalance')->where('status', 'active')->orderBy('full_name')->get();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('leave_requests.index', compact(
            'leaveRequests',
            'employees',
            'leaveTypes',
            'stats'
        ));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.create'), 403);

        $employees = Employee::with('currentLeaveBalance')->where('status', 'active')->orderBy('full_name')->get();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('leave_requests.create', compact(
            'employees',
            'leaveTypes'
        ));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.create'), 403);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'leave_type_id.required' => 'نوع الإجازة مطلوب',
            'start_date.required' => 'تاريخ بداية الإجازة مطلوب',
            'end_date.required' => 'تاريخ نهاية الإجازة مطلوب',
            'end_date.after_or_equal' => 'تاريخ نهاية الإجازة يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        if (!$leaveType->is_active) {
            return back()
                ->withErrors(['leave_type_id' => 'نوع الإجازة غير مفعل'])
                ->withInput();
        }

        if ($this->hasOverlappingLeaveRequest(
            (int) $request->employee_id,
            $request->start_date,
            $request->end_date
        )) {
            return back()
                ->withErrors(['start_date' => 'يوجد طلب إجازة آخر لنفس الموظف داخل نفس الفترة'])
                ->withInput();
        }

        $daysCount = $this->calculateLeaveDays(
            $request->start_date,
            $request->end_date
        );

        if ($daysCount <= 0) {
            return back()
                ->withErrors(['days_count' => 'عدد أيام الإجازة يجب أن يكون أكبر من صفر بعد استبعاد الويكند والإجازات الرسمية'])
                ->withInput();
        }

        if ($leaveType->max_days_per_year && $daysCount > $leaveType->max_days_per_year) {
            return back()
                ->withErrors(['days_count' => 'عدد الأيام يتجاوز الحد الأقصى لهذا النوع من الإجازات'])
                ->withInput();
        }

        if ($leaveType->requires_attachment && !$request->hasFile('attachment')) {
            return back()
                ->withErrors(['attachment' => 'هذا النوع من الإجازات يتطلب إرفاق ملف'])
                ->withInput();
        }

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        DB::transaction(function () use ($request, $leaveType, $daysCount, $attachmentPath) {
            $leaveRequest = LeaveRequest::create([
                'employee_id' => $request->employee_id,
                'leave_type_id' => $leaveType->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days_count' => $daysCount,

                // كل الطلبات تبدأ قيد المراجعة من المدير المباشر
                'status' => 'pending',
                'workflow_status' => 'pending_manager',
                'direct_manager_status' => 'pending',
                'hr_status' => 'waiting_manager',

                'reason' => $request->reason,
                'attachment' => $attachmentPath,
            ]);

            $this->recordLeaveWorkflowTransaction(
                $leaveRequest,
                'leave_request_created',
                'تم تقديم طلب إجازة وحالته بانتظار موافقة المدير المباشر'
            );
        });

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'تم إنشاء طلب الإجازة بنجاح وحالته قيد المراجعة');
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.edit'), 403);

        if ($leaveRequest->status !== 'pending' || ($leaveRequest->workflow_status ?? 'pending_manager') !== 'pending_manager') {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'لا يمكن تعديل الطلب بعد انتقاله من مرحلة المدير المباشر');
        }

        $leaveRequest->load(['employee', 'leaveType']);

        $employees = Employee::with('currentLeaveBalance')->where('status', 'active')->orderBy('full_name')->get();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('leave_requests.edit', compact(
            'leaveRequest',
            'employees',
            'leaveTypes'
        ));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.edit'), 403);

        if ($leaveRequest->status !== 'pending' || ($leaveRequest->workflow_status ?? 'pending_manager') !== 'pending_manager') {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'لا يمكن تعديل الطلب بعد انتقاله من مرحلة المدير المباشر');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'leave_type_id.required' => 'نوع الإجازة مطلوب',
            'start_date.required' => 'تاريخ بداية الإجازة مطلوب',
            'end_date.required' => 'تاريخ نهاية الإجازة مطلوب',
            'end_date.after_or_equal' => 'تاريخ نهاية الإجازة يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        if (!$leaveType->is_active) {
            return back()
                ->withErrors(['leave_type_id' => 'نوع الإجازة غير مفعل'])
                ->withInput();
        }

        if ($this->hasOverlappingLeaveRequest(
            (int) $request->employee_id,
            $request->start_date,
            $request->end_date,
            $leaveRequest->id
        )) {
            return back()
                ->withErrors(['start_date' => 'يوجد طلب إجازة آخر لنفس الموظف داخل نفس الفترة'])
                ->withInput();
        }

        $daysCount = $this->calculateLeaveDays(
            $request->start_date,
            $request->end_date
        );

        if ($daysCount <= 0) {
            return back()
                ->withErrors(['days_count' => 'عدد أيام الإجازة يجب أن يكون أكبر من صفر بعد استبعاد الويكند والإجازات الرسمية'])
                ->withInput();
        }

        if ($leaveType->max_days_per_year && $daysCount > $leaveType->max_days_per_year) {
            return back()
                ->withErrors(['days_count' => 'عدد الأيام يتجاوز الحد الأقصى لهذا النوع من الإجازات'])
                ->withInput();
        }

        if ($leaveType->requires_attachment && !$request->hasFile('attachment') && !$leaveRequest->attachment) {
            return back()
                ->withErrors(['attachment' => 'هذا النوع من الإجازات يتطلب إرفاق ملف'])
                ->withInput();
        }

        $updateData = [
            'employee_id' => $request->employee_id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $daysCount,
            'reason' => $request->reason,
        ];

        if ($request->hasFile('attachment')) {
            if ($leaveRequest->attachment && Storage::disk('public')->exists($leaveRequest->attachment)) {
                Storage::disk('public')->delete($leaveRequest->attachment);
            }

            $updateData['attachment'] = $request->file('attachment')->store('leave_attachments', 'public');
        }

        $leaveRequest->update($updateData);

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'تم تعديل طلب الإجازة بنجاح');
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.approve'), 403);

        /*
         * منع الاعتماد المباشر من صفحة الإجازات القديمة.
         * المسار الجديد يجب أن يكون:
         * المدير المباشر -> الموارد البشرية -> الاعتماد النهائي.
         */
        if ($leaveRequest->workflow_status === 'pending_manager') {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'لا يمكن اعتماد الطلب مباشرة من هذه الصفحة. يجب اعتماد الطلب من صفحة موافقات المدير المباشر أولاً.');
        }

        if ($leaveRequest->workflow_status === 'manager_approved_pending_hr') {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'الطلب بانتظار الموارد البشرية. يرجى اعتماده من صفحة موافقات الموارد البشرية.');
        }

        if ($leaveRequest->status !== 'pending') {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'لا يمكن اعتماد طلب غير قيد المراجعة');
        }

        /*
         * هذا الجزء يبقى فقط للطلبات القديمة التي لا تستخدم workflow_status.
         */
        try {
            DB::transaction(function () use ($leaveRequest) {
                $leaveRequest->load('leaveType');

                $this->applyApprovedLeaveToBalance($leaveRequest);

                $leaveRequest->forceFill([
                    'status' => 'approved',
                    'workflow_status' => 'approved_by_hr',
                    'hr_status' => 'approved',
                    'hr_approved_by' => auth()->id(),
                    'hr_approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ])->save();

                $this->recordLeaveWorkflowTransaction(
                    $leaveRequest,
                    'hr_approved',
                    'تمت موافقة الموارد البشرية واعتماد الإجازة نهائيًا'
                );
            });
        } catch (\Throwable $exception) {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'تم قبول طلب الإجازة بنجاح');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.reject'), 403);

        if ($leaveRequest->status !== 'pending') {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'لا يمكن رفض طلب غير قيد المراجعة');
        }

        if ($leaveRequest->workflow_status === 'manager_approved_pending_hr') {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'الطلب بانتظار الموارد البشرية. يرجى الرفض من صفحة موافقات الموارد البشرية.');
        }

        $request->validate([
            'reject_reason' => 'nullable|string|max:1000',
        ]);

        /*
         * إذا كان الطلب ما زال عند المدير المباشر، نجعل الرفض كرفض مدير حتى يظهر صحيحاً عند الموظف.
         */
        if ($leaveRequest->workflow_status === 'pending_manager') {
            $leaveRequest->forceFill([
                'status' => 'rejected',
                'workflow_status' => 'rejected_by_manager',
                'direct_manager_status' => 'rejected',
                'direct_manager_rejected_by' => auth()->id(),
                'direct_manager_rejected_at' => now(),
                'direct_manager_reject_reason' => $request->reject_reason,
                'hr_status' => 'not_required',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'reject_reason' => $request->reject_reason,
            ])->save();

            $this->recordLeaveWorkflowTransaction(
                $leaveRequest,
                'manager_rejected',
                'تم رفض طلب الإجازة من المدير المباشر'
            );
        } else {
            $leaveRequest->forceFill([
                'status' => 'rejected',
                'workflow_status' => 'rejected_by_hr',
                'hr_status' => 'rejected',
                'hr_rejected_by' => auth()->id(),
                'hr_rejected_at' => now(),
                'hr_reject_reason' => $request->reject_reason,
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'reject_reason' => $request->reject_reason,
            ])->save();

            $this->recordLeaveWorkflowTransaction(
                $leaveRequest,
                'hr_rejected',
                'تم رفض طلب الإجازة من الموارد البشرية'
            );
        }

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'تم رفض الطلب بنجاح');
    }

    public function cancelApproved(Request $request, LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.cancel'), 403);

        /*
         * الإلغاء نهاية للطلب ولا يرجع للمدير المباشر.
         *
         * إذا كان الطلب معتمد من HR: نرجع الرصيد.
         * إذا كان قبل اعتماد HR: نلغي الطلب فقط بدون إرجاع رصيد لأنه لم يخصم شيء.
         */
        $canCancel = in_array($leaveRequest->workflow_status, [
                'pending_manager',
                'manager_approved_pending_hr',
                'approved_by_hr',
            ], true) || in_array($leaveRequest->status, ['pending', 'approved'], true);

        if (!$canCancel) {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'لا يمكن إلغاء هذا الطلب في حالته الحالية');
        }

        $request->validate([
            'cancel_reason' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($leaveRequest, $request) {
                $leaveRequest->load(['leaveType', 'employee']);

                $wasApprovedByHr = $leaveRequest->status === 'approved'
                    || $leaveRequest->workflow_status === 'approved_by_hr';

                if ($wasApprovedByHr) {
                    $this->reverseApprovedLeaveFromBalance($leaveRequest, $request->cancel_reason);
                }

                $leaveRequest->forceFill([
                    'status' => 'cancelled',
                    'workflow_status' => 'cancelled',

                    // لا يرجع للمدير ولا للموارد البشرية بعد الإلغاء
                    'hr_status' => $wasApprovedByHr ? 'cancelled_after_approval' : 'cancelled',
                    'reject_reason' => $request->cancel_reason ?: 'تم إلغاء الطلب من الإدارة',

                    'rejected_by' => auth()->id(),
                    'rejected_at' => now(),
                ])->save();

                $this->recordLeaveWorkflowTransaction(
                    $leaveRequest,
                    $wasApprovedByHr ? 'leave_cancelled_after_approval' : 'leave_cancelled',
                    $wasApprovedByHr
                        ? 'تم إلغاء إجازة معتمدة من الموارد البشرية'
                        : 'تم إلغاء طلب الإجازة قبل الاعتماد النهائي من الموارد البشرية'
                );
            });
        } catch (\Throwable $exception) {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', $exception->getMessage());
        }

        $message = ($leaveRequest->status === 'approved' || $leaveRequest->workflow_status === 'approved_by_hr')
            ? 'تم إلغاء طلب الإجازة وإرجاع الرصيد بنجاح'
            : 'تم إلغاء طلب الإجازة بنجاح بدون إرجاع رصيد لأنه لم يكن معتمدًا من الموارد البشرية';

        return redirect()
            ->route('leave-requests.index')
            ->with('success', $message);
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        abort_if(!auth()->user()->hasPermission('leave_requests.delete'), 403);

        if ($leaveRequest->status === 'approved') {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'لا يمكن حذف طلب إجازة معتمد. استخدم زر إلغاء مع إرجاع الرصيد.');
        }

        $leaveRequest->delete();

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'تم حذف طلب الإجازة بنجاح');
    }

    private function applyApprovedLeaveToBalance(LeaveRequest $leaveRequest): void
    {
        $leaveRequest->load(['leaveType', 'employee']);

        $leaveType = $leaveRequest->leaveType;

        if (!$leaveType) {
            throw new \Exception('نوع الإجازة غير موجود');
        }

        $days = (float) $leaveRequest->days_count;

        $balance = $this->getOpenBalanceForLeave($leaveRequest);

        $beforeBalance = (float) $balance->remaining_days;

        if ($leaveType->deduct_from_annual_balance) {
            if ($beforeBalance < $days) {
                throw new \Exception('رصيد الإجازات غير كافٍ لاعتماد هذا الطلب');
            }

            $afterBalance = $beforeBalance - $days;

            $balance->update([
                'used_paid_days' => max(0, (float) $balance->used_paid_days + $days),
                'remaining_days' => $afterBalance,
            ]);

            EmployeeLeaveTransaction::create([
                'employee_id' => $leaveRequest->employee_id,
                'employee_leave_balance_id' => $balance->id,
                'transaction_type' => 'paid_leave_deduction',
                'days' => -1 * $days,
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
                'reference_type' => LeaveRequest::class,
                'reference_id' => $leaveRequest->id,
                'description' => 'خصم إجازة مدفوعة من الرصيد السنوي - ' . $leaveType->name,
                'created_by' => auth()->id(),
            ]);

            return;
        }

        if (!$leaveType->is_paid) {
            $balance->update([
                'used_unpaid_days' => max(0, (float) $balance->used_unpaid_days + $days),
            ]);

            EmployeeLeaveTransaction::create([
                'employee_id' => $leaveRequest->employee_id,
                'employee_leave_balance_id' => $balance->id,
                'transaction_type' => 'unpaid_leave_record',
                'days' => $days,
                'before_balance' => $beforeBalance,
                'after_balance' => $beforeBalance,
                'reference_type' => LeaveRequest::class,
                'reference_id' => $leaveRequest->id,
                'description' => 'تسجيل إجازة غير مدفوعة بدون خصم من الرصيد السنوي - ' . $leaveType->name,
                'created_by' => auth()->id(),
            ]);

            return;
        }

        $balance->update([
            'used_other_days' => max(0, (float) $balance->used_other_days + $days),
        ]);

        $transactionType = $leaveType->code === 'official'
            ? 'official_leave_record'
            : 'other_leave_record';

        EmployeeLeaveTransaction::create([
            'employee_id' => $leaveRequest->employee_id,
            'employee_leave_balance_id' => $balance->id,
            'transaction_type' => $transactionType,
            'days' => $days,
            'before_balance' => $beforeBalance,
            'after_balance' => $beforeBalance,
            'reference_type' => LeaveRequest::class,
            'reference_id' => $leaveRequest->id,
            'description' => 'تسجيل إجازة لا تخصم من الرصيد السنوي - ' . $leaveType->name,
            'created_by' => auth()->id(),
        ]);
    }

    private function reverseApprovedLeaveFromBalance(LeaveRequest $leaveRequest, ?string $reason = null): void
    {
        $leaveType = $leaveRequest->leaveType;

        if (!$leaveType) {
            throw new \Exception('نوع الإجازة غير موجود');
        }

        $days = (float) $leaveRequest->days_count;
        $balance = $this->getOpenBalanceForLeave($leaveRequest);
        $beforeBalance = (float) $balance->remaining_days;

        if ($leaveType->deduct_from_annual_balance) {
            $afterBalance = $beforeBalance + $days;

            $balance->update([
                'used_paid_days' => max(0, (float) $balance->used_paid_days - $days),
                'remaining_days' => $afterBalance,
            ]);

            EmployeeLeaveTransaction::create([
                'employee_id' => $leaveRequest->employee_id,
                'employee_leave_balance_id' => $balance->id,
                'transaction_type' => 'paid_leave_reversal',
                'days' => $days,
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
                'reference_type' => LeaveRequest::class,
                'reference_id' => $leaveRequest->id,
                'description' => 'إرجاع رصيد بسبب إلغاء طلب إجازة معتمد - ' . ($reason ?: $leaveType->name),
                'created_by' => auth()->id(),
            ]);

            return;
        }

        if (!$leaveType->is_paid) {
            $balance->update([
                'used_unpaid_days' => max(0, (float) $balance->used_unpaid_days - $days),
            ]);

            EmployeeLeaveTransaction::create([
                'employee_id' => $leaveRequest->employee_id,
                'employee_leave_balance_id' => $balance->id,
                'transaction_type' => 'unpaid_leave_reversal',
                'days' => -1 * $days,
                'before_balance' => $beforeBalance,
                'after_balance' => $beforeBalance,
                'reference_type' => LeaveRequest::class,
                'reference_id' => $leaveRequest->id,
                'description' => 'إلغاء تسجيل إجازة غير مدفوعة - ' . ($reason ?: $leaveType->name),
                'created_by' => auth()->id(),
            ]);

            return;
        }

        $balance->update([
            'used_other_days' => max(0, (float) $balance->used_other_days - $days),
        ]);

        EmployeeLeaveTransaction::create([
            'employee_id' => $leaveRequest->employee_id,
            'employee_leave_balance_id' => $balance->id,
            'transaction_type' => 'other_leave_reversal',
            'days' => -1 * $days,
            'before_balance' => $beforeBalance,
            'after_balance' => $beforeBalance,
            'reference_type' => LeaveRequest::class,
            'reference_id' => $leaveRequest->id,
            'description' => 'إلغاء تسجيل إجازة لا تخصم من الرصيد السنوي - ' . ($reason ?: $leaveType->name),
            'created_by' => auth()->id(),
        ]);
    }

    private function recordLeaveWorkflowTransaction(
        LeaveRequest $leaveRequest,
        string $transactionType,
        string $description,
        float $days = 0
    ): void {
        $balance = EmployeeLeaveBalance::where('employee_id', $leaveRequest->employee_id)
            ->where('status', 'open')
            ->whereDate('service_year_start', '<=', $leaveRequest->start_date)
            ->whereDate('service_year_end', '>=', $leaveRequest->end_date)
            ->first();

        /*
         * إذا لم يوجد رصيد مفتوح لا نوقف الطلب.
         * سجلات الخصم/الإرجاع الأساسية ما زالت تستخدم getOpenBalanceForLeave وتتحقق بشكل صارم.
         */
        if (!$balance) {
            return;
        }

        $currentBalance = (float) $balance->remaining_days;

        EmployeeLeaveTransaction::create([
            'employee_id' => $leaveRequest->employee_id,
            'employee_leave_balance_id' => $balance->id,
            'transaction_type' => $transactionType,
            'days' => $days,
            'before_balance' => $currentBalance,
            'after_balance' => $currentBalance,
            'reference_type' => LeaveRequest::class,
            'reference_id' => $leaveRequest->id,
            'description' => $description,
            'created_by' => auth()->id(),
        ]);
    }

    private function getOpenBalanceForLeave(LeaveRequest $leaveRequest): EmployeeLeaveBalance
    {
        $balance = EmployeeLeaveBalance::where('employee_id', $leaveRequest->employee_id)
            ->where('status', 'open')
            ->whereDate('service_year_start', '<=', $leaveRequest->start_date)
            ->whereDate('service_year_end', '>=', $leaveRequest->end_date)
            ->lockForUpdate()
            ->first();

        if (!$balance) {
            throw new \Exception('لا يوجد رصيد إجازات مفتوح يغطي تاريخ الإجازة. يرجى إعادة احتساب الأرصدة أولًا.');
        }

        return $balance;
    }

    private function hasOverlappingLeaveRequest(
        int $employeeId,
        string $startDate,
        string $endDate,
        ?int $ignoreLeaveRequestId = null
    ): bool {
        return LeaveRequest::query()
            ->where('employee_id', $employeeId)
            ->whereIn('status', ['pending', 'approved'])
            ->when($ignoreLeaveRequestId, function ($query) use ($ignoreLeaveRequestId) {
                $query->where('id', '!=', $ignoreLeaveRequestId);
            })
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->exists();
    }

    private function calculateLeaveDays(string $startDate, string $endDate): float
    {
        $policy = LeavePolicy::where('is_active', true)->first();

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        $officialHolidayDates = [];

        if ($policy && $policy->exclude_official_holidays) {
            $holidays = OfficialHoliday::query()
                ->where('is_active', true)
                ->whereDate('start_date', '<=', $end->toDateString())
                ->whereDate('end_date', '>=', $start->toDateString())
                ->get();

            foreach ($holidays as $holiday) {
                $holidayStart = Carbon::parse($holiday->start_date)->startOfDay();
                $holidayEnd = Carbon::parse($holiday->end_date)->startOfDay();

                if ($holidayStart->lessThan($start)) {
                    $holidayStart = $start->copy();
                }

                if ($holidayEnd->greaterThan($end)) {
                    $holidayEnd = $end->copy();
                }

                for ($date = $holidayStart->copy(); $date->lessThanOrEqualTo($holidayEnd); $date->addDay()) {
                    $officialHolidayDates[$date->toDateString()] = true;
                }
            }
        }

        $days = 0;

        for ($date = $start->copy(); $date->lessThanOrEqualTo($end); $date->addDay()) {
            // الويكند المطلوب في النظام: يوم الجمعة فقط
            if ($policy && $policy->exclude_weekends && $date->dayOfWeek === Carbon::FRIDAY) {
                continue;
            }

            if ($policy && $policy->exclude_official_holidays && isset($officialHolidayDates[$date->toDateString()])) {
                continue;
            }

            $days++;
        }

        return (float) $days;
    }
}
