<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use Encore\Admin\Form;
use Encore\Admin\Show;
use App\Models\SaaSMenu;
use App\Models\ManagerMenu;
use Encore\Admin\Grid\Column;
use App\SaaSAdmin\Components\AppSelector;
use App\SaaSAdmin\Extensions\Nav\Shortcut;
use App\SaaSAdmin\Extensions\Show\Password;
use App\SaaSAdmin\Extensions\Column\ZhySwitch;
use App\SaaSAdmin\Extensions\Form\ZhyKeyValue;
use App\SaaSAdmin\Extensions\Form\AliyunSmsCheck;
use App\SaaSAdmin\Extensions\Form\IAPSingleCheck;
use App\SaaSAdmin\Extensions\Form\InterfaceCheck;
use App\SaaSAdmin\Extensions\Form\IAPCallbackCheck;
use App\SaaSAdmin\Extensions\Editormd\MyEditorField;
use App\SaaSAdmin\Facades\SaaSAdmin;

//判断URL中是否是以app/manager开头
if(strpos($_SERVER['REQUEST_URI'], 'app/manager') === false){
    $layout_type = 'custom';
}else{
    $layout_type = 'default';
}

Column::extend('password', function($value, $args, $length = null) {
    if(empty($value)){
        return '';
    }
    $maskChar = $args[0] ?? '*';
    $maskLength = $length ?? strlen($value);
    
    $id = uniqid('pwd_');
    $mask = str_repeat($maskChar, $maskLength);
    
    return <<<HTML
    <span id="{$id}">{$mask}</span>
    <a href="javascript:void(0);" onclick="togglePassword('{$id}', '{$value}', '{$mask}')" class="fa fa-eye"></a>
HTML;
});

Column::extend('prependIcon', function ($value, $icon) {
    if(empty($value)){
        return '';
    }
    return "<span style='color: #999;'><i class='fa fa-$icon'></i>  $value</span>";
});
Show::extend('password', Password::class);
Form::extend('myEditorMd', MyEditorField::class);
Form::extend('interfaceCheck', InterfaceCheck::class);
Form::extend('aliyunSmsCheck', AliyunSmsCheck::class);
Form::extend('iapSingleCheck', IAPSingleCheck::class);
Form::extend('iapCallbackCheck', IAPCallbackCheck::class);
Form::extend('zhyKeyValue', ZhyKeyValue::class);
Column::extend('zhySwitch', ZhySwitch::class);

Admin::script(<<<JS
$(document).ready(function() {
    window.togglePasswordVisibility = function(icon) {
        var input = $(icon).parent().siblings('input');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            $(icon).removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            $(icon).removeClass('fa-eye-slash').addClass('fa-eye');
        }
    };
    window.togglePassword =function (id, value, mask) {
        var span = $('#'+id);
        var icon = span.next('a');
        if (span.text() === mask) {
            span.text(value);
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            span.text(mask);
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }
});
JS);

// API统计功能 - 独立于pjax刷新
Admin::html(<<<HTML
<script>
// API统计全局管理器 - 防止pjax重复初始化
window.ApiStatsManager = window.ApiStatsManager || {
    initialized: false,
    lastUpdateTime: 0,
    
    init: function() {
        if (this.initialized) {
            // 如果已经初始化，只需要重新绑定事件（因为DOM被重新创建）
            this.bindEvents();
            return;
        }
        
        this.initialized = true;
        this.bindEvents();
        this.loadStats();
        
        // 设置定时刷新（每5分钟）
        setInterval(() => {
            this.loadStats(false);
        }, 5 * 60 * 1000);
    },
    
    bindEvents: function() {
        // 移除旧事件监听器，重新绑定
        \$(document).off('click', '#refresh-api-stats').on('click', '#refresh-api-stats', (e) => {
            e.preventDefault();
            this.refreshStats();
        });
    },
    
    updateDisplay: function(apiData, status) {
        var loading = \$('#api-stats-loading');
        var data = \$('#api-stats-data');
        var error = \$('#api-stats-error');
        
        if (loading.length === 0) return; // DOM不存在，跳过更新
        
        // 更新数据元素
        \$('#api-stats-numbers').text(apiData.current + '/' + apiData.limit);
        \$('#api-stats-progress')
            .css('width', apiData.percentage + '%')
            .css('background', status.color)
            .attr('title', '使用率: ' + apiData.percentage + '%');
        \$('#api-stats-status')
            .css('color', status.color)
            .html('<i class="fa fa-' + status.icon + '" style="margin-right: 2px;"></i>' + status.text);
        
        // 显示数据，隐藏加载和错误状态
        loading.hide();
        data.css('display', 'flex');
        error.hide();
        
        this.lastUpdateTime = Date.now();
    },
    
    showError: function(message) {
        var loading = \$('#api-stats-loading');
        var data = \$('#api-stats-data');
        var error = \$('#api-stats-error');
        
        if (loading.length === 0) return; // DOM不存在，跳过更新
        
        loading.hide();
        data.hide();
        \$('#api-stats-error-text').text(message);
        error.show();
    },
    
    loadStats: function(showLoading = true) {
        var loading = \$('#api-stats-loading');
        var data = \$('#api-stats-data');
        var error = \$('#api-stats-error');
        
        if (loading.length === 0) return; // DOM不存在，跳过请求
        
        // 防止频繁请求（1分钟内不重复请求）
        if (Date.now() - this.lastUpdateTime < 60000) {
            return;
        }
        
        if (showLoading) {
            loading.show();
            data.hide();
            error.hide();
        }
        
        \$.get('/console/api-stats/today')
            .done((response) => {
                if (response.success && response.data) {
                    this.updateDisplay(response.data, response.data.status);
                } else {
                    this.showError('获取失败');
                }
            })
            .fail(() => {
                this.showError('网络错误');
            });
    },
    
    refreshStats: function() {
        var btn = \$('#refresh-api-stats');
        var icon = btn.find('i');
        
        if (btn.length === 0) return;
        
        // 添加旋转动画
        icon.addClass('fa-spin');
        btn.prop('disabled', true);
        
        // 强制刷新（忽略时间限制）
        this.lastUpdateTime = 0;
        this.loadStats(false);
        
        // 移除旋转动画
        setTimeout(() => {
            icon.removeClass('fa-spin');
            btn.prop('disabled', false);
        }, 1000);
    }
};

// 页面加载时初始化
\$(document).ready(function() {
    window.ApiStatsManager.init();
});

// pjax刷新后重新初始化
\$(document).on('pjax:complete', function() {
    // 延迟一点确保DOM完全渲染
    setTimeout(() => {
        window.ApiStatsManager.init();
    }, 100);
});
</script>
HTML);

Admin::css("/vendor/laravel-admin/modern-admin.css");

$appSelector = (new AppSelector())->render();
view()->share('appSelector', $appSelector);
Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    // 添加租户API统计显示（使用AJAX异步加载）
    if (SaaSAdmin::user()) {
        $navbar->left(view('saas.partials.api-stats')->render());
    }

    $navbar->right(Shortcut::make([
        '微信开放平台设置' => 'global/config/wechat/platform',
        '微信商户号设置' => 'global/config/wechat/payment',
        '阿里云AccessKey设置' => 'global/config/aliyun/access',
        '苹果服务端API请求证书设置' => 'global/config/apple/apicert',
    ], 'fa-gears')->title('全局设置 <i class="fa fa-caret-down"></i>'));

    $navbar->right(
        '<li class="dropdown">
            <a href="/docs" class="dropdown-toggle" target="_blank">
                <i class="fa fa-book"></i> API接入文档
            </a>
        </li>'
    );
});

if($layout_type == 'custom'){
    view()->share('custom_menu', ['menu' => app(SaasMenu::class)->allNodes()]);
    app('view')->prependNamespace('admin', resource_path('views/saas'));
}else{
    Encore\Admin\Form::forget(['map', 'editor']);
    $appKey = request()->route('app_key');
    
    view()->share('custom_menu', ['menu' => app(ManagerMenu::class)->allNodes($appKey)]);
    
    app('view')->prependNamespace('admin', resource_path('views/manager'));
}
