<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminForumModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_forum_pages(): void
    {
        $this->get(route('admin.forum.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_and_filter_forum_moderation_queue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activeTopic = Topic::create([
            'user_id' => User::factory()->create(['role' => 'asn'])->id,
            'title' => 'Diskusi Integrasi Aplikasi',
            'category' => 'Teknis',
            'content' => 'Konten diskusi integrasi aplikasi.',
            'visibility' => 'open',
            'is_active' => true,
        ]);
        Topic::create([
            'user_id' => User::factory()->create(['role' => 'asn'])->id,
            'title' => 'Diskusi Yang Dinonaktifkan',
            'category' => 'Umum',
            'content' => 'Topik ini akan difilter sebagai nonaktif.',
            'visibility' => 'closed',
            'is_active' => false,
        ]);

        Reply::create([
            'topic_id' => $activeTopic->id,
            'user_id' => $activeTopic->user_id,
            'content' => 'Balasan pertama.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.forum.index'))
            ->assertOk()
            ->assertSee('Moderasi Forum')
            ->assertSee('Diskusi Integrasi Aplikasi')
            ->assertSee('Diskusi Yang Dinonaktifkan');

        $this->actingAs($admin)
            ->get(route('admin.forum.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('Diskusi Yang Dinonaktifkan')
            ->assertDontSee('Diskusi Integrasi Aplikasi');
    }

    public function test_admin_can_deactivate_reactivate_and_delete_topics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $topic = Topic::create([
            'user_id' => User::factory()->create(['role' => 'asn'])->id,
            'title' => 'Topik Untuk Moderasi',
            'category' => 'Teknis',
            'content' => 'Konten topik.',
            'visibility' => 'open',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.forum.toggle-active', $topic))
            ->assertRedirect();

        $this->assertFalse($topic->fresh()->isActive());
        auth()->logout();
        $this->get(route('forum.show', $topic->slug))->assertNotFound();

        $this->actingAs($admin)
            ->patch(route('admin.forum.toggle-active', $topic))
            ->assertRedirect();

        $this->assertTrue($topic->fresh()->isActive());

        $this->actingAs($admin)
            ->delete(route('admin.forum.destroy', $topic))
            ->assertRedirect(route('admin.forum.index'));

        $this->assertDatabaseMissing('topics', ['id' => $topic->id]);
    }

    public function test_topics_with_null_active_flag_are_treated_as_active(): void
    {
        $topic = new Topic();
        $topic->forceFill(['is_active' => null]);

        $this->assertTrue($topic->isActive());
    }

    public function test_admin_can_deactivate_reactivate_and_delete_replies(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $topic = Topic::create([
            'user_id' => User::factory()->create(['role' => 'asn'])->id,
            'title' => 'Topik Untuk Moderasi Balasan',
            'category' => 'Umum',
            'content' => 'Konten topik.',
            'visibility' => 'open',
        ]);
        $reply = Reply::create([
            'topic_id' => $topic->id,
            'user_id' => $topic->user_id,
            'content' => 'Balasan yang akan dimoderasi.',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.forum.reply.toggle-active', $reply))
            ->assertRedirect();

        $this->assertFalse($reply->fresh()->isActive());

        $this->actingAs($admin)
            ->patch(route('admin.forum.reply.toggle-active', $reply))
            ->assertRedirect();

        $this->assertTrue($reply->fresh()->isActive());

        $this->actingAs($admin)
            ->delete(route('admin.forum.reply.destroy', $reply))
            ->assertRedirect();

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    public function test_admin_reply_moderation_actions_require_browser_confirmation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $topic = Topic::create([
            'user_id' => User::factory()->create(['role' => 'asn'])->id,
            'title' => 'Topik Dengan Konfirmasi Moderasi',
            'category' => 'Umum',
            'content' => 'Konten topik.',
            'visibility' => 'open',
        ]);
        Reply::create([
            'topic_id' => $topic->id,
            'user_id' => $topic->user_id,
            'content' => 'Balasan yang memerlukan konfirmasi.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.forum.show', $topic))
            ->assertOk()
            ->assertSee('Nonaktifkan balasan ini dari portal publik?')
            ->assertSee('Hapus balasan ini secara permanen?');
    }
}
