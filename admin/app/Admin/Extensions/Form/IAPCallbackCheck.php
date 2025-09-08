<?php

namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field;
use Encore\Admin\Facades\Admin;

class IAPCallbackCheck extends Field
{
    protected $view = 'saas.form.apple-check';
    protected $testUrl = '';
    protected $callbackUrl = '';
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

    public function callbackUrl($url)
    {
        $this->callbackUrl = $url;
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
            .interface-check-waiting { color: #f39c12; }
            .interface-check-loading { display: none; }
        CSS);

        // 将依赖字段添加到视图变量
        $this->addVariables([
                'testUrl' => $this->testUrl,
                'callbackUrl' => $this->callbackUrl,
                'buttonText' => $this->buttonText,
                'dependentFields' => json_encode($this->dependentFields),
            ])
            ->value(isset($this->default) ? $this->default : $this->value());

        $fields = empty($this->dependentFields) ? '[]' : json_encode($this->dependentFields);
        
        // CSS 代码保持不变 ...

        // 修改 JavaScript 代码
        Admin::script(<<<JS
            var callbackVerify = function(btn, result, uuid) {
                var hiddenInput = btn.siblings('[name="' + btn.data('field') + '"]');
                $.getJSON(btn.data('callback-url'), {uuid: uuid}, function(data) {
                    if (data.status) {
                        result.removeClass('interface-check-fail')
                              .removeClass('interface-check-waiting')
                              .addClass('interface-check-success')
                              .html('<i class="fa fa-check"></i> <span>' + (data.message ? data.message : successText) + '</span>');
                        hiddenInput.val(1);
                        clearInterval(interval);
                    } else {
                        if(typeof data.waiting !== 'undefined' && data.waiting == false) {
                            result.removeClass('interface-check-success')
                                  .removeClass('interface-check-waiting')
                                  .addClass('interface-check-fail')
                                  .html('<i class="fa fa-times"></i> <span>' + (data.message ? data.message : failText) + '</span>');
                            clearInterval(interval);
                        } else {
                            result.removeClass('interface-check-success')
                                  .removeClass('interface-check-fail')
                                  .addClass('interface-check-waiting')
                                  .html('<i class="fa fa-spinner fa-spin"></i> <span>' + (data.message ? data.message : failText) + '</span>');
                        }
                    }
                });
            }

            var interval = null;

            $(function () {
                $('.interface-check-btn.{$this->column}').on('click', function() {
                    var btn = $(this);
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
                                var value = input.val();
                                // 对于select，确保获取选中的值
                                if (input.is('select')) {
                                    value = input.find('option:selected').val();
                                }
                                formData[field] = value;
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
                                      .addClass('interface-check-waiting')
                                      .html('<i class="fa fa-spinner fa-spin"></i> <span>' + response.message + '</span>');
                                var uuid = response.data.uuid;
                                clearInterval(interval);
                                interval = setInterval(function() {
                                    callbackVerify(btn, result, uuid);
                                }, 1000);
                            } else {
                                result.removeClass('interface-check-success')
                                      .addClass('interface-check-fail')
                                      .html('<i class="fa fa-times"></i> <span>' + failText + 
                                            (response.message ? ': ' + response.message : '') + '</span>');
                            }
                            result.show();
                        },
                        error: function(xhr) {
                            var message = xhr.responseJSON ? xhr.responseJSON.message : '请求失败';
                            result.removeClass('interface-check-success')
                                  .addClass('interface-check-fail')
                                  .html('<i class="fa fa-times"></i> <span>' + failText + ': ' + message + '</span>')
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