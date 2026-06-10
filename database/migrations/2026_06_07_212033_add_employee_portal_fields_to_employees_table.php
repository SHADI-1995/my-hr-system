<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'portal_password')) {
                $table->string('portal_password')->nullable()->after('status');
            }

            if (!Schema::hasColumn('employees', 'portal_registered_at')) {
                $table->timestamp('portal_registered_at')->nullable()->after('portal_password');
            }

            if (!Schema::hasColumn('employees', 'portal_last_login_at')) {
                $table->timestamp('portal_last_login_at')->nullable()->after('portal_registered_at');
            }

            if (!Schema::hasColumn('employees', 'direct_manager_user_id')) {
                $table->foreignId('direct_manager_user_id')
                    ->nullable()
                    ->after('portal_last_login_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'direct_manager_user_id')) {
                $table->dropConstrainedForeignId('direct_manager_user_id');
            }

            if (Schema::hasColumn('employees', 'portal_last_login_at')) {
                $table->dropColumn('portal_last_login_at');
            }

            if (Schema::hasColumn('employees', 'portal_registered_at')) {
                $table->dropColumn('portal_registered_at');
            }

            if (Schema::hasColumn('employees', 'portal_password')) {
                $table->dropColumn('portal_password');
            }
        });
    }
};


