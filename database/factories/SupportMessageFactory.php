<?php
namespace Database\Factories;

use App\Models\SupportMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SupportMessage> */
class SupportMessageFactory extends Factory
{
    public function definition(): array
    {
        return ['support_ticket_id' => \App\Models\SupportTicket::factory(), 'user_id' => fn (array $a) => \App\Models\SupportTicket::findOrFail($a['support_ticket_id'])->user_id, 'body' => 'Mohon bantuan untuk perjalanan ini.'];
    }
}

