<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\SaaSAdmin\AppKey;
use App\Models\AppUpgrade;
use Illuminate\Validation\Rule;
use Encore\Admin\Layout\Content;
use App\Models\AppUpgradeChannel;
use Illuminate\Support\MessageBag;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Cache;
use Encore\Admin\Controllers\AdminController;


class AppUpgradeController extends AdminController
{
    use AppKey;

    public function create(Content $content)
    {
        return $content
            ->title('新增版本')
            ->body($this->form());
    }

    public function edit($id, Content $content)
    {
        $id = request()->route('item');
        $app_key = $this->getAppKey();
        $channel_id = request()->route('channel_id');
        $config = AppUpgrade::where('id', $id)->where('app_key', $app_key)->where('channel_id', $channel_id)->first();
        if(!$config) {
            return response()->json(['status' => false, 'message' => '配置不存在']);
        }
        return parent::edit($id, $content)->title('编辑版本')->description('编辑');
    }

    public function update($id)
    {
        $id = request()->route('item');
        $app_key = $this->getAppKey();
        $channel_id = request()->route('channel_id');
        $config = AppUpgrade::where('id', $id)->where('app_key', $app_key)->where('channel_id', $channel_id)->first();
        if(!$config) {
            return response()->json(['status' => false, 'message' => '配置不存在']);
        }
        $this->clearAPICache($channel_id);
        return parent::update($id);
    }

    public function destroy($id)
    {
        $id = request()->route('item');
        $app_key = $this->getAppKey();
        $channel_id = request()->route('channel_id');
        $config = AppUpgrade::where('id', $id)->where('app_key', $app_key)->where('channel_id', $channel_id)->first();
        if(!$config) {
            return response()->json(['status' => false, 'message' => '配置不存在']);
        }
        $this->clearAPICache($channel_id);
        return parent::destroy($id);
    }

    public function form()
    {
        $form = new Form(new AppUpgrade());
        $app_key = $this->getAppKey();
        $tenant_id = SaaSAdmin::user()->id;
        $channel_id = request()->input('channel_id', request()->route('channel_id'));

        // 检查是否是复制操作
        $copyFromId = request()->get('copy_from');
        $sourceConfig = null;
        
        if ($copyFromId) {
            $sourceConfig = AppUpgrade::where('id', $copyFromId)
                ->where('tenant_id', $tenant_id)
                ->where('app_key', $app_key)
                ->first();
        }

        $form->select('channel_id', '渠道')
            ->options(AppUpgradeChannel::where('app_key', $app_key)->where('tenant_id', $tenant_id)->pluck('channel_name', 'id'))
            ->required()
            ->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity')
            ->rules('required|integer|min:1')
            ->help('渠道，用于选择渠道')
            ->value($channel_id);

        $form->switch('enabled', '升级开关')
            ->states([
                'on' => ['value' => 1, 'text' => '开启', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'primary'],
            ])
            ->help('升级开关，用于控制是否开启升级，同一渠道同一平台只允许开启一个版本的升级')
            ->value($sourceConfig ? $sourceConfig->enabled : 1);

        $form->text('version_str', '版本号')
            ->required()
            ->rules('required|string|max:32')
            ->help('版本号，用于显示，最多32个字符')
            ->value($sourceConfig ? $sourceConfig->version_str : '1.0.0');
        $form->number('version_num', '版本值')
            ->default(1)
            ->required()
            ->rules('required|integer|min:1')
            ->help('版本值，用于比较版本')
            ->value($sourceConfig ? $sourceConfig->version_num : 1);
        $form->select('upgrade_from', '升级方式')
            ->options(AppUpgrade::$upgradeFromMap)
            ->required()
            ->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity')
            ->rules('in:'.implode(',', array_keys(AppUpgrade::$upgradeFromMap)))
            ->when(2, function ($form) use ($sourceConfig) {
                $form->text('package_download_url', '安装包下载地址')
                    ->rules('nullable|string|max:255|required_if:upgrade_from,2', [
                        'required_if' => '安装包下载地址不能为空',
                    ])
                    ->help('安装包下载地址，用于下载安装包')
                    ->value($sourceConfig ? $sourceConfig->package_download_url : '');
                $form->text('package_md5', '安装包MD5')
                    ->rules('nullable|string|max:64|required_if:upgrade_from,2', [
                        'required_if' => '安装包MD5不能为空',
                    ])
                    ->help('安装包MD5，用于验证安装包完整性')
                    ->value($sourceConfig ? $sourceConfig->package_md5 : '');
            })
            ->help('升级方式，用于选择升级方式')
            ->value($sourceConfig ? $sourceConfig->upgrade_from : 1);
        $form->radio('gray_upgrade', '灰度升级')
            ->options([
                1 => '开启',
                0 => '关闭',
            ])->required()->when(1, function ($form) use ($sourceConfig) {
                $form->number('gray_percent', '灰度百分比')
                    ->rules('integer|min:1|max:99')
                    ->default(30)
                    ->required()
                    ->value($sourceConfig ? $sourceConfig->gray_percent : 30)
                    ->help('灰度百分比，1-99');
            })
            ->value($sourceConfig ? $sourceConfig->gray_upgrade : 0)
            ->help('灰度升级，用于灰度升级');
        $form->number('min_version_num', '最小版本值')
            ->rules('integer|min:1')
            ->help('最小版本值，小于或等于此版本不管是否开启强制升级都将强制升级，但灰度升级未命中时不会强制升级')
            ->value($sourceConfig ? $sourceConfig->min_version_num : 1);
        $form->switch('force_upgrade', '强制升级')
            ->states([
                'on' => ['value' => 1, 'text' => '是', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '否', 'color' => 'primary'],
            ])
            ->help('强制升级，用于强制升级')
            ->value($sourceConfig ? $sourceConfig->force_upgrade : 0);
        $form->text('package_size', '安装包大小')
            ->rules('nullable|string|max:30')
            ->help('安装包大小，最长30字符')
            ->value($sourceConfig ? $sourceConfig->package_size : 0);
        $form->textarea('upgrade_note', '升级说明')
            ->rules('nullable|string|max:500')
            ->help('升级说明，用于提示用户升级, 不支持html标签，最多500个字符')
            ->value($sourceConfig ? $sourceConfig->upgrade_note : '');

        $form->saving(function (Form $form) use ($channel_id, $app_key, $tenant_id) {
            if($form->isCreating()) {
                $form->model()->channel_id = $channel_id;
                $form->model()->app_key = $this->getAppKey();
                $form->model()->tenant_id = SaaSAdmin::user()->id;
            }

            if($form->enabled == 'on' || $form->enabled == 1) {
                AppUpgrade::where('tenant_id', $tenant_id)
                    ->where('app_key', $app_key)
                    ->where('channel_id', $channel_id)
                    ->update(['enabled' => 0]);
            }
        });

        $form->saved(function (Form $form) {
            if (request()->has('_edit_inline')) {
                // 这是行内编辑操作，只需要返回成功消息，不需要跳转
                return response()->json([
                    'status' => true, 
                    'message' => '操作成功',
                    'then' => ['action' => 'refresh', 'value' => true]
                ]);
            }
            admin_toastr('操作成功', 'success');
            if($form->isCreating() || $form->isEditing()) {
                return redirect(admin_url('app/manager/'.$this->getAppKey().'/upgrade?channel_id='.$form->model()->channel_id));
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

        $form->action = admin_url('app/manager/'.$app_key.'/version/'.$channel_id.'/item');
        
        $form->tools(function (\Encore\Admin\Form\Tools $tools) use ($app_key, $channel_id) {
            $tools->disableList();
            $tools->append('<a class="btn btn-sm btn-default" href="' . admin_url('app/manager/'.$app_key.'/upgrade?channel_id='.$channel_id) . '"><i class="fa fa-list"></i> 列表</a>');
        });

        return $form;
    }

    public function title()
    {
        return '版本信息';
    }

    public function detail($id)
    {
        $app_key = $this->getAppKey();
        $channel_id = request()->route('channel_id');
        $id = request()->route('item');
        $channel = AppUpgradeChannel::where('id', $channel_id)->where('app_key', $app_key)->first();
        $show = new Show(AppUpgrade::where('id', $id)->where('app_key', $app_key)->where('channel_id', $channel_id)->first());
        $show->field('channel_name', '渠道名称')->as(function ($value) use ($channel) {
            return $channel->channel_name;
        });
    
        $show->field('version_str', '版本号');
        $show->field('version_num', '版本值');
        $show->field('min_version_num', '最小版本值');
        $show->field('force_upgrade', '强制升级')->using([
            0 => '否',
            1 => '是',
        ])->badge();
        $show->field('gray_upgrade', '灰度升级')->using([
            0 => '关闭',
            1 => '开启',
        ])->badge();
        if($show->getModel()->gray_upgrade == 1) {
            $show->field('gray_percent', '灰度百分比')->as(function ($value) {
                return $value.'%';
            });
        }
        $show->field('upgrade_from', '升级方式')->using(AppUpgrade::$upgradeFromMap);
        $show->field('package_download_url', '安装包下载地址');
        $show->field('package_md5', '安装包MD5');
        $show->field('package_size', '安装包大小');
        $show->field('upgrade_note', '升级说明')->as(function ($value) {
            return "<pre>{$value}</pre>";
        })->unescape();
        $show->field('updated_at', '更新时间');
        $show->field('created_at', '创建时间');

        $show->setResource(admin_url('app/manager/'.$app_key.'/upgrade?channel_id='.$channel_id));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });
        return $show;
    }

    protected function clearAPICache($channelId)
    {
        $cache_key = 'app_upgrade|'.$channelId;
        Cache::store('api_cache')->forget($cache_key);
    }
}