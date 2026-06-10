<?php

namespace App\Traits;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            if (!function_exists('audit_log')) {
                return;
            }

            audit_log(
                'create',
                static::getAuditModuleName(),
                class_basename($model),
                $model->id,
                'تمت إضافة سجل جديد',
                null,
                static::cleanAuditValues($model->toArray())
            );
        });

        static::updated(function ($model) {
            if (!function_exists('audit_log')) {
                return;
            }

            $changes = $model->getChanges();

            unset($changes['updated_at']);

            $changes = static::cleanAuditValues($changes);

            if (empty($changes)) {
                return;
            }

            $oldValues = [];

            foreach ($changes as $field => $newValue) {
                $oldValues[$field] = $model->getOriginal($field);
            }

            $oldValues = static::cleanAuditValues($oldValues);

            audit_log(
                'update',
                static::getAuditModuleName(),
                class_basename($model),
                $model->id,
                'تم تعديل سجل',
                $oldValues,
                $changes
            );
        });

        static::deleted(function ($model) {
            if (!function_exists('audit_log')) {
                return;
            }

            audit_log(
                'delete',
                static::getAuditModuleName(),
                class_basename($model),
                $model->id,
                'تم حذف سجل',
                static::cleanAuditValues($model->toArray()),
                null
            );
        });
    }

    protected static function getAuditModuleName()
    {
        return strtolower(class_basename(static::class));
    }

    protected static function cleanAuditValues(array $values): array
    {
        $hiddenFields = [
            'password',
            'remember_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'api_token',
            'token',
            'access_token',
            'refresh_token',
        ];

        foreach ($hiddenFields as $field) {
            if (array_key_exists($field, $values)) {
                $values[$field] = '*** مخفي لأسباب أمنية ***';
            }
        }

        return $values;
    }
}
