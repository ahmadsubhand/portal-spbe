<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicArticleExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_header_marks_the_current_section_as_active(): void
    {
        $response = $this->get(route('artikel.index'));

        $response
            ->assertOk()
            ->assertSee('href="'.route('artikel.index').'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('Portal Pengetahuan SPBE')
            ->assertSee('Hubungi Pengelola');

        $html = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/href="'.preg_quote(route('artikel.index'), '/').'"[^>]*aria-current="page"/',
            $html
        );
        $this->assertDoesNotMatchRegularExpression(
            '/href="'.preg_quote(route('beranda'), '/').'"[^>]*aria-current="page"/',
            $html
        );
    }

    public function test_public_article_index_supports_search(): void
    {
        Artikel::factory()->create([
            'judul' => 'Tata Kelola SPBE Kota Bogor',
            'konten' => 'Pembahasan tata kelola dan arsitektur SPBE.',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);

        Artikel::factory()->create([
            'judul' => 'Panduan Arsip Internal',
            'konten' => 'Materi non relevan untuk pencarian ini.',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('artikel.index', ['search' => 'Bogor']))
            ->assertOk()
            ->assertSee('Tata Kelola SPBE Kota Bogor')
            ->assertDontSee('Panduan Arsip Internal');
    }

    public function test_public_article_index_supports_category_and_tag_filters(): void
    {
        $category = Category::factory()->create([
            'name' => 'Layanan SPBE',
            'slug' => 'layanan-spbe',
        ]);
        $otherCategory = Category::factory()->create([
            'name' => 'Keamanan Informasi',
            'slug' => 'keamanan-informasi',
        ]);
        $tag = Tag::factory()->create([
            'name' => 'Integrasi',
            'slug' => 'integrasi',
        ]);
        $otherTag = Tag::factory()->create([
            'name' => 'Audit',
            'slug' => 'audit',
        ]);

        $matchingArticle = Artikel::factory()->create([
            'category_id' => $category->id,
            'judul' => 'Integrasi Layanan Kota',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);
        $matchingArticle->tags()->sync([$tag->id]);

        $otherArticle = Artikel::factory()->create([
            'category_id' => $otherCategory->id,
            'judul' => 'Audit Infrastruktur',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now()->subDay(),
        ]);
        $otherArticle->tags()->sync([$otherTag->id]);

        $this->get(route('artikel.index', [
            'category' => 'layanan-spbe',
            'tag' => 'integrasi',
        ]))
            ->assertOk()
            ->assertSee('Integrasi Layanan Kota')
            ->assertDontSee('Audit Infrastruktur');
    }

    public function test_guest_article_detail_shows_metadata_and_summary_but_hides_complete_content(): void
    {
        $author = User::factory()->create(['name' => 'Faris Ariyanto']);
        $category = Category::factory()->create(['name' => 'Penyelenggara SPBE']);
        $tag = Tag::factory()->create(['name' => 'AI']);

        $article = Artikel::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'judul' => 'Coding dengan Asisten AI',
            'konten' => 'Artikel ini membahas cara menggunakan asisten AI untuk penyusunan dokumen SPBE.',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);
        $article->tags()->sync([$tag->id]);

        $this->get(route('artikel.show', $article))
            ->assertOk()
            ->assertSee('Coding dengan Asisten AI')
            ->assertSee('Faris Ariyanto')
            ->assertSee('Penyelenggara SPBE')
            ->assertSee('Artikel ini membahas cara menggunakan asisten AI')
            ->assertSee('Masuk untuk membaca artikel lengkap')
            ->assertSee('Minta akses');
    }

    public function test_authenticated_user_can_read_complete_published_article(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $article = Artikel::factory()->create([
            'judul' => 'Konten Internal Lengkap',
            'konten' => '<h2>Bagian publikasi</h2><p>Isi lengkap yang hanya ditampilkan setelah pengguna masuk.</p>',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);

        $this->actingAs($asn)
            ->get(route('artikel.show', $article))
            ->assertOk()
            ->assertSee('Isi lengkap yang hanya ditampilkan setelah pengguna masuk.')
            ->assertDontSee('Masuk untuk membaca artikel lengkap');
    }

    public function test_guest_summary_does_not_expose_content_beyond_preview_limit(): void
    {
        $article = Artikel::factory()->create([
            'konten' => str_repeat('kata ringkasan ', 60).'RAHASIA_BAGIAN_AKHIR',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);

        $this->get(route('artikel.show', $article))
            ->assertOk()
            ->assertDontSee('RAHASIA_BAGIAN_AKHIR');
    }

    public function test_article_index_supports_multiple_categories_and_content_format_filters(): void
    {
        $imageCategory = Category::factory()->create(['slug' => 'visualisasi']);
        $videoCategory = Category::factory()->create(['slug' => 'pelatihan']);

        Artikel::factory()->create([
            'category_id' => $imageCategory->id,
            'judul' => 'Panduan Bergambar',
            'konten' => '<p>Panduan</p><img src="/storage/article-media/panduan.png">',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);
        Artikel::factory()->create([
            'category_id' => $videoCategory->id,
            'judul' => 'Pelatihan Video',
            'konten' => '<p>Pelatihan</p><iframe src="https://youtube.com/embed/abc"></iframe>',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);
        Artikel::factory()->create([
            'judul' => 'Dokumen Teks',
            'konten' => '<p>Hanya teks</p>',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);

        $this->get(route('artikel.index', [
            'category' => ['visualisasi', 'pelatihan'],
            'format' => ['image', 'video'],
        ]))
            ->assertOk()
            ->assertSee('Panduan Bergambar')
            ->assertSee('Pelatihan Video')
            ->assertDontSee('Dokumen Teks')
            ->assertSee('Sembunyikan filter');
    }
}
