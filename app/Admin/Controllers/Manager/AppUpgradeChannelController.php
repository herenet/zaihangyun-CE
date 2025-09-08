<?php

namespace App\Admin\Controllers\Manager;

use Encore\Admin\Grid;
use App\Admin\AppKey;
use App\Models\AppUpgrade;
use Encore\Admin\Layout\Content;
use App\Models\AppUpgradeChannel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Admin\Actions\VersionConfigCopy;

class AppUpgradeChannelController extends Controller
{
    use AppKey;
    
    // 定义最大渠道数量
    const MAX_CHANNELS = 10;
    const DEFAULT_CHANNEL_NAME = 'default';

    public function index(Content $content)
    {
        $content->title('版本管理');
        
        // 获取当前选中的渠道ID
        $channel_id = request()->get('channel_id');
        
        // 获取所有渠道
        $channels = AppUpgradeChannel::where('app_key', $this->getAppKey())->get();
        
        // 如果没有任何渠道，创建默认渠道
        if ($channels->isEmpty()) {
            $defaultChannel = AppUpgradeChannel::create([
                'app_key' => $this->getAppKey(),
                'channel_name' => self::DEFAULT_CHANNEL_NAME, 
                'is_default' => AppUpgradeChannel::IS_DEFAULT
            ]);
            $channels = collect([$defaultChannel]);
            $channel_id = $defaultChannel->id;
        }
        
        // 如果没有指定渠道ID，使用默认渠道
        if (!$channel_id) {
            $defaultChannel = $channels->where('is_default', AppUpgradeChannel::IS_DEFAULT)->first();
            if ($defaultChannel) {
                $channel_id = $defaultChannel->id;
            } else {
                $channel_id = $channels->first()->id;
            }
        }

        //默认渠道排在最前面
        $channels = $channels->sortBy('created_at');
        
        $content->row(function ($row) use ($channels, $channel_id) {
            // 左侧版本内容
            $row->column(10, function ($column) use ($channel_id) {
                $channel = AppUpgradeChannel::find($channel_id);
                if ($channel) {
                    $column->append($this->buildChannelGrid($channel_id, $channel->channel_name));
                }
            });
            
            // 右侧渠道列表
            $row->column(2, $this->buildChannelList($channels, $channel_id));
        });
        
        return $content;
    }
    
    /**
     * 构建渠道侧边栏
     */
    protected function buildChannelList($channels, $current_channel_id)
    {
        $html = view('saas.grid.upgrade_channel', [
            'channels' => $channels,
            'current_channel_id' => $current_channel_id,
            'max_channels' => self::MAX_CHANNELS,
            'current_channel_count' => $channels->count(),
            'channel_base_url' => admin_url('app/manager/'.$this->getAppKey().'/upgrade'),
        ])->render();
        
        return $html;
    }

    /**
     * 构建特定渠道的版本网格
     */
    protected function buildChannelGrid(int $channel_id, string $channelName)
    {
        $app_key = $this->getAppKey();
        $grid = new Grid(new AppUpgrade());
        $grid->resource(admin_url('app/manager/'.$app_key.'/version/'.$channel_id.'/item'));
        $grid->model()->where('app_key', $this->getAppKey())->where('channel_id', $channel_id);
        $grid->model()->orderBy('created_at', 'desc');
        $grid->tools(function ($tools) use ($app_key) {
            $tools->append('<a href="/docs/1.x/apis/app_upgrade" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-book"></i> 查看接口文档</a>');
        });
        $grid->fixColumns(2, -2);
        
        $grid->header(function () use ($channelName) {
            return "<h3>{$channelName} - 版本列表</h3>";
        });
        
        $grid->column('id', 'ID')->hide();
        $grid->column('version_str', '版本号')->label();
        $grid->column('version_num', '版本码')->sortable();
        
        $grid->column('min_version_num', '最小版本值')->help('最小版本值，低于此版本将强制升级');
        $grid->column('force_upgrade', '强制升级')
        ->using([
            0 => '否',
            1 => '是',
        ])
        ->label([
            0 => 'default',
            1 => 'success',
        ]);
        $grid->column('enabled', '升级开关')
            ->zhySwitch([
                'on' => ['value' => 1, 'text' => '开启', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'primary'],
            ], admin_url('app/manager/'.$app_key.'/version/'.$channel_id.'/item'))
            ->help('是否开启升级，同一渠道同一平台只允许开启一个版本的升级');

        $grid->column('gray_percent', '灰度升级')->display(function ($gray_percent, $column)  {
                /** @var AppUpgrade $this */
                if($this->gray_upgrade == 1) {
                    return '<span class="label label-success">'.$this->gray_percent.'%</span>';
                } else {
                    return '<span class="label label-default">关闭</span>';
                }
            })
            ->help('是否开启灰度升级');
        $grid->column('upgrade_from', '升级方式')->display(function ($upgrade_from) {
            return AppUpgrade::$upgradeFromMap[$upgrade_from];
        });
        $grid->column('package_download_url', '安装包下载地址')->downloadable()->help('安装包下载地址，用于下载安装包');
        $grid->column('package_md5', '安装包MD5')->help('安装包MD5，用于验证安装包完整性');
        $grid->column('package_size', '安装包大小');
        $grid->column('upgrade_note', '升级说明')
            ->display(function ($value) {
                // 手动限制显示长度
                return \Illuminate\Support\Str::limit($value, 30);
            })
            ->modal('升级说明', function ($model) {
                return nl2br($model->upgrade_note);
            })
            ->help('升级说明，用于提示用户升级');
        $grid->column('updated_at', '更新时间');
        $grid->column('created_at', '创建时间');

        $grid->actions(function ($actions) use ($app_key) {
            // 添加复制按钮，直接链接到创建页面并带上源ID参数
            $actions->add(new VersionConfigCopy($app_key, $actions->row->channel_id));
        });

        $grid->filter(function ($filter) {
            $filter->equal('enabled', '升级开关')->select([
                0 => '关闭',
                1 => '开启',
            ])->config('allowClear', false)
            ->config('minimumResultsForSearch', 'Infinity');
        });
    
        $grid->disableBatchActions();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
        
        return $grid->render();
    }
    
    /**
     * 添加渠道
     */
    public function store()
    {
        $validator = Validator::make(request()->all(),[
            'channel_name' => 'required|string|max:30|regex:/^[a-zA-Z0-9_]+$/'
        ], [
            'channel_name.regex' => '渠道名称只能包含字母、数字和下划线'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false, 
                'message' => $validator->errors()->first()
            ]);
        }

        $data = $validator->validated();

        // 检查渠道数量限制
        $channelCount = AppUpgradeChannel::where('app_key', $this->getAppKey())->count();
        if ($channelCount >= self::MAX_CHANNELS) {
            return response()->json([
                'status' => false, 
                'message' => '最多只能添加' . self::MAX_CHANNELS . '个渠道'
            ]);
        }

        // 检查渠道名称是否已存在
        if (AppUpgradeChannel::where('app_key', $this->getAppKey())->where('channel_name', $data['channel_name'])->exists()) {
            return response()->json([
                'status' => false, 
                'message' => '渠道名称已存在'
            ]);
        }
        
        // 从请求中获取 app_key
        $data['app_key'] = $this->getAppKey();
        // 自动生成渠道代码
        $data['is_default'] = 0; // 非默认渠道
        
        // 创建新渠道
        $channel = AppUpgradeChannel::create($data);
        
        return response()->json([
            'status' => true, 
            'message' => '渠道添加成功',
            'channel' => $channel
        ]);
    }
    
    /**
     * 删除渠道
     */
    public function destroy($appKey, $id)
    {
        $channel = AppUpgradeChannel::find($id);
        
        if (!$channel) {
            return response()->json(['status' => false, 'message' => '渠道不存在']);
        }
        
        // 验证 app_key 是否匹配
        if ($channel->app_key !== $this->getAppKey()) {
            return response()->json(['status' => false, 'message' => '无权操作此渠道']);
        }
        
        // 检查是否是默认渠道
        if ($channel->is_default == 1) {
            return response()->json(['status' => false, 'message' => '默认渠道不能删除']);
        }
        
        // 检查是否有版本依赖
        if (AppUpgrade::where('channel_id', $id)->exists()) {
            return response()->json(['status' => false, 'message' => '该渠道存在关联版本，无法删除']);
        }
        
        $channel->delete();
        
        return response()->json(['status' => true, 'message' => '渠道删除成功']);
    }
}