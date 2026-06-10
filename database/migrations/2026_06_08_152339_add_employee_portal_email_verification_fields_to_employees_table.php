<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'portal_email')) {
                $table->string('portal_email')->nullable()->after('portal_password')->index();
            }

            if (!Schema::hasColumn('employees', 'portal_email_verified_at')) {
                $table->timestamp('portal_email_verified_at')->nullable()->after('portal_email');
            }

            if (!Schema::hasColumn('employees', 'portal_email_verification_code')) {
                $table->string('portal_email_verification_code')->nullable()->after('portal_email_verified_at');
            }

            if (!Schema::hasColumn('employees', 'portal_email_verification_expires_at')) {
                $table->timestamp('portal_email_verification_expires_at')->nullable()->after('portal_email_verification_code');
            }

            if (!Schema::hasColumn('employees', 'portal_password_reset_code')) {
                $table->string('portal_password_reset_code')->nullable()->after('portal_email_verification_expires_at');
            }

            if (!Schema::hasColumn('employees', 'portal_password_reset_expires_at')) {
                $table->timestamp('portal_password_reset_expires_at')->nullable()->after('portal_password_reset_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            foreach ([
                         'portal_password_reset_expires_at',
                         'portal_password_reset_code',
                         'portal_email_verification_expires_at',
                         'portal_email_verification_code',
                         'portal_email_verified_at',
                         'portal_email',
                     ] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
