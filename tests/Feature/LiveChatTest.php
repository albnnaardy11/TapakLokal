<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\User;
use App\Services\AccessService;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LiveChatTest extends TestCase
{
    public function test_existing_unanswered_chat_receives_one_greeting_when_reopened(): void
    {
        $ticket = SupportTicket::factory()->create();
        $ticket->messages()->create(['user_id' => $ticket->user_id, 'body' => 'malam kak']);
        $this->actingAs($ticket->user)->getJson(route('support.show', $ticket))->assertOk()->assertJsonPath('messages.data.0.is_automatic', true);
        $this->getJson(route('support.show', $ticket))->assertOk();
        $this->postJson(route('support.reply', $ticket), ['body' => 'malam'])->assertCreated();
        $this->getJson(route('support.show', $ticket))->assertOk();
        $this->assertSame(1, $ticket->messages()->where('is_automatic', true)->count());
    }

    public function test_existing_chat_with_staff_reply_does_not_receive_an_automatic_greeting(): void
    {
        $ticket = SupportTicket::factory()->create();
        $staff = User::factory()->create();
        $ticket->messages()->create(['user_id' => $ticket->user_id, 'body' => 'Halo']);
        $ticket->messages()->create(['user_id' => $staff->id, 'body' => 'Ada yang bisa dibantu?']);
        $this->actingAs($ticket->user)->getJson(route('support.show', $ticket))->assertOk();
        $this->assertSame(0, $ticket->messages()->where('is_automatic', true)->count());
    }

    public function test_automatic_greeting_happens_once_and_only_the_recipient_marks_messages_read(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create();
        app(AccessService::class)->grant($admin, 'operations_admin');
        $response = $this->actingAs($customer)->postJson(route('support.store'), ['subject' => 'Chat', 'category' => 'other', 'body' => 'Halo'])->assertCreated();
        $ticket = SupportTicket::findOrFail($response->json('ticket.id'));
        $message = $ticket->messages()->where('is_automatic', false)->firstOrFail();
        $this->assertSame(1, $ticket->messages()->where('is_automatic', true)->count());
        $this->getJson(route('support.show', $ticket))->assertOk();
        $this->assertNull($message->fresh()->read_at);
        $this->actingAs(User::factory()->create())->getJson(route('support.show', $ticket))->assertNotFound();
        $this->assertNull($message->fresh()->read_at);
        $this->actingAs($admin)->getJson(route('support.show', $ticket))->assertOk();
        $this->assertNotNull($message->fresh()->read_at);
        $reply = $this->postJson(route('support.reply', $ticket), ['body' => 'Ada yang bisa dibantu?'])->assertCreated();
        $this->getJson(route('support.show', $ticket))->assertOk();
        $this->assertDatabaseHas('support_messages', ['id' => $reply->json('message.id'), 'read_at' => null]);
        $this->actingAs($customer)->getJson(route('support.show', $ticket))->assertOk();
        $this->assertDatabaseMissing('support_messages', ['id' => $reply->json('message.id'), 'read_at' => null]);
        $this->postJson(route('support.reply', $ticket), ['body' => 'Terima kasih'])->assertCreated();
        $this->assertSame(1, $ticket->messages()->where('is_automatic', true)->count());
    }

    public function test_short_first_message_reaches_admin_and_admin_reply_reaches_customer(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create();
        app(AccessService::class)->grant($admin, 'operations_admin');
        $response = $this->actingAs($customer)->postJson(route('support.store'), ['subject' => 'Bantuan TapakLokal', 'category' => 'other', 'body' => 'Hi'])->assertCreated();
        $ticket = SupportTicket::findOrFail($response->json('ticket.id'));
        $this->actingAs($admin)->get(route('admin.panel.resources.index', ['panel' => 'operations', 'module' => 'support']))->assertOk()->assertInertia(fn (AssertableInertia $page) => $page->component('Admin/Resources')->where('records.data.0.id', $ticket->id));
        $this->getJson(route('support.show', $ticket))->assertOk()->assertJsonFragment(['body' => 'Hi']);
        $this->postJson(route('support.reply', $ticket), ['body' => 'Halo, ada yang bisa kami bantu?'])->assertCreated();
        $this->actingAs($customer)->getJson(route('support.show', $ticket))->assertOk()->assertJsonPath('messages.data.0.body', 'Halo, ada yang bisa kami bantu?')->assertJsonPath('messages.data.0.user.id', $admin->id);
        $this->postJson(route('support.reply', $ticket), ['body' => '   '])->assertUnprocessable();
    }

    public function test_owner_can_start_read_and_reply_to_chat_without_redirects(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson(route('support.store'), ['subject' => 'Bantuan pesanan', 'category' => 'booking', 'body' => 'Tolong bantu cek pesanan saya.'])->assertCreated();
        $ticket = SupportTicket::findOrFail($response->json('ticket.id'));
        $this->getJson(route('support.show', $ticket))->assertOk()->assertJsonFragment(['body' => 'Tolong bantu cek pesanan saya.']);
        $this->postJson(route('support.reply', $ticket), ['body' => 'Terima kasih'])->assertCreated()->assertJsonPath('message.user.id', $user->id);
        $this->getJson(route('support.show', $ticket))->assertOk()->assertJsonCount(3, 'messages.data')->assertJsonPath('messages.data.0.body', 'Terima kasih');
    }

    public function test_other_users_cannot_read_or_reply_and_closed_chat_rejects_messages(): void
    {
        $ticket = SupportTicket::factory()->create();
        $this->actingAs(User::factory()->create())->getJson(route('support.show', $ticket))->assertNotFound();
        $this->postJson(route('support.reply', $ticket), ['body' => 'Unauthorized'])->assertNotFound();
        $ticket->update(['status' => 'closed']);
        $this->actingAs($ticket->user)->postJson(route('support.reply', $ticket), ['body' => 'Closed chat'])->assertUnprocessable();
        $this->assertDatabaseCount('support_messages', 0);
    }
}
