<?php

namespace App\SaaSAdmin\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Request;
use App\SaaSAdmin\Facades\SaaSAdmin;

class AppSelector implements Renderable
{
    protected $apps = [];
    protected $currentAppKey = '';
    protected $url = '';
    protected $allUrl = '';

    public function __construct()
    {
        // 获取当前用户的所有应用
        $user = SaaSAdmin::user();
        if($user){
            $apps = \App\Models\App::where('tenant_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get(['app_key', 'name', 'platform_type']);
            
            // 转换为数组格式，包含平台信息
            $this->apps = [];
            foreach ($apps as $app) {
                $platformType = $app->platform_type == 1 ? 'Android' : 'iOS';
                $iconClass = $app->platform_type == 1 ? 'fa-android text-success' : 'fa-apple text-dark';
                $this->apps[] = [
                    'app_key' => $app->app_key,
                    'name' => $app->name,
                    'platform_type' => $app->platform_type,
                    'platform_name' => $platformType,
                    'icon_class' => $iconClass
                ];
            }
        }
        
        // 获取当前URL的app_key参数
        $this->currentAppKey = Request::route('app_key') ?? '';
        
        // 基础URL路径
        $this->url = admin_url('app/manager');
        $this->allUrl = admin_url('/');
    }

    public function render()
    {
        // 构建选项HTML，使用图标显示平台类型
        $options = '';
        $isAllApps = empty($this->currentAppKey);
        $options .= '<option value="" ' . ($isAllApps ? 'selected' : '') . ' data-icon-class="fa-th-large text-primary">全部应用</option>';
        
        if($this->apps && count($this->apps) > 0) {
            foreach ($this->apps as $app) {
                $selected = $app['app_key'] == $this->currentAppKey ? 'selected' : '';
                $options .= "<option value=\"{$app['app_key']}\" {$selected} data-platform=\"{$app['platform_name']}\" data-icon-class=\"{$app['icon_class']}\">{$app['name']}</option>";
            }
        }
        
        // 返回完整的HTML
        return <<<HTML
<style>
.app-selector-option {
    display: flex;
    align-items: center;
}
.app-selector-option i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}
</style>
<div class="user-panel" style="padding: 16px;">
    <select class="form-control" name="app_select" id="app_key_selector">
        {$options}
    </select>
</div>
<script>
$(function() {
    // 初始化 select2 with custom template
    $('#app_key_selector').select2({
        allowClear: false,
        width: '100%',
        minimumInputLength: -1,
        minimumResultsForSearch: -1,
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            var \$option = \$(data.element);
            var iconClass = \$option.data('icon-class');
            
            if (iconClass) {
                return \$('<div class="app-selector-option"><i class="fa ' + iconClass + '"></i><span>' + data.text + '</span></div>');
            }
            return data.text;
        },
        templateSelection: function(data) {
            var \$option = \$(data.element);
            var iconClass = \$option.data('icon-class');
            if (iconClass) {
                // 选中状态不显示分隔线，只显示图标和文字
                return \$('<span style="display: flex; align-items: center;"><i class="fa ' + iconClass + '" style="margin-right: 8px; width: 16px; text-align: center;"></i><span>' + data.text + '</span></span>');
            }
            return data.text;
        }
    });
    
    // 处理应用切换
    $('#app_key_selector').on('change', function() {
        var appKey = $(this).val();
        if (appKey) {
            window.location.href = '{$this->url}/' + appKey;
        } else {
            // 跳转到全部应用页面
            window.location.href = '{$this->allUrl}';
        }
    });
});
</script>
HTML;
    }
} 