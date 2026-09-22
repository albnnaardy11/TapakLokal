<?php
namespace Database\Factories;

use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SupportTicket> */
class SupportTicketFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => \App\Models\User::factory(), 'subject' => 'Bantuan perjalanan', 'category' => 'booking', 'status' => 'open', 'priority' => 'normal'];
    }
}

