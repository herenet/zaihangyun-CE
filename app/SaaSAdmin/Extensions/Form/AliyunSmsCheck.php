<?php

namespace App\SaaSAdmin\Extensions\Form;

use Encore\Admin\Form\Field;
use Encore\Admin\Facades\Admin;

class AliyunSmsCheck extends Field
{
    protected $view = 'saas.form.interface-check';
    protected $testUrl = '';
    protected $buttonText = '测试接口';
    protected $dependentFields = [];
    protected $modal = null;

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
        $this->testUrl = $url;
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
            .interface-check-modal { display: none; }
            .interface-check-modal .modal-body { padding: 20px; }
        CSS);

        // 将依赖字段添加到视图变量
        $this->addVariables([
                'testUrl' => $this->testUrl,
                'buttonText' => $this->buttonText,
                'dependentFields' => json_encode($this->dependentFields),
            ])
            ->value(isset($this->default) ? $this->default : $this->value());

        $fields = empty($this->dependentFields) ? '[]' : json_encode($this->dependentFields);
        
        // 添加模态框 HTML
        Admin::html(<<<HTML
            <div class="modal interface-check-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">短信验证测试</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>手机号</label>
                                <input type="text" class="form-control mobile-input" placeholder="请输入手机号">
                            </div>
                            <div class="form-group">
                                <label>参数</label>
                                <input type="text" class="form-control params-input" placeholder='请输入参数，例如：{"code":"123456"}'>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                            <button type="button" class="btn btn-primary confirm-btn">确定</button>
                        </div>
                    </div>
                </div>
            </div>
        HTML);

        // 修改 JavaScript 代码
        Admin::script(<<<JS
            $(function () {
                $('.interface-check-btn').on('click', function() {
                    var btn = $(this);
                    var modal = $('.interface-check-modal');
                    var confirmBtn = modal.find('.confirm-btn');
                    
                    // 显示模态框
                    modal.modal('show');
                    
                    // 确认按钮点击事件
                    confirmBtn.off('click').on('click', function() {
                        var mobile = modal.find('.mobile-input').val();
                        var params = modal.find('.params-input').val();
                        
                        if (!mobile) {
                            toastr.error('请输入手机号');
                            return;
                        }
                        
                        if (!params) {
                            toastr.error('请输入参数');
                            return;
                        }
                        
                        var hiddenInput = btn.siblings('[name="' + btn.data('field') + '"]');
                        var form = btn.closest('.form-horizontal');
                        var result = btn.siblings('.interface-check-result');
                        var loading = btn.siblings('.interface-check-loading');
                        var successText = result.data('success-text');
                        var failText = result.data('fail-text');
                        var dependentFields = {$fields};
                        
                        // 获取指定字段的值
                        var formData = {
                            _token: LA.token,
                            mobile: mobile,
                            params: params
                        };

                        dependentFields.forEach(function(field) {
                            // 使用更精确的选择器
                            var input;
                            if (field === 'aliyun_access_config_id') {
                                input = form.find('select[name="' + field + '"]');
                            } else {
                                input = form.find('input[name="' + field + '"]');
                            }
                            
                            if (input.length) {
                                if (field.indexOf('[') !== -1) {
                                    var matches = field.match(/([^\[]+)\[([^\]]*)\]/);
                                    if (matches) {
                                        if (!formData[matches[1]]) formData[matches[1]] = {};
                                        formData[matches[1]][matches[2]] = input.val();
                                    }
                                } else {
                                    formData[field] = input.val();
                                }
                            } else {
                                console.log('无法找到字段:', field);
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
                                modal.modal('hide');
                            }
                        });
                    });
                });
            });
        JS);

        return parent::render();
    }
}