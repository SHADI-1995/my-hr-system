<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         |--------------------------------------------------------------------------
         | Fix
         |--------------------------------------------------------------------------
         | MySQL has an index-name length limit.
         | لذلك استخدمنا اسم مختصر للفهرس بدل الاسم الافتراضي الطويل.
         */

        if (!Schema::hasTable('salary_advance_request_logs')) {
            Schema::create('salary_advance_request_logs', function (Blueprint $table) {
                $table->id();

                $table->foreignId('salary_advance_request_id')
                    ->constrained('salary_advance_requests')
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->string('transaction_type');
                $table->string('old_status')->nullable();
                $table->string('new_status')->nullable();
                $table->text('description')->nullable();
                $table->json('meta')->nullable();

                $table->timestamps();

                /*
                 * اسم مختصر للفهرس لتجنب خطأ:
                 * Identifier name is too long
                 */
                $table->index(
                    ['salary_advance_request_id', 'created_at'],
                    'sar_logs_request_created_idx'
                );
            });

            return;
        }

        /*
         * إذا كان الجدول انشأ جزئياً قبل فشل migration،
         * لا نعيد إنشاء الجدول، فقط نحاول إضافة الفهرس المختصر.
         */
        try {
            Schema::table('salary_advance_request_logs', function (Blueprint $table) {
                $table->index(
                    ['salary_advance_request_id', 'created_at'],
                    'sar_logs_request_created_idx'
                );
            });
        } catch (\Throwable $exception) {
            /*
             * نتجاهل الخطأ إذا كان الفهرس موجوداً مسبقاً.
             */
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_advance_request_logs');
    }
};
