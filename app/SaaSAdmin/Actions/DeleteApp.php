<?php

namespace App\SaaSAdmin\Actions;

use App\Models\App;
use Encore\Admin\Admin;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Encore\Admin\Actions\RowAction;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class DeleteApp extends RowAction
{
    // 设置按钮图标
    public $icon = 'fa-trash';

    const SMS_CODE_EXPIRE_TIME = 60 * 5; // 5分钟
    const SMS_CODE_CACHE_KEY = 'delete_app_sms_code:{mobile}:{app_id}';

    static $scriptAdded = false;

    // 设置按钮文字
    public function name()
    {
        return '注销';
    }

    public function form()
    {
        $adminMobile = SaaSAdmin::user()->phone_number ?? '';
        $sendCodeUrl = admin_url('send-delete-app-code');
        $app_info = app(App::class)->getAppInfo($this->getKey());
        
        // 应用标识
        $this->text('app_key', '应用标识')
            ->readonly()
            ->default($app_info['name'] . '（' . $this->getKey() . '）')
            ->attribute(['style' => 'background-color: #f5f5f5; font-family: monospace;']);
        
        // 管理员手机号
        $this->text('mobile', '管理员手机号')
            ->readonly()
            ->default($adminMobile)
            ->attribute(['style' => 'background-color: #f5f5f5;']);
        
        // 短信验证码
        $this->text('sms_code', '短信验证码')
            ->required()
            ->rules('required|string|size:6')
            ->placeholder('请输入6位数字验证码')
            ->attribute(['style' => 'width: 200px; display: inline-block; margin: 10px;'])
            ->help('<small class="text-muted">验证码有效期5分钟，请及时输入</small>');
        
        // 隐藏字段
        $this->hidden('send_code_url')->default($sendCodeUrl);
        
        // 只在第一次加载时添加全局脚本
        $this->addGlobalScript();
    }

    // 处理删除逻辑
    public function handle(Model $model, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sms_code' => 'required|string|size:6',
            'mobile' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->response()->error($validator->errors()->first());
        }

        $adminMobile = SaaSAdmin::user()->phone_number ?? '';
        if (!$adminMobile) {
            return $this->response()->error('请先设置管理员手机号');
        }

        if ($adminMobile !== $request->input('mobile')) {
            return $this->response()->error('管理员手机号不匹配');
        }

        $mobile = $request->input('mobile');
        $appKey = $model->app_key;
        $inputCode = $request->input('sms_code');

        $cacheKey = str_replace(['{mobile}', '{app_id}'], [$mobile, $appKey], self::SMS_CODE_CACHE_KEY);
        $cachedCode = Cache::get($cacheKey);

        if (!$cachedCode) {
            return $this->response()->error('验证码已过期，请重新获取');
        }

        if ($cachedCode !== $inputCode) {
            return $this->response()->error('验证码错误');
        }

        try {
            $model->delete();
            Cache::forget($cacheKey);
            
            return $this->response()
                ->success('应用注销成功')
                ->refresh();
        } catch (\Exception $e) {
            return $this->response()->error('注销失败：' . $e->getMessage());
        }
    }
    
    /**
     * 添加全局脚本（只执行一次）
     */
    protected function addGlobalScript()
    {   
        if (self::$scriptAdded) {
            return;
        }
        
        self::$scriptAdded = true;
        
        // 使用 Admin::script 添加全局脚本
        Admin::html(<<<HTML
            <div class="alert alert-danger hide" id="app-delete-warning" style="margin: 0 0 20px 0;">
                <p style="margin: 10px 0; font-size: 14px;"><strong>注销应用将会清空所有关于此APP的相关数据且无法恢复，包括但不限于：</strong></p>
                <ul style="margin: 10px 0 10px 20px; font-size: 13px;">
                    <li>所有订单数据</li>
                    <li>所有用户数据</li>
                    <li>所有配置信息</li>
                    <li>所有统计数据</li>
                </ul>
                <div style="margin-top: 15px; padding: 8px 12px; background-color: #f0ad4e; border-radius: 3px; text-align: center;">
                    <strong style="color: #fff;"><i class="fa fa-warning" style="margin-right: 5px;"></i>此操作不可恢复！请谨慎操作！</strong>
                </div>
            </div>
        HTML
        );
        Admin::script($this->getGlobalScript());
    }

    /**
     * 获取全局脚本内容
     */
    protected function getGlobalScript()
    {
        return <<<SCRIPT
// 删除应用全局脚本 - 只初始化一次
$(function() {
    // 防止重复初始化
    if (window.deleteAppGlobalInitialized) {
        return;
    }
    window.deleteAppGlobalInitialized = true;
    
    // 监听模态框显示事件
    $(document).on('show.bs.modal', '.modal', function() {
        var modal = $(this);
        
        // 检查是否是删除应用的模态框
        if (modal.find('input[name="send_code_url"]').length > 0) {
            setupDeleteAppModal(modal);
        }
    });
    
    // 设置删除应用模态框
    function setupDeleteAppModal(modal) {
        // 设置模态框标题样式
        modal.find('.modal-title').html('<i class="fa fa-exclamation-triangle" style="color: #d9534f;"></i> 危险操作确认');
        modal.find('.modal-title').css('color', '#d9534f');

        // 移除已存在的警告信息
        modal.find('.app-delete-warning').remove();

        // 添加警告信息
        var warningHtml = $('#app-delete-warning').clone();
        $(warningHtml).removeAttr('id').removeClass('hide').addClass('app-delete-warning');
        
        modal.find('.modal-body').prepend(warningHtml);

        // 移除已存在的发送验证码按钮
        modal.find('.send-sms-code-btn').remove();
        
        // 添加发送验证码按钮
        modal.find('input[name="sms_code"]').after(
            '<button type="button" class="btn btn-info btn-sm send-sms-code-btn" style="margin-left: 10px; min-width: 100px;">' +
                '发送验证码' +
            '</button>'
        );
        
        // 调整确认按钮样式
        modal.find('.modal-footer .btn-primary').removeClass('btn-primary').addClass('btn-danger');
        modal.find('.modal-footer .btn-danger').html('<i class="fa fa-trash"></i> 确认注销');
        
        // 绑定发送验证码事件（使用事件委托避免重复绑定）
        modal.off('click.sendCode').on('click.sendCode', '.send-sms-code-btn', function() {
            var btn = $(this);
            var mobile = modal.find('input[name="mobile"]').val();
            var appKeyInput = modal.find('input[name="app_key"]').val();
            var sendCodeUrl = modal.find('input[name="send_code_url"]').val();
            
            // 从应用标识中提取app_key（去掉应用名称部分）
            var appKey = appKeyInput.match(/（(.+)）/);
            appKey = appKey ? appKey[1] : appKeyInput;
            
            if (!mobile) {
                toastr.error('请先设置管理员手机号');
                return;
            }
            
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> 发送中...');
            
            $.ajax({
                url: sendCodeUrl,
                type: 'POST',
                data: {
                    _token: LA.token,
                    mobile: mobile,
                    app_key: appKey
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success('验证码已发送到您的手机，请注意查收');
                        startCountdown(btn);
                    } else {
                        btn.prop('disabled', false).html('发送验证码');
                        toastr.error(response.message || '发送失败');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('发送验证码');
                    toastr.error('网络错误，请重试');
                }
            });
        });

        // 倒计时函数
        function startCountdown(btn) {
            var seconds = 60;
            var timer = setInterval(function() {
                btn.html('<i class="fa fa-clock-o"></i> ' + seconds + 's后重发');
                seconds--;
                if (seconds < 0) {
                    clearInterval(timer);
                    btn.html('发送验证码');
                    btn.prop('disabled', false);
                }
            }, 1000);
        }
        
        // 添加输入验证
        modal.off('input.smsCode').on('input.smsCode', 'input[name="sms_code"]', function() {
            var value = $(this).val();
            // 只允许输入数字
            value = value.replace(/[^0-9]/g, '');
            // 限制长度为6位
            if (value.length > 6) {
                value = value.substring(0, 6);
            }
            $(this).val(value);
            
            // 实时验证
            if (value.length === 6) {
                $(this).css('border-color', '#5cb85c');
            } else {
                $(this).css('border-color', '');
            }
        });
        
        // 自动聚焦到验证码输入框
        setTimeout(function() {
            modal.find('input[name="sms_code"]').focus();
        }, 500);
    }
});
SCRIPT;
    }

    /**
     * 发送删除应用验证码
     */
    public static function sendDeleteCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string',
            'app_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $adminMobile = SaaSAdmin::user()->phone_number ?? '';
        if (!$adminMobile) {
            return response()->json([
                'status' => false,
                'message' => '请先设置管理员手机号'
            ]);
        }

        if ($adminMobile !== $request->input('mobile')) {
            return response()->json([
                'status' => false,
                'message' => '管理员手机号不匹配'
            ]);
        }

        $mobile = $request->input('mobile');
        $appKey = $request->input('app_key');
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $cacheKey = str_replace(['{mobile}', '{app_id}'], [$mobile, $appKey], self::SMS_CODE_CACHE_KEY);
        Cache::put($cacheKey, $code, self::SMS_CODE_EXPIRE_TIME);

        try {
            app(SmsService::class)->sendVerifyCode($mobile, $code);
            return response()->json([
                'status' => true,
                'message' => '验证码已发送',
                'debug_code' => $code
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => '发送失败：' . $e->getMessage()
            ]);
        }
    }
} 