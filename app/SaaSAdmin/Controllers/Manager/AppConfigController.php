<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\AppConfig;
use App\SaaSAdmin\AppKey;
use Illuminate\Validation\Rule;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Support\Facades\Cache;


class AppConfigController extends AdminController
{
    use AppKey;
    
    public function index(Content $content)
    {
        return $content
            ->title('APP配置项列表')
            ->body($this->grid());
    }

    public function grid()
    {
        $app_key = $this->getAppKey();
        $grid = new Grid(new AppConfig());
        $grid->model()->where('tenant_id', SaaSAdmin::user()->id)->where('app_key', $app_key);
        $grid->model()->orderBy('created_at', 'desc');
        $grid->tools(function ($tools) use ($app_key) {
            $tools->append('<a href="/docs/1.x/apis/app_config" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        $grid->column('id', 'ID');
        $grid->column('title', '配置名称');
        $grid->column('name', '配置标识符')->help('配置标识符，接口调用时使用');
        $grid->column('params', '配置项')->display(function ($params) {
            return '共 '.count($params).' 项';
        });
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '更新时间');

        $grid->actions(function ($actions) use ($app_key) {
            // 添加复制按钮，直接链接到创建页面并带上源ID参数
            $actions->prepend('<a href="' . admin_url('/app/manager/'.$app_key.'/config/create?copy_from=' . $actions->row->id) . '" title="复制配置"><i class="fa fa-copy"></i></a>');
        });
        
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        $grid->disableFilter();

        return $grid;
    }

    public function create(Content $content)
    {
        return $content
            ->title('创建配置')
            ->body($this->form());
    }

    public function edit($id, Content $content)
    {
        $id = request()->route('config');
        return parent::edit($id, $content)->title('编辑配置')->description('编辑');
    }

    public function update($id)
    {
        $id = request()->route('config');
        $config = AppConfig::find($id);
        $this->clearAPICache($config->name, $config->app_key);
        return parent::update($id);
    }

    public function destroy($id)
    {
        $id = request()->route('config');
        $config = AppConfig::find($id);
        $this->clearAPICache($config->name, $config->app_key);
        return parent::destroy($id);
    }

    public function form()
    {
        $form = new Form(new AppConfig());
        $app_key = $this->getAppKey();
        $tenant_id = SaaSAdmin::user()->id;

        // 检查是否是复制操作
        $copyFromId = request()->get('copy_from');
        $sourceConfig = null;
        
        if ($copyFromId) {
            $sourceConfig = AppConfig::where('id', $copyFromId)
                ->where('tenant_id', $tenant_id)
                ->where('app_key', $app_key)
                ->first();
        }

        $form->text('title', '配置名称')
            ->rules('required|string|max:64')
            ->help('配置名称，用于显示');
        $form->text('name', '配置标识符')
            ->required()
            ->rules(function($form) use ($app_key, $tenant_id) {
                $id = $form->model()->id;
                return [
                    'required',
                    'string',
                    'max:32',
                    'regex:/^[a-z][a-z0-9_]*$/',
                    Rule::unique('app_configs')->where(function ($query) use ($app_key, $tenant_id) {
                        return $query->where('app_key', $app_key)->where('tenant_id', $tenant_id);
                    })->ignore($id)
                ];
            })
            ->help('配置标识符，只允许小写字母、数字、下划线，数字不能作为开头；接口调用时使用，要求唯一');
        $form->zhyKeyValue('params', '配置项')
            ->keyRules('distinct|required|string|max:50|regex:/^[a-z][a-z0-9_]*$/')
            ->rules(['required', 'string', 'max:1024'],  ['distinct' => '键不能重复', 'regex' => '键值只允许小写字母、数字、下划线，数字不能作为开头'])
            ->value($sourceConfig ? $sourceConfig->params : []);

        $form->html('<span class="text-danger"><i class="fa fa-warning"></i> 配置项最多30个，键最多50个字符，值最多1024个字符。</span>');

        $form->saving(function (Form $form) {
            if($form->isCreating()) {
                $form->model()->app_key = $this->getAppKey();
                $form->model()->tenant_id = SaaSAdmin::user()->id;
            }

            if(count($form->params['keys']) > 30) {
                $error = new MessageBag([
                    'title'   => '配置项最多30个',
                    'message' => '当前配置项：'.count($form->params['keys']).'个',
                ]);
            
                return back()->with(compact('error'));
            }
        });

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });

        return $form;
    }

    public function detail($id)
    {
        $id = request()->route('config');
        $show = new Show(AppConfig::find($id));
        $show->field('title', '配置名称');
        $show->field('name', '配置标识符');
        $show->field('params', '配置项')->as(function ($params) {
            $json = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return "<pre>{$json}</pre>";
        })->unescape();
        $show->field('updated_at', '更新时间');
        $show->field('created_at', '创建时间');

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });
        return $show;
    }

    protected function clearAPICache($config_name, $app_key)
    {
        $cache_key = 'app_config||'.$config_name.'|'.$app_key;
        Cache::store('api_cache')->forget($cache_key);
    }
}