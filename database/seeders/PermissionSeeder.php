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
            | Employee Deductions
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'عرض استقطاعات الموظفين',
                'code' => 'employee_deductions.view',
                'module' => 'employee_deductions',
            ],
            [
                'name' => 'إضافة استقطاع موظف',
                'code' => 'employee_deductions.create',
                'module' => 'employee_deductions',
            ],
            [
                'name' => 'اعتماد استقطاع موظف',
                'code' => 'employee_deductions.approve',
                'module' => 'employee_deductions',
            ],
            [
                'name' => 'إلغاء استقطاع موظف',
                'code' => 'employee_deductions.cancel',
                'module' => 'employee_deductions',
            ],

            /*
            |--------------------------------------------------------------------------
            | Employee Suspensions
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'عرض إيقافات الموظفين',
                'code' => 'employee_suspensions.view',
                'module' => 'employee_suspensions',
            ],
            [
                'name' => 'إضافة إيقاف موظف',
                'code' => 'employee_suspensions.create',
                'module' => 'employee_suspensions',
            ],
            [
                'name' => 'استئناف موظف موقوف',
                'code' => 'employee_suspensions.resume',
                'module' => 'employee_suspensions',
            ],
            [
                'name' => 'إلغاء إيقاف موظف',
                'code' => 'employee_suspensions.cancel',
                'module' => 'employee_suspensions',
            ],


            /*
            |--------------------------------------------------------------------------
            | Salary Advances
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'عرض سلف الموظفين',
                'code' => 'salary_advances.view',
                'module' => 'salary_advances',
            ],
            [
                'name' => 'إضافة سلفة موظف',
                'code' => 'salary_advances.create',
                'module' => 'salary_advances',
            ],
            [
                'name' => 'تعديل سلفة موظف',
                'code' => 'salary_advances.edit',
                'module' => 'salary_advances',
            ],
            [
                'name' => 'اعتماد سلفة موظف',
                'code' => 'salary_advances.approve',
                'module' => 'salary_advances',
            ],
            [
                'name' => 'إلغاء سلفة موظف',
                'code' => 'salary_advances.cancel',
                'module' => 'salary_advances',
            ],


            /*
            |--------------------------------------------------------------------------
            | Salary Advance Requests Workflow
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'تقديم طلب سلفة من الموظف',
                'code' => 'salary_advance_requests.create',
                'module' => 'salary_advance_requests',
            ],
            [
                'name' => 'عرض طلبات السلف الخاصة بالموظف',
                'code' => 'salary_advance_requests.view_own',
                'module' => 'salary_advance_requests',
            ],
            [
                'name' => 'عرض جميع طلبات السلف',
                'code' => 'salary_advance_requests.view_all',
                'module' => 'salary_advance_requests',
            ],
            [
                'name' => 'موافقة المدير المباشر على طلبات السلف',
                'code' => 'salary_advance_requests.manager_approval',
                'module' => 'salary_advance_requests',
            ],
            [
                'name' => 'موافقة الموارد البشرية على طلبات السلف',
                'code' => 'salary_advance_requests.hr_approval',
                'module' => 'salary_advance_requests',
            ],
            [
                'name' => 'إلغاء طلب سلفة',
                'code' => 'salary_advance_requests.cancel',
                'module' => 'salary_advance_requests',
            ],

            /*
            |--------------------------------------------------------------------------
            | Payroll Periods & Salary Calculation
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'عرض فترات مسير الرواتب',
                'code' => 'payroll_periods.view',
                'module' => 'payroll_periods',
            ],
            [
                'name' => 'إنشاء فترة مسير رواتب',
                'code' => 'payroll_periods.create',
                'module' => 'payroll_periods',
            ],
            [
                'name' => 'احتساب مسير الرواتب',
                'code' => 'payroll_periods.calculate',
                'module' => 'payroll_periods',
            ],
            [
                'name' => 'اعتماد مسير الرواتب',
                'code' => 'payroll_periods.approve',
                'module' => 'payroll_periods',
            ],
            [
                'name' => 'إلغاء اعتماد مسير الرواتب',
                'code' => 'payroll_periods.cancel_approval',
                'module' => 'payroll_periods',
            ],
            [
                'name' => 'صرف مسير الرواتب',
                'code' => 'payroll_periods.pay',
                'module' => 'payroll_periods',
            ],
            [
                'name' => 'حذف فترة مسير الرواتب',
                'code' => 'payroll_periods.delete',
                'module' => 'payroll_periods',
            ],
            [
                'name' => 'عرض تفاصيل رواتب الموظفين داخل المسير',
                'code' => 'payroll_items.view',
                'module' => 'payroll_periods',
            ],
            [
                'name' => 'عرض تفاصيل مكونات راتب الموظف داخل المسير',
                'code' => 'payroll_items.components.view',
                'module' => 'payroll_periods',
            ],

            /*
            |--------------------------------------------------------------------------
            | Leave Types Payroll Settings
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'عرض إعدادات تأثير الإجازات على الرواتب',
                'code' => 'leave_types.payroll_settings.view',
                'module' => 'leave_types',
            ],
            [
                'name' => 'تعديل إعدادات تأثير الإجازات على الرواتب',
                'code' => 'leave_types.payroll_settings.edit',
                'module' => 'leave_types',
            ],


            /*
            |--------------------------------------------------------------------------
            | Payroll Reports & Payslips
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'عرض تقارير الرواتب',
                'code' => 'payroll_reports.view',
                'module' => 'payroll_reports',
            ],
            [
                'name' => 'تصدير تقارير الرواتب',
                'code' => 'payroll_reports.export',
                'module' => 'payroll_reports',
            ],
            [
                'name' => 'عرض وطباعة قسائم الرواتب',
                'code' => 'payroll_reports.payslip',
                'module' => 'payroll_reports',
            ],

            /*
|--------------------------------------------------------------------------
| Salary Payment Methods
|--------------------------------------------------------------------------
*/
            [
                'name' => 'عرض طرق صرف الراتب',
                'code' => 'salary_payment_methods.view',
                'module' => 'salary_payment_methods',
            ],
            [
                'name' => 'إضافة طريقة صرف راتب',
                'code' => 'salary_payment_methods.create',
                'module' => 'salary_payment_methods',
            ],
            [
                'name' => 'تعديل طريقة صرف راتب',
                'code' => 'salary_payment_methods.edit',
                'module' => 'salary_payment_methods',
            ],
            [
                'name' => 'حذف طريقة صرف راتب',
                'code' => 'salary_payment_methods.delete',
                'module' => 'salary_payment_methods',
            ],

            /*
            |--------------------------------------------------------------------------
            | Salary Payment Methods Field Permissions
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'تعديل الاسم العربي لطريقة صرف الراتب',
                'code' => 'salary_payment_methods.edit.name_ar',
                'module' => 'salary_payment_methods',
            ],
            [
                'name' => 'تعديل الاسم الإنجليزي لطريقة صرف الراتب',
                'code' => 'salary_payment_methods.edit.name_en',
                'module' => 'salary_payment_methods',
            ],
            [
                'name' => 'تعديل كود طريقة صرف الراتب',
                'code' => 'salary_payment_methods.edit.code',
                'module' => 'salary_payment_methods',
            ],
            [
                'name' => 'تعديل حالة طريقة صرف الراتب',
                'code' => 'salary_payment_methods.edit.is_active',
                'module' => 'salary_payment_methods',
            ],
            [
                'name' => 'تعديل ترتيب طريقة صرف الراتب',
                'code' => 'salary_payment_methods.edit.sort_order',
                'module' => 'salary_payment_methods',
            ],
            [
                'name' => 'تعديل ملاحظات طريقة صرف الراتب',
                'code' => 'salary_payment_methods.edit.notes',
                'module' => 'salary_payment_methods',
            ],

            [
                'name' => 'عرض مجموعات الرواتب',
                'code' => 'payroll_groups.view',
                'module' => 'payroll_groups',
            ],
            [
                'name' => 'إضافة مجموعات الرواتب',
                'code' => 'payroll_groups.create',
                'module' => 'payroll_groups',
            ],
            [
                'name' => 'تعديل مجموعات الرواتب',
                'code' => 'payroll_groups.edit',
                'module' => 'payroll_groups',
            ],
            [
                'name' => 'حذف مجموعات الرواتب',
                'code' => 'payroll_groups.delete',
                'module' => 'payroll_groups',
            ],
            [
                'name' => 'تعديل الاسم العربي في مجموعات الرواتب',
                'code' => 'payroll_groups.edit.name_ar',
                'module' => 'payroll_groups',
            ],
            [
                'name' => 'تعديل الاسم الإنجليزي في مجموعات الرواتب',
                'code' => 'payroll_groups.edit.name_en',
                'module' => 'payroll_groups',
            ],
            [
                'name' => 'تعديل الكود في مجموعات الرواتب',
                'code' => 'payroll_groups.edit.code',
                'module' => 'payroll_groups',
            ],
            [
                'name' => 'تعديل الحالة في مجموعات الرواتب',
                'code' => 'payroll_groups.edit.is_active',
                'module' => 'payroll_groups',
            ],
            [
                'name' => 'تعديل الترتيب في مجموعات الرواتب',
                'code' => 'payroll_groups.edit.sort_order',
                'module' => 'payroll_groups',
            ],
            [
                'name' => 'تعديل الملاحظات في مجموعات الرواتب',
                'code' => 'payroll_groups.edit.notes',
                'module' => 'payroll_groups',
            ],




            /*
                        |--------------------------------------------------------------------------
                        | مركز التكلفه ومجموعه الرواتب
                        |--------------------------------------------------------------------------
                        */

            [
                'name' => 'عرض مراكز التكلفة',
                'code' => 'cost_centers.view',
                'module' => 'cost_centers',
            ],
            [
                'name' => 'إضافة مراكز التكلفة',
                'code' => 'cost_centers.create',
                'module' => 'cost_centers',
            ],
            [
                'name' => 'تعديل مراكز التكلفة',
                'code' => 'cost_centers.edit',
                'module' => 'cost_centers',
            ],
            [
                'name' => 'حذف مراكز التكلفة',
                'code' => 'cost_centers.delete',
                'module' => 'cost_centers',
            ],
            [
                'name' => 'تعديل الاسم العربي في مراكز التكلفة',
                'code' => 'cost_centers.edit.name_ar',
                'module' => 'cost_centers',
            ],
            [
                'name' => 'تعديل الاسم الإنجليزي في مراكز التكلفة',
                'code' => 'cost_centers.edit.name_en',
                'module' => 'cost_centers',
            ],
            [
                'name' => 'تعديل الكود في مراكز التكلفة',
                'code' => 'cost_centers.edit.code',
                'module' => 'cost_centers',
            ],
            [
                'name' => 'تعديل الحالة في مراكز التكلفة',
                'code' => 'cost_centers.edit.is_active',
                'module' => 'cost_centers',
            ],
            [
                'name' => 'تعديل الترتيب في مراكز التكلفة',
                'code' => 'cost_centers.edit.sort_order',
                'module' => 'cost_centers',
            ],
            [
                'name' => 'تعديل الملاحظات في مراكز التكلفة',
                'code' => 'cost_centers.edit.notes',
                'module' => 'cost_centers',
            ],

            [
                'name' => 'تعديل استقطاع موظف',
                'code' => 'employee_deductions.edit',
                'module' => 'employee_deductions',
            ],

            /*
                                              |--------------------------------------------------------------------------
                                              | اهدادات الراتب
                                              |--------------------------------------------------------------------------
                                              */


            [
                'name' => 'عرض إعدادات الرواتب',
                'code' => 'payroll_settings.view',
                'module' => 'payroll_settings',
            ],
            [
                'name' => 'تعديل إعدادات الرواتب',
                'code' => 'payroll_settings.edit',
                'module' => 'payroll_settings',
            ],

            /*
                                  |--------------------------------------------------------------------------
                                  | deduction_types
                                  |--------------------------------------------------------------------------
                                  */


            [
                'name' => 'عرض أنواع الاستقطاعات',
                'code' => 'deduction_types.view',
                'module' => 'deduction_types',
            ],
            [
                'name' => 'إضافة نوع استقطاع',
                'code' => 'deduction_types.create',
                'module' => 'deduction_types',
            ],
            [
                'name' => 'تعديل نوع استقطاع',
                'code' => 'deduction_types.edit',
                'module' => 'deduction_types',
            ],
            [
                'name' => 'حذف نوع استقطاع',
                'code' => 'deduction_types.delete',
                'module' => 'deduction_types',
            ],
            /*
                       |--------------------------------------------------------------------------
                       | Payroll Period Logs
                       |--------------------------------------------------------------------------
                       */
            [
                'name' => 'عرض سجل حركات مسير الرواتب',
                'code' => 'payroll_period_logs.view',
                'module' => 'payroll_period_logs',
            ],
            [
                'name' => 'تصدير سجل حركات مسير الرواتب',
                'code' => 'payroll_period_logs.export',
                'module' => 'payroll_period_logs',
            ],


            /*
                                    |--------------------------------------------------------------------------
                                    | payroll_bank_transfers
                                    |--------------------------------------------------------------------------
                                    */

            [
                'name' => 'عرض كشف تحويل الرواتب',
                'code' => 'payroll_bank_transfers.view',
                'module' => 'payroll_bank_transfers',
            ],
            [
                'name' => 'تصدير كشف تحويل الرواتب',
                'code' => 'payroll_bank_transfers.export',
                'module' => 'payroll_bank_transfers',
            ],



            /*
                                   |--------------------------------------------------------------------------
                                   |payroll_bank_transfer_batches_permissions
                                   |--------------------------------------------------------------------------
                                   */



            [
                'name' => 'عرض دفعات تحويل الرواتب',
                'code' => 'payroll_bank_transfer_batches.view',
                'module' => 'payroll_bank_transfer_batches',
            ],
            [
                'name' => 'إنشاء دفعة تحويل الرواتب',
                'code' => 'payroll_bank_transfer_batches.create',
                'module' => 'payroll_bank_transfer_batches',
            ],
            [
                'name' => 'تسجيل إرسال دفعة التحويل للبنك',
                'code' => 'payroll_bank_transfer_batches.send',
                'module' => 'payroll_bank_transfer_batches',
            ],
            [
                'name' => 'تأكيد تحويل الرواتب',
                'code' => 'payroll_bank_transfer_batches.confirm',
                'module' => 'payroll_bank_transfer_batches',
            ],
            [
                'name' => 'إلغاء دفعة تحويل الرواتب',
                'code' => 'payroll_bank_transfer_batches.cancel',
                'module' => 'payroll_bank_transfer_batches',
            ],

            [
                'name' => 'تصدير دفعات تحويل الرواتب',
                'code' => 'payroll_bank_transfer_batches.export',
                'module' => 'payroll_bank_transfer_batches',
            ],


            /*
                       |--------------------------------------------------------------------------
                       | تقارير الرواتب من قائمه التقارير
                       |--------------------------------------------------------------------------
                       */

            [
                'name' => 'عرض تقارير الرواتب الشاملة',
                'code' => 'payroll_reports_hub.view',
                'module' => 'payroll_reports_hub',
            ],
            [
                'name' => 'تصدير تقارير الرواتب الشاملة',
                'code' => 'payroll_reports_hub.export',
                'module' => 'payroll_reports_hub',
            ],

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
