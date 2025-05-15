<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\Libs\Helpers;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Article;
use App\SaaSAdmin\AppKey;
use App\Models\ArticleCategory;
use Encore\Admin\Layout\Content;
use App\Models\ArticleContentShow;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Encore\Admin\Controllers\AdminController;

class ArticleController extends AdminController
{
    use AppKey;
    public function index(Content $content)
    {
        return $content
            ->title('帮助文档列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $app_key = $this->getAppKey();
        $grid = new Grid(new Article());
        $grid->model()->where('tenant_id', SaaSAdmin::user()->id)->where('app_key', $this->getAppKey());
        $grid->model()->orderBy('order', 'asc')->orderBy('created_at', 'desc');
        $grid->tools(function ($tools) {
            $tools->append('<a href="/docs/1.x/apis/article_list" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        $grid->column('id', 'ID');
        $grid->column('title', '标题');
        $grid->order('排序')->orderable();
        $grid->column('url', '链接')->display(function($value) {
            /** @var \App\Models\Article $this */
            return '<a href="'.route('article.show', [$this->app_key, $this->id]).'" target="_blank">文档链接</a>';
        })->copyable();
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '更新时间');

        $grid->filter(function ($filter) {
            $filter->equal('category_id', '分类')->select(ArticleCategory::where('app_key', $this->getAppKey())->pluck('name', 'id'))
            ->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity');
        });
        
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();

        return $grid;
    }

    public function edit($id, Content $content)
    {
        $id = request()->route('list');
        return parent::edit($id, $content)->title('帮助文档')->description('编辑');
    }

    public function update($id)
    {
        $id = request()->route('list');
        return parent::update($id);
    }

    public function form()
    {
        $app_key = $this->getAppKey();
        $form = new Form(new Article());

        $form->select('category_id', '分类')
            ->options(ArticleCategory::where('app_key', $app_key)->pluck('name', 'id'))
            ->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity')
            ->required()
            ->rules('required');
        $form->text('title', '标题')->rules(['required', 'string', 'max:32'])->help('标题最多32个字符以内');
        
        // Editor.md 会提交 content 字段和隐藏的 content-markdown-doc 字段
        $form->myEditorMd('content', '内容')->rules(['required', 'string', 'max:10300'],['max' => '内容最多10000个字符以内'])->help('内容最多10000个字符以内');
        
        $form->saving(function (Form $form) use ($app_key) {
            $html_content = request()->input('content_html');
            if(empty($html_content)) {
                admin_error('内容不能为空');
                return back();
            }
            if ($form->isCreating()) {
                $form->model()->id = Helpers::generateArticleId();
                $form->model()->tenant_id = SaaSAdmin::user()->id;
                $form->model()->app_key = $app_key;
            }
        });
        
        $form->saved(function (Form $form) {
            $article = $form->model();
            $htmlContent = request()->input('content_html'); // 获取 HTML 内容
            $article_content_show_model = app(ArticleContentShow::class);
 // 更新已有记录
            $article_content_show_model->updateOrCreate(
            [
                'article_id' => $article->id,
            ],
            [
                'tenant_id' => $article->tenant_id,
                'app_key' => $article->app_key,
                'content' => $htmlContent, // 更新 HTML 内容
            ]);
        });
        
        $form->tools(function(Form\Tools $tools){
            $tools->disableView();
            $tools->disableDelete();
        });

        $form->footer(function(Form\Footer $footer){
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });
        return $form;
    }

    public function title()
    {
        return '帮助文档';
    }

    public function detail()
    {
        $id = request()->route('list');
        $show = new Show(Article::find($id));
        $show->field('title', '标题');
        $show->field('content', '内容')->editormd();

        $show->field('updated_at', '更新时间');
        $show->field('created_at', '注册时间');

        $show->panel()
        ->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });
        return $show;
    }

}