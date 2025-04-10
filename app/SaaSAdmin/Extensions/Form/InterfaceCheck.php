<?php

namespace App\SaaSAdmin\Extensions\Form;

use Encore\Admin\Form\Field;
use Encore\Admin\Facades\Admin;

class InterfaceCheck extends Field
{
    protected $view = 'saas.form.interface-check';
    protected $testUrl = '';
    protected $buttonText = '测试接口';
    protected $dependentFields = [];
    
    protected function initView()
    {
        $this->addVariables([
            'successText' => '测试通过',
            'failText' => '测试失败',
            'element' => $this->getElementClass()
        ]);
    }

    public function dependentOn($fields)
    {
        $this->dependentFields = is_array($fields) ? $fields : [$fields];
        return $this;
    }

    public function default($default) : self
    {
        $this->default = $default;
        return $this;
    }

    public function buttonText($text)
    {
        $this->buttonText = $text;
        return $this;
    }

    public function testUrl($url)
    {
        $this->testUrl = $url;  // 保存 url 到属性
        return $this;
    }

    public function render()
    {
        $this->initView();

        // 添加必要的 CSS 样式
        Admin::style(<<<CSS
            .interface-check-btn { margin-right: 10px; }
            .interface-check-result { display: none; margin-left: 10px; }
            .interface-check-success { color: #00a65a; }
            .interface-check-fail { color: #dd4b39; }
            .interface-check-loading { display: none; }
        CSS);

        // 将依赖字段添加到视图变量
        $this->addVariables([
                'testUrl' => $this->testUrl,
                'buttonText' => $this->buttonText,
                'dependentFields' => json_encode($this->dependentFields),
            ])
            ->value(isset($this->default) ? $this->default : $this->value());

        $fields = empty($this->dependentFields) ? '[]' : json_encode($this->dependentFields);
        
        // CSS 代码保持不变 ...

        // 修改 JavaScript 代码
        Admin::script(<<<JS
            $(function () {
                $('.interface-check-btn').on('click', function() {
                    var btn = $(this);
                    var hiddenInput = btn.siblings('[name="' + btn.data('field') + '"]');
                    var form = btn.closest('.form-horizontal');
                    var result = btn.siblings('.interface-check-result');
                    var loading = btn.siblings('.interface-check-loading');
                    var successText = result.data('success-text');
                    var failText = result.data('fail-text');
                    var dependentFields = {$fields};
                    
                    // 获取指定字段的值
                    var formData = {};

                    var formData = {
                        _token: LA.token  // 添加 CSRF token
                    };

                    dependentFields.forEach(function(field) {
                        var input = form.find('[name="' + field + '"]');
                        if (input.length) {
                            // 处理数组形式的字段名 (例如: config[key])
                            if (field.indexOf('[') !== -1) {
                                var matches = field.match(/([^\[]+)\[([^\]]*)\]/);
                                if (matches) {
                                    if (!formData[matches[1]]) formData[matches[1]] = {};
                                    formData[matches[1]][matches[2]] = input.val();
                                }
                            } else {
                                formData[field] = input.val();
                            }
                        }
                    });
                    
                    // 显示加载状态
                    btn.prop('disabled', true);
                    loading.show();
                    result.hide();
                    
                    // 发送测试请求
                    $.ajax({
                        url: btn.data('test-url'),
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status) {
                                result.removeClass('interface-check-fail')
                                      .addClass('interface-check-success')
                                      .html('<i class="fa fa-check"></i> ' + successText);
                                hiddenInput.val(1);
                            } else {
                                result.removeClass('interface-check-success')
                                      .addClass('interface-check-fail')
                                      .html('<i class="fa fa-times"></i> ' + failText + 
                                            (response.message ? ': ' + response.message : ''));
                            }
                            result.show();
                        },
                        error: function(xhr) {
                            var message = xhr.responseJSON ? xhr.responseJSON.message : '请求失败';
                            result.removeClass('interface-check-success')
                                  .addClass('interface-check-fail')
                                  .html('<i class="fa fa-times"></i> ' + failText + ': ' + message)
                                  .show();
                        },
                        complete: function() {
                            btn.prop('disabled', false);
                            loading.hide();
                        }
                    });
                });
            });
        JS);

        return parent::render();
    }
}