<?php

namespace App\SaaSAdmin\Controllers\Manager;

use Encore\Admin\Grid;
use App\SaaSAdmin\AppKey;
use App\Models\AppUpgrade;
use Encore\Admin\Layout\Content;
use App\Models\AppUpgradeChannel;
use App\Http\Controllers\Controller;

class AppUpgradeChannelController extends Controller
{
    use AppKey;
    
    // 定义最大渠道数量
    const MAX_CHANNELS = 10;

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
                'channel_name' => 'default', 
                'is_default' => 1
            ]);
            $channels = collect([$defaultChannel]);
            $channel_id = $defaultChannel->id;
        }
        
        // 如果没有指定渠道ID，使用默认渠道
        if (!$channel_id) {
            $defaultChannel = $channels->where('is_default', 1)->first();
            if ($defaultChannel) {
                $channel_id = $defaultChannel->id;
            } else {
                $channel_id = $channels->first()->id;
            }
        }
        
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
            'current_channel_count' => $channels->count()
        ])->render();
        
        return $html;
    }

    /**
     * 构建特定渠道的版本网格
     */
    protected function buildChannelGrid(int $channel_id, string $channelName)
    {
        $grid = new Grid(new AppUpgrade());
        $grid->model()->where('app_key', $this->getAppKey())->where('channel_id', $channel_id);
        $grid->model()->orderBy('created_at', 'desc');
        
        $grid->header(function () use ($channelName) {
            return "<h3>{$channelName} - 版本列表</h3>";
        });
        
        $grid->column('id', 'ID');
        $grid->column('version_str', '版本号');
        $grid->column('version_number', '版本码');
        $grid->column('status', '状态')->using([0 => '未生效', 1 => '已生效']);
        $grid->column('force_upgrade', '强制升级')->using([0 => '否', 1 => '是']);
        $grid->column('created_at', '创建时间');
        
        // 添加创建版本按钮
        $grid->tools(function ($tools) use ($channel_id) {
            $tools->append('<a href="'.admin_url('app-upgrades/create?channel_id='.$channel_id).'" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> 添加版本</a>');
        });
        
        return $grid->render();
    }
    
    /**
     * 添加渠道
     */
    public function store()
    {
        $data = request()->validate([
            'channel_name' => 'required|string|max:50',
        ]);
        
        // 检查渠道数量限制
        $channelCount = AppUpgradeChannel::where('app_key', $this->getAppKey())->count();
        if ($channelCount >= self::MAX_CHANNELS) {
            return response()->json([
                'status' => false, 
                'message' => '最多只能添加' . self::MAX_CHANNELS . '个渠道'
            ]);
        }
        
        // 从请求中获取 app_key
        $data['app_key'] = $this->getAppKey();
        
        // 自动生成渠道代码
        $data['channel_code'] = 'channel_' . time() . rand(100, 999);
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
    public function destroy($id)
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