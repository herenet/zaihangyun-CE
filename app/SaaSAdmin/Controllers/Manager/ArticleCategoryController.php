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
use App\SaaSAdmin\Facades\SaaSAdmin;
use Encore\Admin\Controllers\AdminController;

class ArticleCategoryController extends AdminController
{
    use AppKey;

    public function index(Content $content)
    {
        return $content
            ->title('文档分类')
            ->body($this->grid());
    }

    protected function grid()
    {
        $app_key = $this->getAppKey();
        $tenant_id = SaaSAdmin::user()->id;
        $grid = new Grid(new ArticleCategory());
        $grid->model()->where('app_key', $app_key)->where('tenant_id', $tenant_id)->orderBy('created_at', 'desc');
        $grid->tools(function ($tools) {
            $tools->append('<a href="/docs/1.x/apis/article_cate_list" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        $grid->column('id', 'ID');
        $grid->column('name', '名称');
        $grid->column('desc', '描述');
        $grid->column('url', '链接')->display(function($value) use ($app_key) {
            /** @var \App\Models\ArticleCategory $this */
            return '<a href="'.route('article.category.show', [$app_key, $this->id]).'" target="_blank">列表页链接</a>';
        })->copyable();
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '更新时间');

        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->disableFilter();
        
        return $grid;
    }

    public function edit($id, Content $content)
    {
        $id = request()->route('category');
        return parent::edit($id, $content)->title('文档分类')->description('编辑');
    }

    public function update($id)
    {
        $id = request()->route('category');
        return parent::update($id);
    }

    public function title()
    {
        return '文档分类';
    }

    public function form()
    {
        $app_key = $this->getAppKey();
        $form = new Form(new ArticleCategory());
        $form->text('name', '名称')
            ->required()
            ->rules('required|string|max:32');
        $form->text('desc', '描述')
            ->required()
            ->rules('required|string|max:128');

        $form->saving(function (Form $form) use ($app_key) {
            if ($form->isCreating()) {
                $form->model()->id = Helpers::generateArticleId();
                $form->model()->tenant_id = SaaSAdmin::user()->id;
                $form->model()->app_key = $app_key;
            };
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

    public function destroy($id)
    {
        $id = request()->route('category');
        $article_list = Article::where('category_id', $id)->get();
        if ($article_list->count() > 0) {
            return response()->json(['status' => false, 'message' => '该分类下有文档，不能删除']);
        }
        return parent::destroy($id);
    }

    public function detail()
    {
        $id = request()->route('category');
        $show = new Show(ArticleCategory::find($id));
        $show->field('name', '名称');
        $show->field('desc', '描述');
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '更新时间');

        $show->panel()
        ->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });
        return $show;
    }
}