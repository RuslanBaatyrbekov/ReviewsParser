<?php

namespace Tests\Feature;

use App\Contracts\ReviewSource;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportReviewsTest extends TestCase
{
    use RefreshDatabase;

    private const URL = 'https://yandex.ru/maps/org/coffee_house/1234567890/';

    private function fakeSource(array $reviews): void
    {
        $this->app->instance(ReviewSource::class, new class($reviews) implements ReviewSource
        {
            public function __construct(private array $reviews) {}

            public function parse(string $url): array
            {
                return ['reviews' => $this->reviews, 'count' => count($this->reviews)];
            }
        });
    }

    private function review(string $author, int $rating = 5): array
    {
        return [
            'author' => $author,
            'text' => 'Great coffee',
            'rating' => $rating,
            'date' => now()->subDays(3),
            'avatar' => null,
        ];
    }

    public function test_it_replaces_the_users_reviews_with_freshly_imported_ones(): void
    {
        $user = User::factory()->create();
        Review::factory()->for($user)->count(4)->create();
        $otherUsersReview = Review::factory()->create();

        $this->fakeSource([$this->review('Anna', 5), $this->review('Ivan', 3)]);

        $this->actingAs($user)
            ->post('/settings', ['url' => self::URL])
            ->assertRedirect(route('reviews.index'));

        $this->assertSame(['Anna', 'Ivan'], $user->reviews()->orderBy('author_name')->pluck('author_name')->all());
        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'source_url' => self::URL, 'rating' => 3]);
        $this->assertModelExists($otherUsersReview);
    }

    public function test_existing_reviews_are_kept_when_nothing_was_found(): void
    {
        $user = User::factory()->create();
        Review::factory()->for($user)->count(2)->create();
        $this->fakeSource([]);

        $this->actingAs($user)
            ->from('/settings')
            ->post('/settings', ['url' => self::URL])
            ->assertRedirect('/settings')
            ->assertSessionHasErrors('url');

        $this->assertSame(2, $user->reviews()->count());
    }

    public function test_only_yandex_maps_links_are_accepted(): void
    {
        $this->fakeSource([$this->review('Anna')]);

        $this->actingAs(User::factory()->create())
            ->post('/settings', ['url' => 'https://example.com/maps/org/1'])
            ->assertSessionHasErrors('url');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_guests_cannot_import_reviews(): void
    {
        $this->post('/settings', ['url' => self::URL])->assertRedirect('/login');
    }
}
