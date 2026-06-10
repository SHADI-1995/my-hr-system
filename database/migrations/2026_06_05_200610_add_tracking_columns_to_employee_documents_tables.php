<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_iqamas', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_iqamas', 'remaining_days')) {
                $table->integer('remaining_days')->nullable()->after('expiry_date');
            }

            if (!Schema::hasColumn('employee_iqamas', 'document_status')) {
                $table->string('document_status')->nullable()->after('remaining_days');
            }
        });

        Schema::table('employee_passports', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_passports', 'remaining_days')) {
                $table->integer('remaining_days')->nullable()->after('expiry_date');
            }

            if (!Schema::hasColumn('employee_passports', 'document_status')) {
                $table->string('document_status')->nullable()->after('remaining_days');
            }
        });

        Schema::table('employee_health_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_health_cards', 'remaining_days')) {
                $table->integer('remaining_days')->nullable()->after('expiry_date');
            }

            if (!Schema::hasColumn('employee_health_cards', 'document_status')) {
                $table->string('document_status')->nullable()->after('remaining_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_iqamas', function (Blueprint $table) {
            if (Schema::hasColumn('employee_iqamas', 'document_status')) {
                $table->dropColumn('document_status');
            }

            if (Schema::hasColumn('employee_iqamas', 'remaining_days')) {
                $table->dropColumn('remaining_days');
            }
        });

        Schema::table('employee_passports', function (Blueprint $table) {
            if (Schema::hasColumn('employee_passports', 'document_status')) {
                $table->dropColumn('document_status');
            }

            if (Schema::hasColumn('employee_passports', 'remaining_days')) {
                $table->dropColumn('remaining_days');
            }
        });

        Schema::table('employee_health_cards', function (Blueprint $table) {
            if (Schema::hasColumn('employee_health_cards', 'document_status')) {
                $table->dropColumn('document_status');
            }

            if (Schema::hasColumn('employee_health_cards', 'remaining_days')) {
                $table->dropColumn('remaining_days');
            }
        });
    }
};
