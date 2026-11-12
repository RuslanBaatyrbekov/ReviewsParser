<?php

namespace Tests\Unit;

use App\Services\YandexParser;
use PHPUnit\Framework\TestCase;

class YandexParserTest extends TestCase
{
    public function test_it_generates_a_consistent_newest_first_dataset(): void
    {
        $result = (new YandexParser(delaySeconds: 0))->parse('https://yandex.ru/maps/org/test/1/');

        $this->assertSame(count($result['reviews']), $result['count']);
        $this->assertGreaterThanOrEqual(15, $result['count']);
        $this->assertLessThanOrEqual(30, $result['count']);

        $previous = null;
        foreach ($result['reviews'] as $review) {
            $this->assertArrayHasKey('author', $review);
            $this->assertArrayHasKey('text', $review);
            $this->assertArrayHasKey('avatar', $review);
            $this->assertContains($review['rating'], [1, 2, 3, 4, 5]);

            if ($previous) {
                $this->assertGreaterThanOrEqual($review['date'], $previous);
            }
            $previous = $review['date'];
        }
    }
}
