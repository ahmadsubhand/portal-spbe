<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsnWorkspaceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_asn_dashboard_shows_only_their_content_summary(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $otherAsn = User::factory()->create(['role' => 'asn']);

        Artikel::factory()->create(['user_id' => $asn->id, 'judul' => 'Draft Milik ASN', 'is_draft' => true]);
        Artikel::factory()->create(['user_id' => $otherAsn->id, 'judul' => 'Draft Milik Orang Lain', 'is_draft' => true]);

        $this->actingAs($asn)->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard ASN')
            ->assertSee('Draft Milik ASN')
            ->assertDontSee('Draft Milik Orang Lain');
    }

    public function test_asn_article_listing_can_filter_and_paginate(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        Artikel::factory()->count(11)->create(['user_id' => $asn->id, 'is_draft' => true]);

        $this->actingAs($asn)->get(route('portal.artikel.index', ['status' => 'draft', 'page' => 2]))
            ->assertOk()
            ->assertViewHas('artikels', fn ($artikels) => $artikels->currentPage() === 2);
    }

    public function test_admin_can_explicitly_return_to_admin_area_from_asn_workspace(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $asn = User::factory()->create(['role' => 'asn']);

        $this->actingAs($admin)
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee('Area Admin')
            ->assertSee('Portal Publik')
            ->assertSee(route('admin.dashboard'), false);

        $this->actingAs($asn)
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertDontSee('Area Admin');
    }

    public function test_admin_area_exposes_asn_workspace_and_public_portal_switches(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Area ASN')
            ->assertSee('Portal Publik')
            ->assertSee(route('portal.artikel.index'), false)
            ->assertSee(route('beranda'), false);
    }

    public function test_asn_can_manage_only_their_own_topics(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $otherAsn = User::factory()->create(['role' => 'asn']);
        $topic = Topic::create([
            'user_id' => $asn->id,
            'title' => 'Integrasi Data Antar Perangkat Daerah',
            'category' => 'Teknis',
            'content' => 'Membahas kebutuhan integrasi data antar perangkat daerah secara aman.',
        ]);
        $otherTopic = Topic::create([
            'user_id' => $otherAsn->id,
            'title' => 'Topik Milik ASN Lain Untuk Pengujian',
            'category' => 'Umum',
            'content' => 'Topik ini tidak boleh dikelola oleh ASN yang sedang masuk.',
        ]);

        $this->actingAs($asn)->get(route('forum.my-topics'))
            ->assertOk()
            ->assertSee($topic->title)
            ->assertDontSee($otherTopic->title);

        $this->actingAs($asn)->get(route('forum.edit', $otherTopic->id))->assertNotFound();
    }
}
