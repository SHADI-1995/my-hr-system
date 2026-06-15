<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('salary_payment_methods')) {
            Schema::create('salary_payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->string('code')->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        $defaultMethods = [
            [
                'name_ar' => 'تحويل بنكي',
                'name_en' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'نقدي',
                'name_en' => 'Cash',
                'code' => 'cash',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'شيك',
                'name_en' => 'Cheque',
                'code' => 'cheque',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name_ar' => 'أخرى',
                'name_en' => 'Other',
                'code' => 'other',
                'is_active' => true,
                'sort_order' => 99,
            ],
        ];

        foreach ($defaultMethods as $method) {
            DB::table('salary_payment_methods')->updateOrInsert(
                ['code' => $method['code']],
                array_merge($method, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'salary_payment_method_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('salary_payment_method_id')
                    ->nullable()
                    ->after('iban')
                    ->constrained('salary_payment_methods')
                    ->nullOnDelete();
            });
        }

        /*
         * ترحيل اختياري من الحقل القديم salary_payment_method إذا كان موجودًا.
         * لا نحذف الحقل القديم حتى لا ينكسر أي كود سابق.
         */
        if (
            Schema::hasTable('employees') &&
            Schema::hasColumn('employees', 'salary_payment_method') &&
            Schema::hasColumn('employees', 'salary_payment_method_id')
        ) {
            $methods = DB::table('salary_payment_methods')->pluck('id', 'code')->toArray();

            foreach ($methods as $code => $id) {
                DB::table('employees')
                    ->where('salary_payment_method', $code)
                    ->whereNull('salary_payment_method_id')
                    ->update(['salary_payment_method_id' => $id]);
            }

            $bankTransferId = $methods['bank_transfer'] ?? null;

            if ($bankTransferId) {
                DB::table('employees')
                    ->whereNull('salary_payment_method_id')
                    ->update(['salary_payment_method_id' => $bankTransferId]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'salary_payment_method_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropConstrainedForeignId('salary_payment_method_id');
            });
        }

        Schema::dropIfExists('salary_payment_methods');
    }
};
