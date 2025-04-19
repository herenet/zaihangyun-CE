<?php

namespace App\SaaSAdmin\Controllers;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Models\ArticleContentShow;
class ShowController extends Controller
{
    public function show($app_key, $id)
    {
        $article = ArticleContentShow::where('app_key', $app_key)->where('article_id', $id)->first();
        if (!$article) {
            abort(404, '文章不存在');
        }
        return view('article', ['article' => $article]);
    }
}
