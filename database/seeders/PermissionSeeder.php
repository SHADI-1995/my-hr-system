<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            /*
            |--------------------------------------------------------------------------
            | Employees
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض الموظفين', 'code' => 'employees.view', 'module' => 'employees'],
            [
                'name' => 'عرض تفاصيل الموظف',
                'code' => 'employees.show',
                'module' => 'employees',
            ],
            ['name' => 'إضافة موظف', 'code' => 'employees.create', 'module' => 'employees'],
            ['name' => 'تعديل موظف', 'code' => 'employees.edit', 'module' => 'employees'],
            ['name' => 'حذف موظف', 'code' => 'employees.delete', 'module' => 'employees'],
            ['name' => 'بحث الموظفين', 'code' => 'employees.search', 'module' => 'employees'],

            ['name' => 'تعديل رقم الموظف', 'code' => 'employees.edit.employee_number', 'module' => 'employees'],
            ['name' => 'تعديل اسم الموظف', 'code' => 'employees.edit.name', 'module' => 'employees'],
            ['name' => 'تعديل بريد الموظف', 'code' => 'employees.edit.email', 'module' => 'employees'],
            ['name' => 'تعديل جوال الموظف', 'code' => 'employees.edit.phone', 'module' => 'employees'],
            ['name' => 'تعديل قسم الموظف', 'code' => 'employees.edit.department_id', 'module' => 'employees'],
            ['name' => 'تعديل وظيفة الموظف', 'code' => 'employees.edit.position_id', 'module' => 'employees'],
            ['name' => 'تعديل تاريخ التعيين', 'code' => 'employees.edit.hire_date', 'module' => 'employees'],
            ['name' => 'تعديل راتب الموظف', 'code' => 'employees.edit.basic_salary', 'module' => 'employees'],
            ['name' => 'تعديل حالة الموظف', 'code' => 'employees.edit.status', 'module' => 'employees'],
            [
                'name' => 'تعديل جنسية الموظف',
                'code' => 'employees.edit.nationality_id',
                'module' => 'employees',
            ],
            [
                'name' => 'تعديل تاريخ انتهاء الخدمة',
                'code' => 'employees.edit.termination_date',
                'module' => 'employees',
            ],
            [
                'name' => 'تعديل سبب انتهاء الخدمة',
                'code' => 'employees.edit.termination_reason',
                'module' => 'employees',
            ],
            [
                'name' => 'تعديل بدل السكن',
                'code' => 'employees.edit.housing_allowance',
                'module' => 'employees',
            ],
            [
                'name' => 'تعديل بدل النقل',
                'code' => 'employees.edit.transport_allowance',
                'module' => 'employees',
            ],
            [
                'name' => 'تعديل بدل الطعام',
                'code' => 'employees.edit.food_allowance',
                'module' => 'employees',
            ],
            [
                'name' => 'تعديل البدلات الأخرى',
                'code' => 'employees.edit.other_allowance',
                'module' => 'employees',
            ],
            [
                'name' => 'تعديل اسم البنك',
                'code' => 'employees.edit.bank_name',
                'module' => 'employees',
            ],
            [
                'name' => 'تعديل الآيبان',
                'code' => 'employees.edit.iban',
                'module' => 'employees',
            ],
            [
                'name' => 'تعديل ملاحظات الموظف',
                'code' => 'employees.edit.notes',
                'module' => 'employees',
            ],

            [
                'name' => 'عرض سجل تغييرات الراتب',
                'code' => 'employees.salary_history.view',
                'module' => 'employees',
            ],





            /*
                         |--------------------------------------------------------------------------
                         | احتساب الاجازات
                         |--------------------------------------------------------------------------
                         */

            [
                'name' => 'عرض أرصدة الإجازات',
                'code' => 'leave_balances.view',
                'module' => 'leave_balances',
            ],
            [
                'name' => 'إعادة احتساب أرصدة الإجازات',
                'code' => 'leave_balances.recalculate',
                'module' => 'leave_balances',
            ],
            [
                'name' => 'عرض هستوري الإجازات',
                'code' => 'leave_transactions.view',
                'module' => 'leave_balances',
            ],



            /*
                        |--------------------------------------------------------------------------
                        | سياسات الاجازات
                        |--------------------------------------------------------------------------
                        */

            [
                'name' => 'عرض سياسات الإجازات',
                'code' => 'leave_policies.view',
                'module' => 'leave_policies',
            ],
            [
                'name' => 'إضافة سياسة إجازات',
                'code' => 'leave_policies.create',
                'module' => 'leave_policies',
            ],
            [
                'name' => 'تعديل سياسة إجازات',
                'code' => 'leave_policies.edit',
                'module' => 'leave_policies',
            ],



            /*
                                   |--------------------------------------------------------------------------
                                   | انواغ  الاجازات
                                   |--------------------------------------------------------------------------
                                   */

            [
                'name' => 'عرض أنواع الإجازات',
                'code' => 'leave_types.view',
                'module' => 'leave_types',
            ],
            [
                'name' => 'إضافة نوع إجازة',
                'code' => 'leave_types.create',
                'module' => 'leave_types',
            ],
            [
                'name' => 'تعديل نوع إجازة',
                'code' => 'leave_types.edit',
                'module' => 'leave_types',
            ],
            [
                'name' => 'حذف نوع إجازة',
                'code' => 'leave_types.delete',
                'module' => 'leave_types',
            ],




            /*
                                   |--------------------------------------------------------------------------
                                   |الغاء طلب اجازه معمد الاجازات
                                   |--------------------------------------------------------------------------
                                   */
            [
                'name' => 'إلغاء إجازة معتمدة وإرجاع الرصيد',
                'code' => 'leave_requests.cancel',
                'module' => 'leave_requests',
            ],



            /*
                                             |--------------------------------------------------------------------------
                                            الاجازات الرسميه
                                             |--------------------------------------------------------------------------
                                             */

            [
                'name' => 'عرض الإجازات الرسمية',
                'code' => 'official_holidays.view',
                'module' => 'official_holidays',
            ],
            [
                'name' => 'إضافة إجازة رسمية',
                'code' => 'official_holidays.create',
                'module' => 'official_holidays',
            ],
            [
                'name' => 'تعديل إجازة رسمية',
                'code' => 'official_holidays.edit',
                'module' => 'official_holidays',
            ],
            [
                'name' => 'حذف إجازة رسمية',
                'code' => 'official_holidays.delete',
                'module' => 'official_holidays',
            ],

            /*
                                    |--------------------------------------------------------------------------
                                    | تقرير الاجازات
                                    |--------------------------------------------------------------------------
                                    */

            [
                'name' => 'عرض تقارير الإجازات',
                'code' => 'leave_reports.view',
                'module' => 'leave_reports',
            ],
            [
                'name' => 'تصدير تقارير الإجازات',
                'code' => 'leave_reports.export',
                'module' => 'leave_reports',
            ],


            /*
                    |--------------------------------------------------------------------------
                    سجل حركات الاجازات
                    |--------------------------------------------------------------------------
                    */
            [
                'name' => 'عرض سجل حركات الإجازات',
                'code' => 'leave_transactions.view',
                'module' => 'leave_transactions',
            ],
            [
                'name' => 'تصدير سجل حركات الإجازات',
                'code' => 'leave_transactions.export',
                'module' => 'leave_transactions',
            ],


            /*
                            |--------------------------------------------------------------------------
                           موافقه المدير المباشر على طلب الاجازه
                            |--------------------------------------------------------------------------
                            */



            [
                'name' => 'موافقة المدير المباشر على الإجازات',
                'code' => 'leave_requests.manager_approval',
                'module' => 'leave_requests',
            ],

            /*
                            |--------------------------------------------------------------------------
                           موافقه الموارد على طلب الاجازه
                            |--------------------------------------------------------------------------
                            */


            [
                'name' => 'موافقة الموارد البشرية على الإجازات',
                'code' => 'leave_requests.hr_approval',
                'module' => 'leave_requests',
            ],





            /*
                        |--------------------------------------------------------------------------
                        | الجنسيات
                        |--------------------------------------------------------------------------
                        */

            [
                'name' => 'عرض الجنسيات',
                'code' => 'nationalities.view',
                'module' => 'nationalities',
            ],
            [
                'name' => 'إضافة جنسية',
                'code' => 'nationalities.create',
                'module' => 'nationalities',
            ],
            [
                'name' => 'تعديل جنسية',
                'code' => 'nationalities.edit',
                'module' => 'nationalities',
            ],
            [
                'name' => 'حذف جنسية',
                'code' => 'nationalities.delete',
                'module' => 'nationalities',
            ],

            /*
            |--------------------------------------------------------------------------
            | Departments
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض الأقسام', 'code' => 'departments.view', 'module' => 'departments'],
            ['name' => 'إضافة قسم', 'code' => 'departments.create', 'module' => 'departments'],
            ['name' => 'تعديل قسم', 'code' => 'departments.edit', 'module' => 'departments'],
            ['name' => 'حذف قسم', 'code' => 'departments.delete', 'module' => 'departments'],
            ['name' => 'بحث الأقسام', 'code' => 'departments.search', 'module' => 'departments'],

            ['name' => 'تعديل اسم القسم', 'code' => 'departments.edit.name', 'module' => 'departments'],
            ['name' => 'تعديل كود القسم', 'code' => 'departments.edit.code', 'module' => 'departments'],
            ['name' => 'تعديل حالة القسم', 'code' => 'departments.edit.is_active', 'module' => 'departments'],

            /*
            |--------------------------------------------------------------------------
            | Positions
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض الوظائف', 'code' => 'positions.view', 'module' => 'positions'],
            ['name' => 'إضافة وظيفة', 'code' => 'positions.create', 'module' => 'positions'],
            ['name' => 'تعديل وظيفة', 'code' => 'positions.edit', 'module' => 'positions'],
            ['name' => 'حذف وظيفة', 'code' => 'positions.delete', 'module' => 'positions'],
            ['name' => 'بحث الوظائف', 'code' => 'positions.search', 'module' => 'positions'],

            ['name' => 'تعديل مسمى الوظيفة', 'code' => 'positions.edit.title', 'module' => 'positions'],
            ['name' => 'تعديل قسم الوظيفة', 'code' => 'positions.edit.department_id', 'module' => 'positions'],
            ['name' => 'تعديل كود الوظيفة', 'code' => 'positions.edit.code', 'module' => 'positions'],
            ['name' => 'تعديل الحد الأدنى للراتب', 'code' => 'positions.edit.min_salary', 'module' => 'positions'],
            ['name' => 'تعديل الحد الأعلى للراتب', 'code' => 'positions.edit.max_salary', 'module' => 'positions'],
            ['name' => 'تعديل حالة الوظيفة', 'code' => 'positions.edit.is_active', 'module' => 'positions'],

            /*
            |--------------------------------------------------------------------------
            | Attendances
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض الحضور', 'code' => 'attendances.view', 'module' => 'attendances'],
            ['name' => 'إضافة حضور', 'code' => 'attendances.create', 'module' => 'attendances'],
            ['name' => 'تعديل حضور', 'code' => 'attendances.edit', 'module' => 'attendances'],
            ['name' => 'حذف حضور', 'code' => 'attendances.delete', 'module' => 'attendances'],
            ['name' => 'بحث الحضور', 'code' => 'attendances.search', 'module' => 'attendances'],

            ['name' => 'تعديل موظف الحضور', 'code' => 'attendances.edit.employee_id', 'module' => 'attendances'],
            ['name' => 'تعديل تاريخ الحضور', 'code' => 'attendances.edit.attendance_date', 'module' => 'attendances'],
            ['name' => 'تعديل وقت الدخول', 'code' => 'attendances.edit.check_in', 'module' => 'attendances'],
            ['name' => 'تعديل وقت الخروج', 'code' => 'attendances.edit.check_out', 'module' => 'attendances'],
            ['name' => 'تعديل حالة الحضور', 'code' => 'attendances.edit.status', 'module' => 'attendances'],
            ['name' => 'تعديل ملاحظات الحضور', 'code' => 'attendances.edit.notes', 'module' => 'attendances'],

            /*
            |--------------------------------------------------------------------------
            | Leave Requests
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض الإجازات', 'code' => 'leave_requests.view', 'module' => 'leave_requests'],
            ['name' => 'إضافة إجازة', 'code' => 'leave_requests.create', 'module' => 'leave_requests'],
            ['name' => 'تعديل إجازة', 'code' => 'leave_requests.edit', 'module' => 'leave_requests'],
            ['name' => 'حذف إجازة', 'code' => 'leave_requests.delete', 'module' => 'leave_requests'],
            ['name' => 'بحث الإجازات', 'code' => 'leave_requests.search', 'module' => 'leave_requests'],
            ['name' => 'اعتماد الإجازة', 'code' => 'leave_requests.approve', 'module' => 'leave_requests'],
            ['name' => 'رفض الإجازة', 'code' => 'leave_requests.reject', 'module' => 'leave_requests'],

            ['name' => 'تعديل موظف الإجازة', 'code' => 'leave_requests.edit.employee_id', 'module' => 'leave_requests'],
            ['name' => 'تعديل نوع الإجازة', 'code' => 'leave_requests.edit.leave_type', 'module' => 'leave_requests'],
            ['name' => 'تعديل تاريخ بداية الإجازة', 'code' => 'leave_requests.edit.start_date', 'module' => 'leave_requests'],
            ['name' => 'تعديل تاريخ نهاية الإجازة', 'code' => 'leave_requests.edit.end_date', 'module' => 'leave_requests'],
            ['name' => 'تعديل عدد أيام الإجازة', 'code' => 'leave_requests.edit.days_count', 'module' => 'leave_requests'],
            ['name' => 'تعديل حالة الإجازة', 'code' => 'leave_requests.edit.status', 'module' => 'leave_requests'],
            ['name' => 'تعديل سبب الإجازة', 'code' => 'leave_requests.edit.reason', 'module' => 'leave_requests'],

            /*
            |--------------------------------------------------------------------------
            | Payrolls
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض الرواتب', 'code' => 'payrolls.view', 'module' => 'payrolls'],
            ['name' => 'إضافة راتب', 'code' => 'payrolls.create', 'module' => 'payrolls'],
            ['name' => 'تعديل راتب', 'code' => 'payrolls.edit', 'module' => 'payrolls'],
            ['name' => 'حذف راتب', 'code' => 'payrolls.delete', 'module' => 'payrolls'],
            ['name' => 'بحث الرواتب', 'code' => 'payrolls.search', 'module' => 'payrolls'],

            ['name' => 'تعديل موظف الراتب', 'code' => 'payrolls.edit.employee_id', 'module' => 'payrolls'],
            ['name' => 'تعديل شهر الراتب', 'code' => 'payrolls.edit.month', 'module' => 'payrolls'],
            ['name' => 'تعديل الراتب الأساسي', 'code' => 'payrolls.edit.basic_salary', 'module' => 'payrolls'],
            ['name' => 'تعديل البدلات', 'code' => 'payrolls.edit.allowances', 'module' => 'payrolls'],
            ['name' => 'تعديل الخصومات', 'code' => 'payrolls.edit.deductions', 'module' => 'payrolls'],
            ['name' => 'تعديل حالة الراتب', 'code' => 'payrolls.edit.status', 'module' => 'payrolls'],

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض المستخدمين', 'code' => 'users.view', 'module' => 'users'],
            ['name' => 'إضافة مستخدم', 'code' => 'users.create', 'module' => 'users'],
            ['name' => 'تعديل مستخدم', 'code' => 'users.edit', 'module' => 'users'],
            ['name' => 'حذف مستخدم', 'code' => 'users.delete', 'module' => 'users'],

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض الأدوار', 'code' => 'roles.view', 'module' => 'roles'],
            ['name' => 'إضافة دور', 'code' => 'roles.create', 'module' => 'roles'],
            ['name' => 'تعديل دور', 'code' => 'roles.edit', 'module' => 'roles'],
            ['name' => 'حذف دور', 'code' => 'roles.delete', 'module' => 'roles'],

            /*
            |--------------------------------------------------------------------------
            | Reports
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض التقارير', 'code' => 'reports.view', 'module' => 'reports'],
            ['name' => 'تصدير التقارير', 'code' => 'reports.export', 'module' => 'reports'],

            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض الإعدادات', 'code' => 'settings.view', 'module' => 'settings'],
            ['name' => 'تعديل الإعدادات', 'code' => 'settings.edit', 'module' => 'settings'],

            /*
|--------------------------------------------------------------------------
| Employee Iqamas Permissions
|--------------------------------------------------------------------------
*/

            [
                'name' => 'عرض إقامات الموظفين',
                'code' => 'employee_iqamas.view',
                'module' => 'employee_iqamas',
            ],
            [
                'name' => 'إضافة إقامة موظف',
                'code' => 'employee_iqamas.create',
                'module' => 'employee_iqamas',
            ],
            [
                'name' => 'تعديل إقامة موظف',
                'code' => 'employee_iqamas.edit',
                'module' => 'employee_iqamas',
            ],
            [
                'name' => 'حذف إقامة موظف',
                'code' => 'employee_iqamas.delete',
                'module' => 'employee_iqamas',
            ],


            /*
            |--------------------------------------------------------------------------
            | Employee Passports Permissions
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'عرض جوازات الموظفين',
                'code' => 'employee_passports.view',
                'module' => 'employee_passports',
            ],
            [
                'name' => 'إضافة جواز موظف',
                'code' => 'employee_passports.create',
                'module' => 'employee_passports',
            ],
            [
                'name' => 'تعديل جواز موظف',
                'code' => 'employee_passports.edit',
                'module' => 'employee_passports',
            ],
            [
                'name' => 'حذف جواز موظف',
                'code' => 'employee_passports.delete',
                'module' => 'employee_passports',
            ],


            /*
            |--------------------------------------------------------------------------
            | Employee Health Cards Permissions
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'عرض الكروت الصحية للموظفين',
                'code' => 'employee_health_cards.view',
                'module' => 'employee_health_cards',
            ],
            [
                'name' => 'إضافة كرت صحي للموظف',
                'code' => 'employee_health_cards.create',
                'module' => 'employee_health_cards',
            ],
            [
                'name' => 'تعديل كرت صحي للموظف',
                'code' => 'employee_health_cards.edit',
                'module' => 'employee_health_cards',
            ],
            [
                'name' => 'حذف كرت صحي للموظف',
                'code' => 'employee_health_cards.delete',
                'module' => 'employee_health_cards',
            ],
            /*
            |--------------------------------------------------------------------------
            | Employee Document Photos
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'عرض صورة الإقامة',
                'code' => 'employee_iqamas.photo.view',
                'module' => 'employee_iqamas',
            ],
            [
                'name' => 'إضافة صورة الإقامة',
                'code' => 'employee_iqamas.photo.create',
                'module' => 'employee_iqamas',
            ],
            [
                'name' => 'تعديل صورة الإقامة',
                'code' => 'employee_iqamas.photo.edit',
                'module' => 'employee_iqamas',
            ],

            [
                'name' => 'عرض صورة الجواز',
                'code' => 'employee_passports.photo.view',
                'module' => 'employee_passports',
            ],
            [
                'name' => 'إضافة صورة الجواز',
                'code' => 'employee_passports.photo.create',
                'module' => 'employee_passports',
            ],
            [
                'name' => 'تعديل صورة الجواز',
                'code' => 'employee_passports.photo.edit',
                'module' => 'employee_passports',
            ],

            [
                'name' => 'عرض صورة الكرت الصحي',
                'code' => 'employee_health_cards.photo.view',
                'module' => 'employee_health_cards',
            ],
            [
                'name' => 'إضافة صورة الكرت الصحي',
                'code' => 'employee_health_cards.photo.create',
                'module' => 'employee_health_cards',
            ],
            [
                'name' => 'تعديل صورة الكرت الصحي',
                'code' => 'employee_health_cards.photo.edit',
                'module' => 'employee_health_cards',
            ],

            /*
            |--------------------------------------------------------------------------
            | Documents Tracking
            |--------------------------------------------------------------------------
            */
            ['name' => 'عرض متابعة الوثائق', 'code' => 'documents.view', 'module' => 'documents'],
            ['name' => 'تصدير متابعة الوثائق', 'code' => 'documents.export', 'module' => 'documents'],


            /*
                        |--------------------------------------------------------------------------
                        | audit_logs
                        |--------------------------------------------------------------------------
                        */

            ['name' => 'عرض سجل النشاط', 'code' => 'audit_logs.view', 'module' => 'audit_logs'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['code' => $permission['code']],
                $permission
            );
        }
    }
}
