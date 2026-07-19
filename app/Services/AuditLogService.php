<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogService
{
    public function record(User $actor, string $action, string $subjectType, ?int $subjectId = null, array $metadata = []): void
    {
        AuditLog::query()->create([
            'user_id' => $actor->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'metadata' => $metadata !== [] ? $metadata : null,
            'created_at' => now(),
        ]);
    }
}
