<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_iqamas', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_iqamas', 'photo')) {
                $table->string('photo')->nullable()->after('document_status');
            }
        });

        Schema::table('employee_passports', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_passports', 'photo')) {
                $table->string('photo')->nullable()->after('document_status');
            }
        });

        Schema::table('employee_health_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_health_cards', 'photo')) {
                $table->string('photo')->nullable()->after('document_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_iqamas', function (Blueprint $table) {
            if (Schema::hasColumn('employee_iqamas', 'photo')) {
                $table->dropColumn('photo');
            }
        });

        Schema::table('employee_passports', function (Blueprint $table) {
            if (Schema::hasColumn('employee_passports', 'photo')) {
                $table->dropColumn('photo');
            }
        });

        Schema::table('employee_health_cards', function (Blueprint $table) {
            if (Schema::hasColumn('employee_health_cards', 'photo')) {
                $table->dropColumn('photo');
            }
        });
    }
};
