<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\TaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_categories_and_tags(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Keamanan',
            'description' => 'Kategori keamanan informasi.',
        ])->assertRedirect(route('admin.taxonomy.index'));

        $category = Category::query()->where('name', 'Keamanan')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Keamanan SPBE',
            'description' => 'Kategori keamanan SPBE terbaru.',
        ])->assertRedirect(route('admin.taxonomy.index'));

        $this->actingAs($admin)->post(route('admin.tags.store'), [
            'name' => 'Audit',
        ])->assertRedirect(route('admin.taxonomy.index'));

        $tag = Tag::query()->where('name', 'Audit')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.tags.update', $tag), [
            'name' => 'Audit Internal',
        ])->assertRedirect(route('admin.taxonomy.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Keamanan SPBE',
            'slug' => 'keamanan-spbe',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'Audit Internal',
            'slug' => 'audit-internal',
        ]);
    }

    public function test_asn_cannot_access_admin_taxonomy_page(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);

        $this->actingAs($asn)
            ->get(route('admin.taxonomy.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_admin_taxonomy_page(): void
    {
        $this->get(route('admin.taxonomy.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_search_and_paginate_taxonomy_lists_independently(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Category::factory()->count(9)->create();
        Category::factory()->create(['name' => 'Keamanan Khusus', 'slug' => 'keamanan-khusus']);
        Tag::factory()->count(13)->create();
        Tag::factory()->create(['name' => 'Audit Khusus', 'slug' => 'audit-khusus']);

        $this->actingAs($admin)
            ->get(route('admin.taxonomy.index', [
                'category_q' => 'Keamanan Khusus',
                'tag_q' => 'Audit Khusus',
            ]))
            ->assertOk()
            ->assertSee('Keamanan Khusus')
            ->assertSee('Audit Khusus');

        $this->actingAs($admin)
            ->get(route('admin.taxonomy.index', [
                'categories_page' => 2,
                'tags_page' => 2,
            ]))
            ->assertOk()
            ->assertViewHas('categories', fn ($categories) => $categories->currentPage() === 2)
            ->assertViewHas('tags', fn ($tags) => $tags->currentPage() === 2);
    }

    public function test_taxonomy_seeder_provides_relevant_spbe_categories_and_labels_idempotently(): void
    {
        $this->seed(TaxonomySeeder::class);
        $this->seed(TaxonomySeeder::class);

        $this->assertDatabaseHas('categories', ['name' => 'Tata Kelola SPBE']);
        $this->assertDatabaseHas('categories', ['name' => 'Keamanan Informasi']);
        $this->assertDatabaseHas('tags', ['name' => 'Arsitektur SPBE']);
        $this->assertDatabaseHas('tags', ['name' => 'Manajemen Pengetahuan']);

        $this->assertSame(10, Category::count());
        $this->assertSame(10, Tag::count());
    }
}
