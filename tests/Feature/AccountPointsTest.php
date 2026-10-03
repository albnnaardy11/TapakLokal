<?php

namespace Tests\Feature;

use App\Models\RewardEntry;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AccountPointsTest extends TestCase
{
    public function test_points_summary_uses_all_entries_and_excludes_other_users(): void
    {
        $user = User::factory()->create(['created_at' => '2025-01-01 00:00:00']);
        RewardEntry::factory()->count(11)->create(['user_id' => $user->id, 'points' => 50]);
        RewardEntry::factory()->create(['user_id' => $user->id, 'points' => -30]);
        RewardEntry::factory()->create(['points' => 9999]);

        $this->actingAs($user)->get('/account/points')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Account')->where('pointBalance', 520)->where('pointSummary.earned', 550)
            ->where('pointSummary.deducted', 30)->where('pointSummary.memberSince', '2025')
            ->where('records.total', 12)->has('records.data', 10));
        $this->get('/account/points?activity=deducted')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('pointFilter', 'deducted')->where('pointBalance', 520)->where('pointSummary.earned', 550)
            ->where('records.total', 1)->where('records.data.0.points', -30));
        $this->get('/account/points?activity=earned')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('records.total', 11)->where('records.data.0.points', 50));
    }

    public function test_empty_points_account_has_zero_totals(): void
    {
        $this->actingAs(User::factory()->create())->get('/account/points')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('pointBalance', 0)->where('pointSummary.earned', 0)->where('pointSummary.deducted', 0)->has('records.data', 0));
    }
}
