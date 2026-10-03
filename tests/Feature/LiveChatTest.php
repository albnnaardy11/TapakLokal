<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\User;
use Tests\TestCase;

class LiveChatTest extends TestCase
{
    public function test_owner_can_start_read_and_reply_to_chat_without_redirects(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson(route('support.store'), ['subject' => 'Bantuan pesanan', 'category' => 'booking', 'body' => 'Tolong bantu cek pesanan saya.'])->assertCreated();
        $ticket = SupportTicket::findOrFail($response->json('ticket.id'));
        $this->getJson(route('support.show', $ticket))->assertOk()->assertJsonPath('messages.data.0.body', 'Tolong bantu cek pesanan saya.');
        $this->postJson(route('support.reply', $ticket), ['body' => 'Terima kasih'])->assertCreated()->assertJsonPath('message.user.id', $user->id);
        $this->getJson(route('support.show', $ticket))->assertOk()->assertJsonCount(2, 'messages.data')->assertJsonPath('messages.data.0.body', 'Terima kasih');
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
