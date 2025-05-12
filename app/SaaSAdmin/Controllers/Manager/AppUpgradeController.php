<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\SaaSAdmin\AppKey;
use App\Models\AppUpgrade;
use Encore\Admin\Widgets\Box;
use Illuminate\Validation\Rule;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Cache;
use Encore\Admin\Controllers\AdminController;


class AppUpgradeController extends AdminController
{
    use AppKey;
    
    public function index(Content $content)
    {
        return $content
            ->title('APP升级列表')
            ->body($this->grid());
    }

    public function grid()
    {
        $app_key = $this->getAppKey();
        $grid = new Grid(new AppUpgrade());
        $grid->model()->where('tenant_id', SaaSAdmin::user()->id)->where('app_key', $app_key);
        $grid->model()->orderBy('created_at', 'desc');
        $grid->tools(function ($tools) use ($app_key) {
            $tools->append('<a href="/docs/1.x/apis/app_upgrade" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        $grid->fixColumns(2, -2);
        $grid->column('id', 'ID');
        $grid->column('channel_name', '渠道名称');
        $grid->column('platform_type', '平台')->display(function ($platform) {
            return AppUpgrade::$platformMap[$platform];
        })->icon([
            1 => 'fa-apple',
            2 => 'fa-android',
            99 => 'fa-question',
        ]);
        $grid->column('version_string', '版本号')->label();
        $grid->column('version_num', '版本值');
        $grid->column('min_version_num', '最小版本值')->help('最小版本值，低于此版本将强制升级');
        $grid->column('force_upgrade', '强制升级')->bool([
            0 => false,
            1 => true,
        ]);
        $grid->column('status', '升级开关')->switch([
            'on' => ['value' => 1, 'text' => '开启', 'color' => 'success'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'primary'],
        ])->help('是否开启升级');
        $grid->column('gray_percent', '灰度百分比')->display(function ($gray_percent) {
            return $gray_percent.'%';
        })->help('灰度百分比，0-100');
        $grid->column('upgrade_from', '升级方式')->display(function ($upgrade_from) {
            return AppUpgrade::$upgradeFromMap[$upgrade_from];
        });
        $grid->column('package_download_url', '安装包下载地址')->downloadable()->help('安装包下载地址，用于下载安装包');
        $grid->column('package_md5', '安装包MD5')->help('安装包MD5，用于验证安装包完整性');
        $grid->column('package_size', '安装包大小')->filesize();
        $grid->column('upgrade_note', '升级说明')
            ->display(function ($value) {
                // 手动限制显示长度
                return \Illuminate\Support\Str::limit($value, 30);
            })
            ->modal('升级说明', function ($model) {
                return nl2br($model->upgrade_note);
            })
            ->help('升级说明，用于提示用户升级');
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '更新时间');

        $grid->actions(function ($actions) use ($app_key) {
            // 添加复制按钮，直接链接到创建页面并带上源ID参数
            $actions->prepend('<a href="' . admin_url('/app/manager/'.$app_key.'/upgrade/create?copy_from=' . $actions->row->id) . '" title="复制配置"><i class="fa fa-copy"></i></a>');
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
        $id = request()->route('upgrade');
        return parent::edit($id, $content)->title('编辑配置')->description('编辑');
    }

    public function update($id)
    {
        $id = request()->route('upgrade');
        $config = AppUpgrade::find($id);
        $this->clearAPICache($config->name, $config->app_key);
        return parent::update($id);
    }

    public function destroy($id)
    {
        $id = request()->route('upgrade');
        $config = AppUpgrade::find($id);
        $this->clearAPICache($config->name, $config->app_key);
        return parent::destroy($id);
    }

    public function form()
    {
        $form = new Form(new AppUpgrade());
        $app_key = $this->getAppKey();
        $tenant_id = SaaSAdmin::user()->id;

        // 检查是否是复制操作
        $copyFromId = request()->get('copy_from');
        $sourceConfig = null;
        
        if ($copyFromId) {
            $sourceConfig = AppUpgrade::where('id', $copyFromId)
                ->where('tenant_id', $tenant_id)
                ->where('app_key', $app_key)
                ->first();
        }

        $form->text('channel_name', '渠道名称')
            ->rules(function($form) use ($app_key, $tenant_id) {
                $id = $form->model()->id;
                return [
                    'required',
                    'string',
                    'max:32',
                    'regex:/^[a-z][a-z0-9_]*$/',
                    Rule::unique('app_upgrades')->where(function ($query) use ($app_key, $tenant_id) {
                        return $query->where('app_key', $app_key)->where('tenant_id', $tenant_id);
                    })->ignore($id)
                ];
            })
            ->help('渠道名称，要求唯一，用于显示，最多32个字符，只允许小写字母、数字、下划线，数字不能作为开头');
        $form->select('platform_type', '平台')
            ->options(AppUpgrade::$platformMap)
            ->required()
            ->rules('in:'.implode(',', array_keys(AppUpgrade::$platformMap)))
            ->help('平台，用于选择平台')
            ->value($sourceConfig ? $sourceConfig->platform_type : 1);
        $form->text('version_str', '版本号')
            ->required()
            ->rules('required|string|max:32')
            ->help('版本号，用于显示，最多32个字符')
            ->value($sourceConfig ? $sourceConfig->version_string : '1.0.0');
        $form->number('version_num', '版本值')
            ->default(1)
            ->required()
            ->rules('required|integer|min:1')
            ->help('版本值，用于比较版本')
            ->value($sourceConfig ? $sourceConfig->version_num : 1);
        $form->select('upgrade_from', '升级方式')
            ->options(AppUpgrade::$upgradeFromMap)
            ->required()
            ->rules('in:'.implode(',', array_keys(AppUpgrade::$upgradeFromMap)))
            ->help('升级方式，用于选择升级方式')
            ->value($sourceConfig ? $sourceConfig->upgrade_from : 1);
        $form->radio('gray_upgrade', '灰度升级')
            ->options([
                1 => '开启',
                0 => '关闭',
            ])->required()->when(1, function ($form) {
                $form->number('gray_percent', '灰度百分比')
                    ->rules('integer|min:1|max:100')
                    ->default(100)
                    ->required()
                    ->help('灰度百分比，1-100');
            })->help('灰度升级，用于灰度升级');
        $form->number('min_version_num', '最小版本值')
            ->rules('integer|min:1')
            ->help('最小版本值，低于此版本将强制升级')
            ->value($sourceConfig ? $sourceConfig->min_version_num : 1);
        $form->switch('force_upgrade', '强制升级')
            ->help('强制升级，用于强制升级')
            ->value($sourceConfig ? $sourceConfig->force_upgrade : 0);
        $form->text('package_download_url', '安装包下载地址')
            ->help('安装包下载地址，用于下载安装包')
            ->value($sourceConfig ? $sourceConfig->package_download_url : '');
        $form->text('package_md5', '安装包MD5')
            ->help('安装包MD5，用于验证安装包完整性')
            ->value($sourceConfig ? $sourceConfig->package_md5 : '');
        $form->number('package_size', '安装包大小')
            ->help('安装包大小，用于显示，单位：字节')
            ->value($sourceConfig ? $sourceConfig->package_size : 0);
        $form->textarea('upgrade_note', '升级说明')
            ->help('升级说明，用于提示用户升级, 不支持html标签，最多500个字符')
            ->value($sourceConfig ? $sourceConfig->upgrade_note : '');

        $form->saving(function (Form $form) {
            if($form->isCreating()) {
                $form->model()->status = 1;
                $form->model()->app_key = $this->getAppKey();
                $form->model()->tenant_id = SaaSAdmin::user()->id;
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
        $id = request()->route('upgrade');
        $show = new Show(AppUpgrade::find($id));
        $show->field('channel_name', '渠道名称');
        $show->field('platform_type', '平台');
        $show->field('version_string', '版本号');
        $show->field('version_num', '版本值');
        $show->field('min_version_num', '最小版本值');
        $show->field('force_upgrade', '强制升级');
        $show->field('gray_percent', '灰度百分比');
        $show->field('upgrade_from', '升级方式');
        $show->field('package_download_url', '安装包下载地址');
        $show->field('package_md5', '安装包MD5');
        $show->field('package_size', '安装包大小');
        $show->field('upgrade_note', '升级说明');
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
        // $cache_key = 'app_config||'.$config_name.'|'.$app_key;
        // Cache::store('api_cache')->forget($cache_key);
    }
}