<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreManagedUserRequest;
use App\Http\Requests\UpdateManagedUserRequest;
use App\Models\User;
use App\Models\AccountDeactivationRequest;
use App\Http\Requests\ReviewAccountDeactivationRequest;
use App\Services\AuditLogService;
use App\Services\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $role = trim((string) $request->string('role'));
        $search = trim((string) $request->string('q'));

        $users = User::query()
            ->withCount(['artikels', 'topics'])
            ->when($role !== '', fn ($query) => $query->where('role', $role))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($userQuery) use ($search): void {
                    $userQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'role' => $role,
                'q' => $search,
            ],
            'roles' => ['admin', 'asn', 'guest'],
            'deactivationRequests' => AccountDeactivationRequest::query()
                ->with('user')
                ->where('status', AccountDeactivationRequest::STATUS_PENDING)
                ->oldest()
                ->paginate(5, ['*'], 'deactivation_page')
                ->withQueryString(),
            'stats' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'admin' => User::where('role', 'admin')->count(),
                'asn' => User::where('role', 'asn')->count(),
            ],
        ]);
    }

    public function approveDeactivation(
        ReviewAccountDeactivationRequest $request,
        AccountDeactivationRequest $deactivationRequest,
        AuditLogService $auditLogService
    ): RedirectResponse {
        abort_unless($deactivationRequest->status === AccountDeactivationRequest::STATUS_PENDING, 422);

        $deactivationRequest->user()->update(['is_active' => false]);
        $deactivationRequest->update([
            'status' => AccountDeactivationRequest::STATUS_APPROVED,
            'admin_note' => $request->validated('admin_note'),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);
        $auditLogService->record($request->user(), 'account.deactivation_approved', AccountDeactivationRequest::class, $deactivationRequest->id, [
            'target_user_id' => $deactivationRequest->user_id,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Pengajuan disetujui dan akun dinonaktifkan. Seluruh konten tetap dipertahankan.');
    }

    public function rejectDeactivation(
        ReviewAccountDeactivationRequest $request,
        AccountDeactivationRequest $deactivationRequest,
        AuditLogService $auditLogService
    ): RedirectResponse {
        abort_unless($deactivationRequest->status === AccountDeactivationRequest::STATUS_PENDING, 422);

        $deactivationRequest->update([
            'status' => AccountDeactivationRequest::STATUS_REJECTED,
            'admin_note' => $request->validated('admin_note'),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);
        $auditLogService->record($request->user(), 'account.deactivation_rejected', AccountDeactivationRequest::class, $deactivationRequest->id, [
            'target_user_id' => $deactivationRequest->user_id,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Pengajuan penonaktifan ditolak.');
    }

    public function store(StoreManagedUserRequest $request, UserManagementService $userManagementService): RedirectResponse
    {
        $this->authorize('create', User::class);

        $userManagementService->createUser($request->validated());

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil ditambahkan.');
    }

    public function update(UpdateManagedUserRequest $request, User $user, UserManagementService $userManagementService): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        if ($request->user()->is($user)) {
            if (! (bool) ($validated['is_active'] ?? false)) {
                return redirect()->route('admin.users.index')->withErrors([
                    'user' => 'Admin tidak dapat menonaktifkan akun sendiri.',
                ]);
            }

            if (($validated['role'] ?? null) !== 'admin') {
                return redirect()->route('admin.users.index')->withErrors([
                    'user' => 'Admin tidak dapat menurunkan role akun sendiri.',
                ]);
            }
        }

        $userManagementService->updateUser($user, $validated);

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil diperbarui.');
    }

    public function toggleActive(User $user, UserManagementService $userManagementService): RedirectResponse
    {
        $this->authorize('update', $user);

        if (request()->user()->is($user)) {
            return redirect()->route('admin.users.index')->withErrors([
                'user' => 'Admin tidak dapat mengubah status aktif akun sendiri dari tombol cepat.',
            ]);
        }

        $userManagementService->toggleActive($user);

        return redirect()->route('admin.users.index')->with('status', 'Status pengguna berhasil diperbarui.');
    }

    public function destroy(User $user, UserManagementService $userManagementService): RedirectResponse
    {
        $this->authorize('delete', $user);

        if (request()->user()->is($user)) {
            return redirect()->route('admin.users.index')->withErrors([
                'user' => 'Admin tidak dapat menghapus akun sendiri.',
            ]);
        }

        $userManagementService->deleteUser($user);

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil dihapus.');
    }
}
