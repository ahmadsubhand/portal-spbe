<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ProfileDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'nip' => '199001012020121001',
                'phone_number' => '+628123456789',
                'unit_kerja' => 'Diskominfo Kota Bogor',
                'jabatan' => 'Pranata Komputer',
                'bidang_keahlian' => 'Tata kelola SPBE',
                'bio' => 'Berkontribusi pada pengembangan portal SPBE.',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('199001012020121001', $user->nip);
        $this->assertSame('Diskominfo Kota Bogor', $user->unit_kerja);
        $this->assertSame('Pranata Komputer', $user->jabatan);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_request_account_deactivation(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('profile.deactivation-request.store'), [
                'reason' => 'Saya telah berpindah unit kerja dan tidak lagi menggunakan portal ini.',
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('account_deactivation_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        $this->assertNotNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_request_deactivation(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->post(route('profile.deactivation-request.store'), [
                'reason' => 'Saya telah berpindah unit kerja dan tidak lagi menggunakan portal ini.',
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_user_can_upload_replace_and_delete_profile_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $firstAvatar = UploadedFile::fake()->image('avatar-one.jpg', 400, 400);

        $this->actingAs($user)
            ->put(route('profile.avatar.update'), ['avatar' => $firstAvatar])
            ->assertRedirect(route('profile.edit'));

        $firstPath = $user->fresh()->avatar_path;
        Storage::disk('public')->assertExists($firstPath);

        $secondAvatar = UploadedFile::fake()->image('avatar-two.png', 500, 500);

        $this->actingAs($user)
            ->put(route('profile.avatar.update'), ['avatar' => $secondAvatar])
            ->assertRedirect(route('profile.edit'));

        $secondPath = $user->fresh()->avatar_path;
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);

        $this->actingAs($user)
            ->delete(route('profile.avatar.destroy'))
            ->assertRedirect(route('profile.edit'));

        Storage::disk('public')->assertMissing($secondPath);
        $this->assertNull($user->fresh()->avatar_path);
    }

    public function test_profile_avatar_must_be_a_valid_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('profile.avatar.update'), [
                'avatar' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('avatar');
    }

    public function test_user_can_upload_download_and_delete_private_profile_document(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('profile.documents.store'), [
            'type' => 'competency_certificate',
            'title' => 'Sertifikat Kompetensi SPBE',
            'document' => UploadedFile::fake()->create('sertifikat.pdf', 250, 'application/pdf'),
        ])->assertRedirect(route('profile.edit'));

        $document = ProfileDocument::query()->firstOrFail();
        Storage::disk('local')->assertExists($document->path);

        $this->actingAs($user)
            ->get(route('profile.documents.download', $document))
            ->assertOk()
            ->assertDownload('sertifikat.pdf');

        $this->actingAs($user)
            ->delete(route('profile.documents.destroy', $document))
            ->assertRedirect(route('profile.edit'));

        Storage::disk('local')->assertMissing($document->path);
        $this->assertDatabaseMissing('profile_documents', ['id' => $document->id]);
    }

    public function test_other_user_cannot_access_private_profile_document(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $document = ProfileDocument::query()->create([
            'user_id' => $owner->id,
            'type' => 'training_certificate',
            'title' => 'Pelatihan SPBE',
            'path' => 'profile-documents/'.$owner->id.'/pelatihan.pdf',
            'original_name' => 'pelatihan.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
        ]);
        Storage::disk('local')->put($document->path, 'private');

        $this->actingAs($otherUser)->get(route('profile.documents.download', $document))->assertForbidden();
        $this->actingAs($otherUser)->delete(route('profile.documents.destroy', $document))->assertForbidden();
    }

    public function test_profile_document_rejects_unsupported_file_type(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('profile.documents.store'), [
            'type' => 'other_supporting',
            'title' => 'Dokumen Tidak Valid',
            'document' => UploadedFile::fake()->create('dokumen.zip', 100, 'application/zip'),
        ])->assertSessionHasErrors('document');
    }
}
