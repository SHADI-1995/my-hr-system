<?php

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('audit_log')) {
    function audit_log(
        string $action,
        ?string $module = null,
        ?string $modelType = null,
        ?int $modelId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $userId = null;

        if (Auth::check()) {
            $userId = Auth::id();
        } elseif (request()->user()) {
            $userId = request()->user()->id;
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
