<?php
namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\YandexParser;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::where('user_id', auth()->id())
            ->when($request->sort === 'oldest', function ($q) {
                $q->orderBy('published_at', 'asc');
            }, function ($q) {
                $q->orderBy('published_at', 'desc');
            })
            ->paginate(5)
            ->withQueryString();

        $stats = [
            'total_reviews' => Review::where('user_id', auth()->id())->count(),
            'average_rating' => round(Review::where('user_id', auth()->id())->avg('rating'), 1) ?? 0,
        ];

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'filters' => $request->only(['sort']),
        ]);
    }

    public function settings()
    {
        return Inertia::render('Settings/Integration');
    }

    public function store(Request $request, YandexParser $parser)
    {
        $request->validate(['url' => 'required|url']);

        set_time_limit(180);

        $data = $parser->parse($request->url);

        if (!$data || empty($data['reviews'])) {
            return back()->withErrors(['url' => 'Отзывы не найдены или Яндекс заблокировал запрос (Captcha).']);
        }

        \DB::transaction(function () use ($data, $request) {

            Review::where('user_id', auth()->id())->delete();

            foreach ($data['reviews'] as $item) {
                Review::create([
                    'user_id' => auth()->id(),
                    'source_url' => $request->url,
                    'author_name' => $item['author'],
                    'text' => $item['text'],
                    'rating' => $item['rating'],
                    'published_at' => $item['date'],
                    'avatar_url' => $item['avatar'],
                ]);
            }
        });

        return redirect()->route('reviews.index');
    }
}
