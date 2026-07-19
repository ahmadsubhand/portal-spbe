<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;

class SettingService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {
    }

    public function updateSettings(User $actor, array $settings): void
    {
        foreach ($settings as $key => $value) {
            $setting = Setting::query()->firstOrNew(['key' => $key]);
            $setting->fill([
                'value' => is_scalar($value) || $value === null ? (string) ($value ?? '') : json_encode($value),
                'updated_by' => $actor->id,
            ])->save();

            $this->auditLogService->record(
                $actor,
                'setting.updated',
                Setting::class,
                $setting->id,
                ['key' => $key]
            );
        }
    }
}
