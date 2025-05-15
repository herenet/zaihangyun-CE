<?php

namespace App\SaaSAdmin\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleContentShow;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ArticleConfig;
class ShowController extends Controller
{
    public function show($app_key, $id, Request $request)
    {
        $article_config = app(ArticleConfig::class)->getConfigByAppKey($app_key);
        if(empty($article_config) || $article_config['switch'] == 0){
            abort(403, '文章模块未开启');
        }
        $article = ArticleContentShow::where('app_key', $app_key)->where('article_id', $id)->first();
        if (!$article) {
            abort(404, '文章不存在');
        }

        $theme = $request->query('theme') ?? $article_config['content_theme'];
        if (!in_array($theme, ['light', 'dark'])) {
            // 从系统配置获取默认主题
            $theme = ArticleConfig::DEFAULT_CONTENT_THEME;
        }
        
        return view('article', [
            'article' => $article,
            'theme' => $theme
        ]);
    }

    /**
     * 显示指定分类下的文章列表
     *
     * @param string $app_key 应用标识
     * @param int $id 分类ID
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function category($app_key, $id, Request $request)
    {
        $article_config = app(ArticleConfig::class)->getConfigByAppKey($app_key);
        if(empty($article_config) || $article_config['switch'] == 0){
            abort(403, '文章模块未开启');
        }
        $category = ArticleCategory::where('app_key', $app_key)->where('id', $id)->first();
        if (!$category) {
            abort(404, '分类不存在');
        }
        
        $articles = Article::where('category_id', $id)
            ->select('id', 'title')
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // 获取主题设置 - 优先使用 URL 参数，其次使用系统配置
        $theme = $request->query('theme') ?? $article_config['list_theme'];
        if (!in_array($theme, ['light', 'dark'])) {
            // 从系统配置获取默认主题
            $theme = ArticleConfig::DEFAULT_LIST_THEME;
        }
        
        // 处理AJAX请求（用于无限滚动加载）
        if ($request->ajax()) {
            return response()->json([
                'articles' => $articles->items(),
                'has_more_pages' => $articles->hasMorePages()
            ]);
        }
        
        // 常规页面请求
        return view('article_category', [
            'category' => $category, 
            'articles' => $articles,
            'app_key' => $app_key,
            'theme' => $theme // 传递主题到视图
        ]);
    }
}
