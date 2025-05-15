<?php

namespace App\SaaSAdmin\Forms;

use Encore\Admin\Widgets\Form;
use App\SaaSAdmin\AppKey;
use App\SaaSAdmin\Facades\SaaSAdmin;
use App\Models\ArticleConfig;

class ArticleBaseConfig extends Form
{
    use AppKey;

    public $title = '基础配置';

    public function form()
    {
        $this->radioButton('switch', '是否启用接口')->options([
            1 => '启用',
            0 => '关闭',
        ])->required()->when(1, function (Form $form) {
            $this->html('<span class="text-danger"><i class="fa fa-warning"></i> 启用后才能使用文档相关接口以及使用前端渲染连接</span>');
            $this->select('list_theme', '列表主题')
            ->options(ArticleConfig::$listTheme)
            ->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity')
            ->default(ArticleConfig::DEFAULT_LIST_THEME)
            ->required()
            ->help('列表主题，用于列表页面的主题颜色');
            
            $this->select('content_theme', '内容主题')
            ->options(ArticleConfig::$contentTheme)
            ->default(ArticleConfig::DEFAULT_CONTENT_THEME)
            ->required()
            ->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity')
            ->help('内容主题，用于内容页面的主题颜色');
            $this->action(admin_url('app/manager/'.$this->getAppKey().'/article/config/base'))->method('post');
            $this->disableReset();
        });
        
    }

    public function data()
    {
        $tenant_id = SaaSAdmin::user()->id;
        $config = app(ArticleConfig::class)->getConfig($this->getAppKey(), $tenant_id);
        return[
            'switch' => $config['switch'] ?? 0,
            'list_theme' => $config['list_theme'] ?? ArticleConfig::DEFAULT_LIST_THEME,
            'content_theme' => $config['content_theme'] ?? ArticleConfig::DEFAULT_CONTENT_THEME,
        ];
    }
}