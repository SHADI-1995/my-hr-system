<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('employees', 'portal_email') && Schema::hasColumn('employees', 'email')) {
            DB::table('employees')
                ->whereNull('email')
                ->whereNotNull('portal_email')
                ->update([
                    'email' => DB::raw('portal_email'),
                ]);
        }

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'portal_email')) {
                $table->dropColumn('portal_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'portal_email')) {
                $table->string('portal_email')->nullable()->after('portal_password')->index();
            }
        });
    }
};
