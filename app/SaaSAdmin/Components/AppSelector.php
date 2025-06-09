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
            $this->apps = \App\Models\App::where('tenant_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->pluck('name', 'app_key')
                ->toArray();
        }
        
        // 获取当前URL的app_key参数
        $this->currentAppKey = Request::route('app_key') ?? '';
        
        // 基础URL路径
        $this->url = admin_url('app/manager');
        $this->allUrl = admin_url('/');
    }

    public function render()
    {
        // 直接构建选项HTML
        $options = '';
        $isAllApps = empty($this->currentAppKey);
        $options .= '<option value="" ' . ($isAllApps ? 'selected' : '') . '>全部应用</option>';
        
        foreach ($this->apps as $appKey => $appName) {
            $selected = $appKey == $this->currentAppKey ? 'selected' : '';
            $options .= "<option value=\"{$appKey}\" {$selected}>{$appName}</option>";
        }
        
        // 返回完整的HTML
        return <<<HTML
<div class="user-panel" style="padding: 16px;">
    <select class="form-control" name="app_select" id="app_key_selector">
        {$options}
    </select>
</div>
<script>
$(function() {
    // 初始化 select2
    $('#app_key_selector').select2({
        allowClear: false,
        width: '100%',
        minimumInputLength: -1,
        minimumResultsForSearch: -1,
        
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