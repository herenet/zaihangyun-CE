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

use App\Models\SaaSMenu;
use App\Models\ManagerMenu;
use Encore\Admin\Grid\Column;
use App\SaaSAdmin\Extentions\Nav\Link;
use App\SaaSAdmin\Extensions\Grid\Column\Password;

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

view()->share('custom_data', ['app_select' => ['name' => 'app_select', 'options' => ['1' => '1', '2' => '2']]]); 
if($layout_type == 'custom'){
    view()->share('custom_menu', ['menu' => app(SaasMenu::class)->allNodes()]);
    app('view')->prependNamespace('admin', resource_path('views/saas'));
}else{
    $appKey = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
    Encore\Admin\Form::forget(['map', 'editor']);
    view()->share('custom_menu', ['menu' => app(ManagerMenu::class)->allNodes($appKey)]);
    
    Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
        // $navbar->right(Link::make('接入文档', 'https://www.baidu.com', 'fa-book'));
    });   
    app('view')->prependNamespace('admin', resource_path('views/manager'));
}
