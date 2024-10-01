<?php

namespace App\Http\Controllers\API\Compro;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');
        $articles = Article::query()->orderByDesc('id');
        if ($category) {
            $articles->where('category', $category);
        }
        $articles = $articles->with('media')->paginate(9);
        $articles->map(function ($article) {
            $article->thumbnail = $article->getFirstMediaUrl('article-thumbnail');
            unset($article->media);
            return $article;
        });
        return ResponseFormatter::success($articles, 'Data artikel berhasil diambil');
    }

    public function isUnggulanArticle()
    {
        $articles = Article::query()->where('is_unggulan', 1)->orderByDesc('id')->with('media')->limit(3)->get();
        $articles->map(function ($article) {
            $article->thumbnail = $article->getFirstMediaUrl('article-thumbnail');
            unset($article->media);
            return $article;
        });
        return ResponseFormatter::success($articles, 'Data artikel unggulan berhasil diambil');
    }

    public function show($slug)
    {
        $article = Article::query()->where('slug', $slug)->with('media')->first();
        if (!$article) {
            return ResponseFormatter::error('Data artikel tidak ditemukan', 404);
        }
        $article->thumbnail = $article->getFirstMediaUrl('article-thumbnail');
        unset($article->media);
        return ResponseFormatter::success($article, 'Data artikel berhasil diambil');
    }
}
