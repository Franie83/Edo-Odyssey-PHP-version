<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\News;
use App\Models\NewsComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::where('is_published', true);

        if ($s = $request->q) {
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('content', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%");
            });
        }
        if ($cat = $request->category) {
            $query->where('category', $cat);
        }

        return view('news.index', [
            'articles'   => $query->orderByDesc('created_at')->paginate(12)->withQueryString(),
            'categories' => News::where('is_published', true)->distinct('category')->pluck('category')->filter()->values(),
            'featured'   => News::where('is_published', true)->where('is_featured', true)->latest()->first(),
        ]);
    }

    public function show(int $id)
    {
        $article = News::findOrFail($id);
        $article->increment('views');
        return view('news.show', [
            'article'  => $article,
            'comments' => $article->comments()->with('user')->orderByDesc('created_at')->get(),
            'related'  => News::where('is_published', true)
                               ->where('id', '!=', $id)
                               ->where('category', $article->category)
                               ->latest()->limit(3)->get(),
        ]);
    }

    public function comment(Request $request, int $id)
    {
        $request->validate(['content' => 'required|string|max:1000']);
        $article = News::findOrFail($id);
        NewsComment::create([
            'news_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);
        Helpers::awardPoints(Auth::user(), 5, 'Commenting on news');
        return back()->with('success', 'Comment posted!');
    }
}
