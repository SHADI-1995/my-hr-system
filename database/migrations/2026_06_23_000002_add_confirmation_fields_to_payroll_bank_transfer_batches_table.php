<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payroll_bank_transfer_batches')) {
            return;
        }

        Schema::table('payroll_bank_transfer_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_bank_transfer_batches', 'bank_reference')) {
                $table->string('bank_reference')->nullable()->after('confirmed_at');
            }

            if (!Schema::hasColumn('payroll_bank_transfer_batches', 'bank_transfer_date')) {
                $table->date('bank_transfer_date')->nullable()->after('bank_reference');
            }

            if (!Schema::hasColumn('payroll_bank_transfer_batches', 'confirmation_file')) {
                $table->string('confirmation_file')->nullable()->after('bank_transfer_date');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payroll_bank_transfer_batches')) {
            return;
        }

        Schema::table('payroll_bank_transfer_batches', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_bank_transfer_batches', 'confirmation_file')) {
                $table->dropColumn('confirmation_file');
            }

            if (Schema::hasColumn('payroll_bank_transfer_batches', 'bank_transfer_date')) {
                $table->dropColumn('bank_transfer_date');
            }

            if (Schema::hasColumn('payroll_bank_transfer_batches', 'bank_reference')) {
                $table->dropColumn('bank_reference');
            }
        });
    }
};
