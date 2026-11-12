<?php

namespace App\Contracts;

interface ReviewSource
{
    /**
     * Fetch reviews for the organisation behind the given URL.
     *
     * @return array{
     *     reviews: list<array{author: string, text: ?string, rating: int, date: \DateTimeInterface, avatar: ?string}>,
     *     count: int
     * }
     */
    public function parse(string $url): array;
}
