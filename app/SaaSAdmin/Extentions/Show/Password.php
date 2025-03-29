<?php

namespace App\SaaSAdmin\Extentions\Show;

use Encore\Admin\Show\AbstractField;

class Password extends AbstractField
{
    public $escape = false;

    public function render($arg = '')
    {
        if (empty($this->value)) {
            return '';
        }

        // 生成唯一ID
        $id = 'pwd_' . uniqid();

        // 添加必要的 JavaScript
        $script = <<<JS
        function togglePassword_{$id}(icon) {
            var span = $(icon).prev();
            if (span.text() === '••••••') {
                span.text('{$this->value}');
                $(icon).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                span.text('••••••');
                $(icon).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
            }
        }
JS;

        // 返回HTML
        return <<<HTML

            <span id="{$id}" style="font-family: monospace;">••••••</span>
            <a href="javascript:void(0);" 
               onclick="togglePassword_{$id}(this)" 
               class="btn btn-sm btn-default" 
               style="margin-left: 5px; cursor: pointer; color: #666;">
               <i class="fa fa-eye"></i>
            </a>
            <script>{$script}</script>
 
HTML;
    }
}