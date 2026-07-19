<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHomepageExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_presents_primary_public_services_and_integration_previews(): void
    {
        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee('Sistem Manajemen Pengetahuan SPBE')
            ->assertSee('tugu-bogor-adhit-murridwan.jpg', false)
            ->assertSee('Foto: Adhit Murridwan / Unsplash')
            ->assertSee('Basis Pengetahuan')
            ->assertSee('Dashboard')
            ->assertDontSee('Neo4j')
            ->assertDontSee('Looker Studio')
            ->assertSee(route('artikel.index'), false)
            ->assertSee(route('forum.index'), false);
    }

    public function test_homepage_uses_real_published_article_category_and_forum_data(): void
    {
        $category = Category::factory()->create(['name' => 'Arsitektur SPBE']);
        $article = Artikel::factory()->create([
            'category_id' => $category->id,
            'judul' => 'Pedoman Arsitektur Kota Bogor',
            'status' => Artikel::STATUS_APPROVED,
            'is_draft' => false,
            'published_at' => now(),
        ]);
        $draft = Artikel::factory()->create([
            'judul' => 'Draft Tidak Publik',
            'is_draft' => true,
        ]);
        $author = User::factory()->create();
        $topic = Topic::create([
            'user_id' => $author->id,
            'title' => 'Diskusi Integrasi Antar Layanan',
            'category' => 'Integrasi',
            'content' => 'Pembahasan integrasi layanan.',
        ]);

        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee($article->judul)
            ->assertDontSee($draft->judul)
            ->assertSee($category->name)
            ->assertSee($topic->title);
    }
}
