<?php

namespace Tests\Feature;

use App\Models\AccountDeactivationRequest;
use App\Models\Artikel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Operator Baru',
            'email' => 'operator.baru@example.test',
            'role' => 'asn',
            'password' => 'secretpass',
            'password_confirmation' => 'secretpass',
            'is_active' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $managedUser = User::query()->where('email', 'operator.baru@example.test')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.users.update', $managedUser), [
            'name' => 'Operator Diperbarui',
            'email' => 'operator.baru@example.test',
            'role' => 'admin',
            'password' => '',
            'password_confirmation' => '',
            'is_active' => '0',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'operator.baru@example.test',
            'name' => 'Operator Diperbarui',
            'role' => 'admin',
            'is_active' => false,
        ]);
    }

    public function test_asn_cannot_access_user_management(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);

        $this->actingAs($asn)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_user_management(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_inactive_user_cannot_login(): void
    {
        $inactiveUser = User::factory()->create([
            'email' => 'inactive@example.test',
            'password' => 'password',
            'is_active' => false,
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $inactiveUser->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_cannot_deactivate_or_demote_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $deactivateResponse = $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'admin',
            'password' => '',
            'password_confirmation' => '',
            'is_active' => '0',
        ]);

        $deactivateResponse->assertRedirect(route('admin.users.index'));
        $deactivateResponse->assertSessionHasErrors('user');
        $this->assertTrue($admin->fresh()->is_active);

        $demoteResponse = $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'asn',
            'password' => '',
            'password_confirmation' => '',
            'is_active' => '1',
        ]);

        $demoteResponse->assertRedirect(route('admin.users.index'));
        $demoteResponse->assertSessionHasErrors('user');
        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_admin_can_toggle_and_delete_other_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $managedUser = User::factory()->create([
            'role' => 'asn',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $managedUser))
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($managedUser->fresh()->is_active);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $managedUser))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $managedUser->id,
        ]);
    }

    public function test_admin_can_approve_deactivation_without_deleting_user_content(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $asn = User::factory()->create(['role' => 'asn', 'is_active' => true]);
        $article = Artikel::factory()->create(['user_id' => $asn->id]);
        $request = AccountDeactivationRequest::query()->create([
            'user_id' => $asn->id,
            'reason' => 'Saya telah berpindah instansi dan tidak lagi menggunakan akun portal.',
            'status' => AccountDeactivationRequest::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.deactivation-requests.approve', $request), ['admin_note' => 'Disetujui setelah verifikasi.'])
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($asn->fresh()->is_active);
        $this->assertDatabaseHas('users', ['id' => $asn->id]);
        $this->assertDatabaseHas('artikels', ['id' => $article->id, 'user_id' => $asn->id]);
        $this->assertDatabaseHas('account_deactivation_requests', [
            'id' => $request->id,
            'status' => AccountDeactivationRequest::STATUS_APPROVED,
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_admin_cannot_permanently_delete_user_with_content(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $asn = User::factory()->create(['role' => 'asn']);
        Artikel::factory()->create(['user_id' => $asn->id]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $asn))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $asn->id]);
    }

    public function test_admin_can_reject_and_user_can_cancel_pending_deactivation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $asn = User::factory()->create(['role' => 'asn']);
        $rejected = AccountDeactivationRequest::query()->create([
            'user_id' => $asn->id,
            'reason' => 'Pengajuan pertama untuk diuji penolakannya oleh admin.',
            'status' => AccountDeactivationRequest::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.deactivation-requests.reject', $rejected), ['admin_note' => 'Masih dibutuhkan.'])
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue($asn->fresh()->is_active);
        $this->assertSame(AccountDeactivationRequest::STATUS_REJECTED, $rejected->fresh()->status);

        $pending = AccountDeactivationRequest::query()->create([
            'user_id' => $asn->id,
            'reason' => 'Pengajuan kedua untuk diuji pembatalannya oleh pengguna.',
            'status' => AccountDeactivationRequest::STATUS_PENDING,
        ]);

        $this->actingAs($asn)
            ->delete(route('profile.deactivation-request.cancel'))
            ->assertRedirect(route('profile.edit'));

        $this->assertSame(AccountDeactivationRequest::STATUS_CANCELLED, $pending->fresh()->status);
    }
}
