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
use App\SaaSAdmin\Extensions\Nav\Link;
use App\SaaSAdmin\Components\AppSelector;
use App\SaaSAdmin\Extensions\Nav\Dropdown;
use App\SaaSAdmin\Extensions\Nav\Shortcut;
use App\SaaSAdmin\Extensions\Show\Password;
use App\SaaSAdmin\Extensions\Form\AliyunSmsCheck;
use App\SaaSAdmin\Extensions\Form\InterfaceCheck;

//判断URL中是否是以app/manager开头
if(strpos($_SERVER['REQUEST_URI'], 'app/manager') === false){
    $layout_type = 'custom';
}else{
    $layout_type = 'default';
}

Column::extend('password', function($value, $args) {
    if(empty($value)){
        return '';
    }
    $maskChar = $args[0] ?? '*';
    $maskLength = $args[1] ?? 6;
    
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

Form::extend('interfaceCheck', InterfaceCheck::class);
Form::extend('aliyunSmsCheck', AliyunSmsCheck::class);

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


$appSelector = (new AppSelector())->render();
view()->share('appSelector', $appSelector);
Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    $navbar->right(Shortcut::make([
        '微信开放平台设置' => 'global/config/wechat/platform',
        '微信商户号设置' => 'global/config/wechat/payment',
        '阿里云AccessKey设置' => 'global/config/aliyun/access',
    ], 'fa-gears')->title('全局设置'));
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
