<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use App\Models\Reply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicForumExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_public_forum_listing(): void
    {
        $author = User::factory()->create(['role' => 'asn']);

        $topic = Topic::create([
            'user_id' => $author->id,
            'title' => 'Sosialisasi Integrasi Data SIMP@N',
            'category' => 'Aplikasi SPBE',
            'content' => 'Diskusi ini membahas integrasi data lintas perangkat daerah.',
        ]);

        $this->get(route('forum.index'))
            ->assertOk()
            ->assertSee('Forum Diskusi')
            ->assertSee('Sembunyikan filter')
            ->assertSee($topic->title);
    }

    public function test_forum_listing_supports_combined_status_category_and_format_filters(): void
    {
        $author = User::factory()->create(['role' => 'asn']);
        $imageTopic = Topic::create([
            'user_id' => $author->id,
            'title' => 'Visualisasi Arsitektur SPBE',
            'category' => 'Arsitektur',
            'content' => '<p>Diagram</p><img src="/storage/article-media/arsitektur.png">',
        ]);
        Topic::create([
            'user_id' => $author->id,
            'title' => 'Diskusi Teks Umum',
            'category' => 'Umum',
            'content' => '<p>Diskusi tanpa media</p>',
        ]);

        $this->get(route('forum.index', [
            'category' => ['Arsitektur'],
            'status' => ['unanswered'],
            'format' => ['image'],
        ]))
            ->assertOk()
            ->assertSee($imageTopic->title)
            ->assertDontSee('Diskusi Teks Umum')
            ->assertSee('Belum dijawab');
    }

    public function test_guest_can_read_complete_open_forum_detail(): void
    {
        $author = User::factory()->create(['role' => 'asn', 'name' => 'Lisda Rahayu']);

        $topic = Topic::create([
            'user_id' => $author->id,
            'title' => 'Sosialisasi Integrasi Data SIMP@N',
            'category' => 'Aplikasi SPBE',
            'content' => 'Konten lengkap forum terbuka dapat dibaca publik.',
            'visibility' => 'open',
        ]);

        $this->get(route('forum.show', $topic->slug))
            ->assertOk()
            ->assertSee('Sosialisasi Integrasi Data SIMP@N')
            ->assertSee('Lisda Rahayu')
            ->assertSee('Konten lengkap forum terbuka dapat dibaca publik.')
            ->assertSee('Masuk untuk membaca')
            ->assertSee('ikut berdiskusi')
            ->assertSee('Kembali ke Forum')
            ->assertDontSee('Tulis Balasan');
    }

    public function test_guest_must_login_before_opening_closed_forum_detail(): void
    {
        $topic = Topic::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Diskusi Internal Pemerintah Kota',
            'category' => 'Teknis',
            'content' => 'Informasi internal yang tidak boleh dibaca publik.',
            'visibility' => 'closed',
        ]);

        $this->get(route('forum.show', $topic->slug))
            ->assertRedirect(route('login'));

        $this->get(route('forum.index'))
            ->assertOk()
            ->assertSee($topic->title)
            ->assertSee('Masuk untuk melihat isi diskusi tertutup.')
            ->assertDontSee('Informasi internal yang tidak boleh dibaca publik.');
    }

    public function test_asn_can_create_sanitized_multimedia_forum_topic(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);

        $this->actingAs($asn)->post(route('forum.store'), [
            'title' => 'Diskusi Multimedia Integrasi Data',
            'category' => 'Teknis',
            'content' => '<h2>Arsitektur</h2><p><img src="/storage/article-media/diagram.png" width="50%"></p><script>alert("x")</script>',
        ])->assertRedirect(route('forum.index'))
            ->assertSessionHas('clear_article_draft', 'portal-spbe:forum-draft:'.$asn->id.':new');

        $topic = Topic::query()->where('title', 'Diskusi Multimedia Integrasi Data')->firstOrFail();

        $this->assertStringContainsString('/storage/article-media/diagram.png', $topic->content);
        $this->assertStringContainsString('width="50%"', $topic->content);
        $this->assertStringNotContainsString('<script>', $topic->content);
    }

    public function test_forum_create_page_uses_rich_editor_and_preview(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);

        $this->actingAs($asn)->get(route('forum.create'))
            ->assertOk()
            ->assertSee('Preview')
            ->assertSee('data-quill-wrapper', false)
            ->assertSee('Atur ukuran gambar');
    }

    public function test_authenticated_user_can_like_and_reply_to_a_forum_reply(): void
    {
        $author = User::factory()->create(['role' => 'asn']);
        $responder = User::factory()->create(['role' => 'asn']);
        $topic = Topic::create([
            'user_id' => $author->id,
            'title' => 'Diskusi Interaksi Balasan Forum',
            'category' => 'Umum',
            'content' => 'Diskusi untuk menguji interaksi pada balasan.',
            'visibility' => 'open',
        ]);
        $reply = Reply::create([
            'topic_id' => $topic->id,
            'user_id' => $author->id,
            'content' => 'Balasan utama.',
        ]);

        $this->actingAs($responder)->post(route('forum.reply.like', $reply))->assertRedirect();
        $this->actingAs($responder)->post(route('forum.reply.store', $topic), [
            'parent_id' => $reply->id,
            'content' => 'Tanggapan terhadap balasan utama.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reply_likes', ['reply_id' => $reply->id, 'user_id' => $responder->id]);
        $this->assertDatabaseHas('replies', [
            'topic_id' => $topic->id,
            'parent_id' => $reply->id,
            'user_id' => $responder->id,
        ]);

        $this->actingAs($responder)->get(route('forum.show', $topic->slug))
            ->assertOk()
            ->assertSee('Tanggapan terhadap balasan utama.');
    }

    public function test_inactive_replies_are_hidden_and_cannot_be_liked_publicly(): void
    {
        $author = User::factory()->create(['role' => 'asn']);
        $responder = User::factory()->create(['role' => 'asn']);
        $topic = Topic::create([
            'user_id' => $author->id,
            'title' => 'Diskusi Dengan Balasan Nonaktif',
            'category' => 'Umum',
            'content' => 'Konten diskusi.',
            'visibility' => 'open',
        ]);
        $activeReply = Reply::create([
            'topic_id' => $topic->id,
            'user_id' => $author->id,
            'content' => 'Balasan aktif.',
        ]);
        $inactiveReply = Reply::create([
            'topic_id' => $topic->id,
            'user_id' => $author->id,
            'content' => 'Balasan nonaktif.',
            'is_active' => false,
        ]);

        $this->actingAs($responder)->get(route('forum.show', $topic->slug))
            ->assertOk()
            ->assertSee('Balasan aktif.')
            ->assertDontSee('Balasan nonaktif.');

        $this->actingAs($responder)->post(route('forum.reply.like', $inactiveReply))
            ->assertNotFound();

        $this->actingAs($responder)->post(route('forum.reply.store', $topic), [
            'parent_id' => $inactiveReply->id,
            'content' => 'Harus gagal karena parent nonaktif.',
        ])->assertNotFound();

        $this->assertDatabaseMissing('reply_likes', ['reply_id' => $inactiveReply->id, 'user_id' => $responder->id]);
        $this->assertDatabaseHas('replies', ['id' => $activeReply->id]);
    }

    public function test_owner_can_deactivate_reactivate_and_delete_a_published_topic(): void
    {
        $asn = User::factory()->create(['role' => 'asn']);
        $topic = Topic::create([
            'user_id' => $asn->id,
            'title' => 'Diskusi yang dapat dinonaktifkan',
            'category' => 'Umum',
            'content' => 'Konten diskusi.',
        ]);

        $this->actingAs($asn)
            ->get(route('forum.show', $topic->slug))
            ->assertOk()
            ->assertSee('Nonaktifkan topik ini dari portal?')
            ->assertSee('Hapus topik ini secara permanen?');

        $this->actingAs($asn)->patch(route('forum.toggle-active', $topic))->assertRedirect();

        $this->assertFalse($topic->fresh()->isActive());
        auth()->logout();
        $this->get(route('forum.index'))->assertDontSee($topic->title);
        $this->get(route('forum.show', $topic->slug))->assertNotFound();
        $this->actingAs($asn)->get(route('forum.show', $topic->slug))->assertOk();

        $this->actingAs($asn)->patch(route('forum.toggle-active', $topic))->assertRedirect();
        $this->assertTrue($topic->fresh()->isActive());

        $this->actingAs($asn)->delete(route('forum.destroy', $topic->id))->assertRedirect(route('forum.my-topics'));
        $this->assertDatabaseMissing('topics', ['id' => $topic->id]);
    }

    public function test_admin_can_moderate_topics_but_other_asn_cannot(): void
    {
        $owner = User::factory()->create(['role' => 'asn']);
        $otherAsn = User::factory()->create(['role' => 'asn']);
        $admin = User::factory()->create(['role' => 'admin']);
        $topic = Topic::create([
            'user_id' => $owner->id,
            'title' => 'Diskusi untuk moderasi admin',
            'category' => 'Umum',
            'content' => 'Konten diskusi.',
        ]);

        $this->actingAs($otherAsn)->patch(route('forum.toggle-active', $topic))->assertForbidden();
        $this->actingAs($admin)->patch(route('forum.toggle-active', $topic))->assertRedirect();
        $this->assertFalse($topic->fresh()->isActive());

        $this->actingAs($admin)->delete(route('forum.destroy', $topic->id))->assertRedirect(route('forum.index'));
        $this->assertDatabaseMissing('topics', ['id' => $topic->id]);
    }
}
