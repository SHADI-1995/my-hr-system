<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('salary_advances')) {
            return;
        }

        if (!Schema::hasColumn('salary_advances', 'salary_advance_request_id')) {
            Schema::table('salary_advances', function (Blueprint $table) {
                $table->foreignId('salary_advance_request_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('salary_advance_requests')
                    ->nullOnDelete();

                $table->index('salary_advance_request_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('salary_advances')) {
            return;
        }

        if (Schema::hasColumn('salary_advances', 'salary_advance_request_id')) {
            Schema::table('salary_advances', function (Blueprint $table) {
                $table->dropConstrainedForeignId('salary_advance_request_id');
            });
        }
    }
};
