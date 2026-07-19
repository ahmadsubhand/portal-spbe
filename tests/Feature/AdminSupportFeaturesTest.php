<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSupportFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                Setting::PUBLIC_CONTACT_EMAIL => 'help@bogor.test',
                Setting::PUBLIC_SUPPORT_LABEL => 'Helpdesk SPBE',
                Setting::DASHBOARD_EMBED_URL => 'https://example.test/embed',
                Setting::KNOWLEDGE_BASE_STATUS => 'Segera hadir',
            ],
        ])->assertRedirect(route('admin.settings.index'));

        $this->assertDatabaseHas('settings', [
            'key' => Setting::PUBLIC_CONTACT_EMAIL,
            'value' => 'help@bogor.test',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'setting.updated',
        ]);
    }

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                Setting::PUBLIC_CONTACT_EMAIL => 'help@bogor.test',
                Setting::PUBLIC_SUPPORT_LABEL => '',
                Setting::DASHBOARD_EMBED_URL => '',
                Setting::KNOWLEDGE_BASE_STATUS => '',
            ],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee('setting.updated');
    }

    public function test_article_can_store_category_and_tags(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $this->actingAs($asn)->post(route('portal.artikel.store'), [
            'judul' => 'Artikel Taksonomi',
            'konten' => 'Konten artikel untuk diuji.',
            'category_id' => $category->id,
            'tags' => $tags->pluck('id')->all(),
        ])->assertRedirect(route('portal.artikel.index'));

        $artikel = Artikel::query()->where('judul', 'Artikel Taksonomi')->firstOrFail();

        $this->assertSame($category->id, $artikel->category_id);
        $this->assertCount(2, $artikel->tags);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'article.created',
        ]);
    }

    public function test_admin_can_filter_and_paginate_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (range(1, 21) as $index) {
            AuditLog::query()->create([
                'user_id' => $admin->id,
                'action' => $index === 21 ? 'article.published' : 'setting.updated',
                'subject_type' => Artikel::class,
                'subject_id' => $index,
                'created_at' => now()->subMinutes($index),
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index', ['action' => 'article.published']))
            ->assertOk()
            ->assertSee('Article Published');

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index', ['page' => 2]))
            ->assertOk()
            ->assertViewHas('logs', fn ($logs) => $logs->currentPage() === 2);
    }
}
