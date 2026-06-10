<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeavePolicy;
use App\Models\LeaveType;

class LeaveSystemSeeder extends Seeder
{
    public function run(): void
    {
        LeavePolicy::updateOrCreate(
            [
                'name' => 'سياسة الإجازات الافتراضية',
            ],
            [
                'annual_days_before_5_years' => 21,
                'annual_days_after_5_years' => 30,
                'after_years' => 5,

                /*
                 * hire_date  = حسب تاريخ مباشرة الموظف
                 * gregorian  = حسب السنة الميلادية
                 * hijri      = حسب السنة الهجرية
                 */
                'leave_year_type' => 'hire_date',

                'carry_forward_enabled' => true,
                'max_carry_forward_days' => 30,

                'exclude_weekends' => true,
                'exclude_official_holidays' => true,

                'inactive_employee_accrual' => false,
                'is_active' => true,
            ]
        );

        $leaveTypes = [
            [
                'name' => 'إجازة سنوية',
                'code' => 'annual',
                'is_paid' => true,
                'deduct_from_annual_balance' => true,
                'requires_attachment' => false,
                'requires_approval' => true,
                'auto_approved' => false,
                'max_days_per_year' => null,
                'is_active' => true,
            ],
            [
                'name' => 'إجازة غير مدفوعة',
                'code' => 'unpaid',
                'is_paid' => false,
                'deduct_from_annual_balance' => false,
                'requires_attachment' => false,
                'requires_approval' => true,
                'auto_approved' => false,
                'max_days_per_year' => null,
                'is_active' => true,
            ],
            [
                'name' => 'إجازة رسمية',
                'code' => 'official',
                'is_paid' => true,
                'deduct_from_annual_balance' => false,
                'requires_attachment' => false,
                'requires_approval' => false,
                'auto_approved' => true,
                'max_days_per_year' => null,
                'is_active' => true,
            ],
            [
                'name' => 'إجازة مرضية',
                'code' => 'sick',
                'is_paid' => true,
                'deduct_from_annual_balance' => false,
                'requires_attachment' => true,
                'requires_approval' => true,
                'auto_approved' => false,
                'max_days_per_year' => null,
                'is_active' => true,
            ],
            [
                'name' => 'إجازة زواج',
                'code' => 'marriage',
                'is_paid' => true,
                'deduct_from_annual_balance' => false,
                'requires_attachment' => true,
                'requires_approval' => true,
                'auto_approved' => false,
                'max_days_per_year' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'إجازة مولود',
                'code' => 'newborn',
                'is_paid' => true,
                'deduct_from_annual_balance' => false,
                'requires_attachment' => true,
                'requires_approval' => true,
                'auto_approved' => false,
                'max_days_per_year' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'إجازة وفاة',
                'code' => 'death',
                'is_paid' => true,
                'deduct_from_annual_balance' => false,
                'requires_attachment' => true,
                'requires_approval' => true,
                'auto_approved' => false,
                'max_days_per_year' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'إجازة أخرى',
                'code' => 'other',
                'is_paid' => true,
                'deduct_from_annual_balance' => false,
                'requires_attachment' => false,
                'requires_approval' => true,
                'auto_approved' => false,
                'max_days_per_year' => null,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::updateOrCreate(
                ['code' => $leaveType['code']],
                $leaveType
            );
        }
    }
}
