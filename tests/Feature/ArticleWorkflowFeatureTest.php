<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleWorkflowFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_asn_cannot_publish_articles(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $article = Artikel::factory()->create([
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
        ]);

        $response = $this->actingAs($asn)->post(route('admin.artikel.publish', $article));

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_admin_pages(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_approve_and_reject_submitted_articles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $article = Artikel::factory()->create([
            'status' => Artikel::STATUS_PENDING,
            'is_draft' => false,
        ]);

        $this->actingAs($admin)->post(route('admin.artikel.approve', $article))
            ->assertRedirect();

        $this->assertDatabaseHas('artikels', [
            'id' => $article->id,
            'status' => Artikel::STATUS_APPROVED,
        ]);

        $secondArticle = Artikel::factory()->create([
            'status' => Artikel::STATUS_PENDING,
            'is_draft' => false,
        ]);

        $this->actingAs($admin)->post(route('admin.artikel.reject', $secondArticle), [
            'review_note' => 'Butuh revisi substansi',
        ])->assertRedirect();

        $this->assertDatabaseHas('artikels', [
            'id' => $secondArticle->id,
            'status' => Artikel::STATUS_REJECTED,
            'review_note' => 'Butuh revisi substansi',
        ]);
    }

    public function test_rejected_articles_are_not_publicly_visible(): void
    {
        $article = Artikel::factory()->create([
            'status' => Artikel::STATUS_REJECTED,
            'is_draft' => false,
        ]);

        $this->get(route('artikel.show', $article))->assertNotFound();
    }

    public function test_published_articles_are_publicly_visible(): void
    {
        $article = Artikel::factory()->create([
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);

        $this->get(route('artikel.show', $article))
            ->assertOk()
            ->assertSee($article->judul);
    }

    public function test_admin_can_unpublish_and_permanently_delete_an_article(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $article = Artikel::factory()->create([
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('admin.artikel.unpublish', $article))->assertRedirect();

        $this->assertNull($article->fresh()->published_at);
        $this->get(route('artikel.show', $article))->assertNotFound();

        $this->actingAs($admin)->delete(route('admin.artikel.destroy', $article))
            ->assertRedirect(route('admin.artikel.validasi', ['status' => 'all']));

        $this->assertDatabaseMissing('artikels', ['id' => $article->id]);
    }

    public function test_asn_cannot_unpublish_or_delete_a_published_article(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $article = Artikel::factory()->create([
            'user_id' => $asn->id,
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);

        $this->actingAs($asn)->post(route('admin.artikel.unpublish', $article))->assertForbidden();
        $this->actingAs($asn)->delete(route('portal.artikel.destroy', $article))->assertForbidden();
        $this->assertDatabaseHas('artikels', ['id' => $article->id]);
    }

    public function test_asn_article_content_is_sanitized_when_stored(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);

        $this->actingAs($asn)->post(route('portal.artikel.store'), [
            'judul' => 'Artikel Dengan HTML',
            'konten' => '<p>Konten aman</p><script>alert("x")</script><p><a href="https://example.com">Tautan</a></p>',
            'category_id' => null,
            'tags' => [],
        ])->assertRedirect(route('portal.artikel.index'))
            ->assertSessionHas('clear_article_draft', 'portal-spbe:article-draft:'.$asn->id.':new');

        $savedArticle = Artikel::query()->where('judul', 'Artikel Dengan HTML')->firstOrFail();

        $this->assertStringContainsString('<p>Konten aman</p>', $savedArticle->konten);
        $this->assertStringNotContainsString('<script>', $savedArticle->konten);
    }

    public function test_asn_can_upload_article_media(): void
    {
        Storage::fake('public');

        $asn = User::factory()->create(['role' => 'asn']);
        $image = UploadedFile::fake()->image('portal.png');

        $this->actingAs($asn)->postJson(route('portal.artikel.media.store'), [
            'image' => $image,
        ])->assertOk()
            ->assertJsonStructure(['url', 'path', 'message'])
            ->assertJsonPath('url', '/storage/article-media/'.$image->hashName())
            ->assertJsonPath('path', 'article-media/'.$image->hashName());

        Storage::disk('public')->assertExists('article-media/'.$image->hashName());
    }

    public function test_article_media_upload_rejects_unsupported_or_oversized_files(): void
    {
        Storage::fake('public');

        $asn = User::factory()->create(['role' => 'asn']);

        $this->actingAs($asn)->postJson(route('portal.artikel.media.store'), [
            'image' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('image');

        $this->actingAs($asn)->postJson(route('portal.artikel.media.store'), [
            'image' => UploadedFile::fake()->image('besar.png')->size(6000),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('image');
    }

    public function test_uploaded_article_image_is_preserved_by_content_sanitizer(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);

        $this->actingAs($asn)->post(route('portal.artikel.store'), [
            'judul' => 'Artikel Multimedia',
            'konten' => '<p>Diagram arsitektur:</p><p><img src="/storage/article-media/diagram.png" alt="Diagram"></p>',
            'category_id' => null,
            'tags' => [],
        ])->assertRedirect(route('portal.artikel.index'));

        $this->assertStringContainsString(
            '/storage/article-media/diagram.png',
            Artikel::query()->where('judul', 'Artikel Multimedia')->firstOrFail()->konten
        );
    }

    public function test_article_image_size_is_preserved_but_restricted_to_supported_values(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);

        $this->actingAs($asn)->post(route('portal.artikel.store'), [
            'judul' => 'Artikel Dengan Ukuran Gambar',
            'konten' => '<p><img src="/storage/article-media/satu.png" width="50%"></p><p><img src="/storage/article-media/dua.png" width="9999"></p>',
            'category_id' => null,
            'tags' => [],
        ])->assertRedirect(route('portal.artikel.index'));

        $content = Artikel::query()->where('judul', 'Artikel Dengan Ukuran Gambar')->firstOrFail()->konten;

        $this->assertStringContainsString('width="50%"', $content);
        $this->assertStringNotContainsString('width="9999"', $content);
    }

    public function test_article_lists_quotes_and_all_uploaded_images_survive_sanitization(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $images = collect(range(1, 5))
            ->map(fn ($number) => '<p><img src="/storage/article-media/gambar-'.$number.'.png" width="50%"></p>')
            ->join('');

        $this->actingAs($asn)->post(route('portal.artikel.store'), [
            'judul' => 'Artikel Dengan Banyak Media',
            'konten' => '<blockquote>Kutipan penting</blockquote><ul><li>Poin pertama</li><li>Poin kedua</li></ul>'.$images,
            'category_id' => null,
            'tags' => [],
        ])->assertRedirect(route('portal.artikel.index'));

        $content = Artikel::query()->where('judul', 'Artikel Dengan Banyak Media')->firstOrFail()->konten;

        $this->assertStringContainsString('<blockquote>Kutipan penting</blockquote>', $content);
        $this->assertStringContainsString('<ul><li>Poin pertama</li><li>Poin kedua</li></ul>', $content);
        $this->assertSame(5, substr_count($content, '<img'));
    }

    public function test_asn_can_delete_only_their_own_draft_article(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $otherAsn = User::factory()->create(['role' => 'asn']);
        $draft = Artikel::factory()->create(['user_id' => $asn->id, 'is_draft' => true]);
        $otherDraft = Artikel::factory()->create(['user_id' => $otherAsn->id, 'is_draft' => true]);

        $this->actingAs($asn)->delete(route('portal.artikel.destroy', $otherDraft))->assertForbidden();

        $this->actingAs($asn)->delete(route('portal.artikel.destroy', $draft))
            ->assertRedirect(route('portal.artikel.index'))
            ->assertSessionHas('clear_article_draft', 'portal-spbe:article-draft:'.$asn->id.':'.$draft->id);

        $this->assertDatabaseMissing('artikels', ['id' => $draft->id]);
        $this->assertDatabaseHas('artikels', ['id' => $otherDraft->id]);
    }

    public function test_submitted_article_cannot_be_deleted_by_asn(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $article = Artikel::factory()->create([
            'user_id' => $asn->id,
            'is_draft' => false,
            'status' => Artikel::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $this->actingAs($asn)->delete(route('portal.artikel.destroy', $article))->assertForbidden();
        $this->assertDatabaseHas('artikels', ['id' => $article->id]);
    }

    public function test_article_list_shows_confirmation_copy_before_submission(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        Artikel::factory()->create(['user_id' => $asn->id, 'is_draft' => true]);

        $this->actingAs($asn)->get(route('portal.artikel.index'))
            ->assertOk()
            ->assertSee('Sudah yakin artikelnya siap?')
            ->assertSee('tidak dapat diedit atau dihapus');
    }

    public function test_admin_can_filter_and_paginate_article_review_queue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Artikel::factory()->count(11)->create([
            'status' => Artikel::STATUS_PENDING,
            'is_draft' => false,
            'submitted_at' => now(),
        ]);

        Artikel::factory()->create([
            'judul' => 'Artikel Siap Publikasi',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'approved_at' => now(),
            'published_at' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.artikel.validasi', ['status' => 'approved']))
            ->assertOk()
            ->assertSee('Artikel Siap Publikasi');

        $this->actingAs($admin)
            ->get(route('admin.artikel.validasi', [
                'status' => 'pending',
                'page' => 2,
            ]))
            ->assertOk()
            ->assertViewHas('artikels', fn ($artikels) => $artikels->currentPage() === 2);
    }

    public function test_admin_can_read_complete_article_content_before_reviewing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $article = Artikel::factory()->create([
            'judul' => 'Pedoman Lengkap SPBE',
            'konten' => '<h2>Bagian Utama</h2><p>Isi lengkap yang harus dibaca administrator sebelum mengambil keputusan editorial.</p><blockquote>Catatan kebijakan penting.</blockquote><ul><li>Poin verifikasi pertama</li><li>Poin verifikasi kedua</li></ul><p><img src="/storage/article-media/review.png" alt="Diagram review"></p>',
            'status' => Artikel::STATUS_PENDING,
            'is_draft' => false,
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.artikel.show', $article))
            ->assertOk()
            ->assertDontSee('Tidak dipotong')
            ->assertDontSee('Ringkasan cepat')
            ->assertSee('Isi lengkap yang harus dibaca administrator')
            ->assertSee('Catatan kebijakan penting.')
            ->assertSee('Poin verifikasi kedua')
            ->assertSee('/storage/article-media/review.png', false)
            ->assertSee(route('admin.artikel.approve', $article), false)
            ->assertSee(route('admin.artikel.reject', $article), false);
    }

    public function test_non_admin_can_not_open_internal_article_review_detail(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $article = Artikel::factory()->create();

        $this->actingAs($asn)
            ->get(route('admin.artikel.show', $article))
            ->assertForbidden();
    }

    public function test_admin_can_not_approve_or_reject_article_outside_pending_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $draft = Artikel::factory()->create([
            'status' => Artikel::STATUS_PENDING,
            'is_draft' => true,
        ]);
        $approved = Artikel::factory()->create([
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.artikel.approve', $draft))
            ->assertSessionHas('warning');

        $this->actingAs($admin)
            ->post(route('admin.artikel.reject', $approved), ['review_note' => 'Tidak boleh berubah'])
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('artikels', [
            'id' => $draft->id,
            'is_draft' => true,
            'status' => Artikel::STATUS_PENDING,
        ]);
        $this->assertDatabaseHas('artikels', [
            'id' => $approved->id,
            'status' => Artikel::STATUS_APPROVED,
            'review_note' => null,
        ]);
    }
}
