<?php

namespace app\controller\api;

use support\Request;
use app\model\Article;
use app\model\ArticleConfig;
use app\model\ArticleCategory;

class ArticleController
{
    public function info(Request $request)
    {
        $app_key = $request->input('appkey');
        $id = $request->input('id');
        if(empty($id)){
            return json(['code' => 400101, 'msg' => 'id is required']);
        }

        $article_config_model = new ArticleConfig();
        $article_config = $article_config_model->getArticleConfigByAppKey($app_key);
        if(empty($article_config)){
            return json(['code' => 400102, 'msg' => 'article config not found']);
        }

        if($article_config['switch'] == 0){
            return json(['code' => 400103, 'msg' => 'article interface is not enabled']);
        }

        $article_model = new Article();
        $article_info = $article_model->getArticleInfoById($id, $app_key);
        if(empty($article_info)){
            return json(['code' => config('const.request_not_found'), 'msg' => 'article not found']);
        }
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => $article_info]);
    }

    public function list(Request $request)
    {
        $app_key = $request->input('appkey');
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        if($page < 1){
            return json(['code' => 400101, 'msg' => 'page must be greater than 0']);
        }

        if($page_size < 1){
            return json(['code' => 400102, 'msg' => 'page_size must be greater than 0']);
        }

        if($page_size > 100){
            return json(['code' => 400103, 'msg' => 'page_size must be less than 100']);
        }
        
        $article_config_model = new ArticleConfig();
        $article_config = $article_config_model->getArticleConfigByAppKey($app_key);
        if(empty($article_config)){
            return json(['code' => 400104, 'msg' => 'article config not found']);
        }

        if($article_config['switch'] == 0){
            return json(['code' => 400105, 'msg' => 'article interface is not enabled']);
        }

        $start = ($page - 1) * $page_size;
        $article_model = new Article();
        $total = $article_model->getCount($app_key);
        $article_list = $article_model->getArticleList($app_key, $start, $page_size);
        foreach($article_list as &$value){
            $value['link'] = getArticleUrl($app_key, $value['id']);
        }
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => [
            'page' => $page,
            'page_size' => $page_size,
            'total' => $total,
            'list' => $article_list
        ]]);
    }

    public function categoryList(Request $request)
    {
        $app_key = $request->input('appkey');
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);
        
        if($page < 1){
            return json(['code' => 400101, 'msg' => 'page must be greater than 0']);
        }

        if($page_size < 1){
            return json(['code' => 400102, 'msg' => 'page_size must be greater than 0']);
        }   

        if($page_size > 100){
            return json(['code' => 400103, 'msg' => 'page_size must be less than 100']);
        }   

        $article_config_model = new ArticleConfig();
        $article_config = $article_config_model->getArticleConfigByAppKey($app_key);
        if(empty($article_config)){
            return json(['code' => 400104, 'msg' => 'article config not found']);
        }

        if($article_config['switch'] == 0){
            return json(['code' => 400105, 'msg' => 'article interface is not enabled']);
        }
        $start = ($page - 1) * $page_size;
        $article_category_model = new ArticleCategory();
        $total = $article_category_model->getCount($app_key);
        $article_category_list = $article_category_model->getArticleCategoryList($app_key, $start, $page_size);
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => [
            'page' => $page,
            'page_size' => $page_size,
            'total' => $total,
            'list' => $article_category_list
        ]]);
        
        
        
    }
}