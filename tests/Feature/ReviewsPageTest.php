<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReviewsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/reviews')->assertRedirect('/login');
    }

    public function test_it_shows_only_the_users_own_reviews_with_statistics(): void
    {
        $user = User::factory()->create();
        Review::factory()->for($user)->create(['rating' => 5]);
        Review::factory()->for($user)->create(['rating' => 4]);
        Review::factory()->for($user)->create(['rating' => 4]);
        Review::factory()->count(3)->create(['rating' => 1]); // other users

        $this->actingAs($user)
            ->get('/reviews')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Reviews/Index')
                ->has('reviews.data', 3)
                ->where('stats.total_reviews', 3)
                ->where('stats.average_rating', 4.3)
            );
    }

    public function test_average_rating_is_zero_without_reviews(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/reviews')
            ->assertInertia(fn (Assert $page) => $page
                ->where('stats.total_reviews', 0)
                ->where('stats.average_rating', 0)
            );
    }

    public function test_reviews_are_paginated_and_sorted_newest_first_by_default(): void
    {
        $user = User::factory()->create();
        foreach (range(1, 7) as $daysAgo) {
            Review::factory()->for($user)->create([
                'author_name' => "Author {$daysAgo}",
                'published_at' => now()->subDays($daysAgo),
            ]);
        }

        $this->actingAs($user)
            ->get('/reviews')
            ->assertInertia(fn (Assert $page) => $page
                ->has('reviews.data', 5)
                ->where('reviews.total', 7)
                ->where('reviews.data.0.author_name', 'Author 1')
            );
    }

    public function test_reviews_can_be_sorted_oldest_first(): void
    {
        $user = User::factory()->create();
        Review::factory()->for($user)->create(['author_name' => 'New', 'published_at' => now()->subDay()]);
        Review::factory()->for($user)->create(['author_name' => 'Old', 'published_at' => now()->subMonth()]);

        $this->actingAs($user)
            ->get('/reviews?sort=oldest')
            ->assertInertia(fn (Assert $page) => $page
                ->where('reviews.data.0.author_name', 'Old')
                ->where('filters.sort', 'oldest')
            );
    }
}
