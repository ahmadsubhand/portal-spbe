<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;

class UserManagementService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {
    }

    public function createUser(array $data): User
    {
        $user = User::query()->create([
            ...Arr::except($data, ['password_confirmation']),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $this->auditLogService->record(auth()->user(), 'user.created', User::class, $user->id, [
            'role' => $user->role,
            'is_active' => $user->is_active,
        ]);

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        $payload = [
            ...Arr::except($data, ['password', 'password_confirmation']),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];

        if (blank($data['password'] ?? null)) {
            unset($payload['password']);
        }

        $user->update($payload);

        $this->auditLogService->record(auth()->user(), 'user.updated', User::class, $user->id, [
            'role' => $user->role,
            'is_active' => $user->is_active,
        ]);

        return $user->refresh();
    }

    public function toggleActive(User $user): User
    {
        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        $this->auditLogService->record(auth()->user(), 'user.toggled_active', User::class, $user->id, [
            'is_active' => $user->is_active,
        ]);

        return $user->refresh();
    }

    public function deleteUser(User $user): void
    {
        $this->auditLogService->record(auth()->user(), 'user.deleted', User::class, $user->id, [
            'email' => $user->email,
        ]);

        $user->delete();
    }
}
