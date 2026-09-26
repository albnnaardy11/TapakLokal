<?php

namespace Database\Factories;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SupportMessage> */
class SupportMessageFactory extends Factory
{
    public function definition(): array
    {
        return ['support_ticket_id' => SupportTicket::factory(), 'user_id' => fn (array $a) => SupportTicket::findOrFail($a['support_ticket_id'])->user_id, 'body' => 'Mohon bantuan untuk perjalanan ini.'];
    }
}
