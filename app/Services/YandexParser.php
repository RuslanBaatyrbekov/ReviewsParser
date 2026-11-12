<?php

namespace App\Services;

use App\Contracts\ReviewSource;
use Carbon\Carbon;

class YandexParser implements ReviewSource
{
    public function __construct(
        private readonly int $delaySeconds = 2,
    ) {}

    /**
     * Mock implementation of the review source.
     *
     * Yandex Maps protects its pages with SmartCaptcha, so for this assignment the source
     * generates realistic demo data instead of scraping. Swapping in a real API client only
     * requires another ReviewSource implementation bound in AppServiceProvider.
     */
    public function parse(string $url): array
    {
        sleep($this->delaySeconds);

        $reviewsCount = rand(15, 30);
        $reviews = [];

        $names = ['Иван Иванов', 'Анна Смирнова', 'Алексей Петров', 'Екатерина Соколова', 'Михаил Волков', 'Илья Софронов', 'Наталья'];
        $texts = [
            'Отличное место! Все очень понравилось, обязательно вернемся.',
            'Вкусно, но обслуживание могло быть и быстрее. В целом 4 звезды.',
            'Так, с чего начать... Разнообразная продукция, множество закусок. Кухня вкусная.',
            'Ужасно. Ждали заказ 40 минут. Больше ни ногой.',
            'Прекрасная атмосфера и вежливый персонал. Рекомендую всем друзьям.',
            'Обычное кафе, ничего выдающегося. Цены немного завышены.',
            'Очень уютно. Идеально для бизнес-ланча или встречи с друзьями.',
        ];

        for ($i = 0; $i < $reviewsCount; $i++) {
            $reviews[] = [
                'author' => $names[array_rand($names)],
                'text' => $texts[array_rand($texts)],
                'rating' => rand(1, 10) > 2 ? rand(4, 5) : rand(1, 3),
                'date' => Carbon::now()->subDays(rand(1, 90)),
                'avatar' => rand(0, 1) ? 'https://i.pravatar.cc/150?u='.rand(1, 1000) : null,
            ];
        }

        usort($reviews, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return [
            'reviews' => $reviews,
            'count' => count($reviews),
        ];
    }
}
