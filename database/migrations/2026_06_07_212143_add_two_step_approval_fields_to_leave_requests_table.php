<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'workflow_status')) {
                $table->string('workflow_status')
                    ->default('pending_manager')
                    ->after('status');
            }

            if (!Schema::hasColumn('leave_requests', 'direct_manager_status')) {
                $table->string('direct_manager_status')
                    ->default('pending')
                    ->after('workflow_status');
            }

            if (!Schema::hasColumn('leave_requests', 'direct_manager_approved_by')) {
                $table->foreignId('direct_manager_approved_by')
                    ->nullable()
                    ->after('direct_manager_status')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('leave_requests', 'direct_manager_approved_at')) {
                $table->timestamp('direct_manager_approved_at')->nullable()->after('direct_manager_approved_by');
            }

            if (!Schema::hasColumn('leave_requests', 'direct_manager_rejected_by')) {
                $table->foreignId('direct_manager_rejected_by')
                    ->nullable()
                    ->after('direct_manager_approved_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('leave_requests', 'direct_manager_rejected_at')) {
                $table->timestamp('direct_manager_rejected_at')->nullable()->after('direct_manager_rejected_by');
            }

            if (!Schema::hasColumn('leave_requests', 'direct_manager_reject_reason')) {
                $table->text('direct_manager_reject_reason')->nullable()->after('direct_manager_rejected_at');
            }

            if (!Schema::hasColumn('leave_requests', 'hr_status')) {
                $table->string('hr_status')
                    ->default('waiting_manager')
                    ->after('direct_manager_reject_reason');
            }

            if (!Schema::hasColumn('leave_requests', 'hr_approved_by')) {
                $table->foreignId('hr_approved_by')
                    ->nullable()
                    ->after('hr_status')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('leave_requests', 'hr_approved_at')) {
                $table->timestamp('hr_approved_at')->nullable()->after('hr_approved_by');
            }

            if (!Schema::hasColumn('leave_requests', 'hr_rejected_by')) {
                $table->foreignId('hr_rejected_by')
                    ->nullable()
                    ->after('hr_approved_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('leave_requests', 'hr_rejected_at')) {
                $table->timestamp('hr_rejected_at')->nullable()->after('hr_rejected_by');
            }

            if (!Schema::hasColumn('leave_requests', 'hr_reject_reason')) {
                $table->text('hr_reject_reason')->nullable()->after('hr_rejected_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            foreach ([
                         'hr_reject_reason',
                         'hr_rejected_at',
                         'hr_rejected_by',
                         'hr_approved_at',
                         'hr_approved_by',
                         'hr_status',
                         'direct_manager_reject_reason',
                         'direct_manager_rejected_at',
                         'direct_manager_rejected_by',
                         'direct_manager_approved_at',
                         'direct_manager_approved_by',
                         'direct_manager_status',
                         'workflow_status',
                     ] as $column) {
                if (Schema::hasColumn('leave_requests', $column)) {
                    if (in_array($column, ['direct_manager_approved_by', 'direct_manager_rejected_by', 'hr_approved_by', 'hr_rejected_by'])) {
                        $table->dropConstrainedForeignId($column);
                    } else {
                        $table->dropColumn($column);
                    }
                }
            }
        });
    }
};
