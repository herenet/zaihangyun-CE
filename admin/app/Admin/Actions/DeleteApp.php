<?php

namespace App\Admin\Actions;

use App\Models\App;
use Encore\Admin\Admin;
use Illuminate\Http\Request;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

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
        $app_info = app(App::class)->getAppInfo($this->getKey());
        
        // 应用标识
        $this->text('app_key', '应用标识')
            ->readonly()
            ->default($app_info['name'] . '（' . $this->getKey() . '）')
            ->attribute(['style' => 'background-color: #f5f5f5; font-family: monospace;']);

        // 只在第一次加载时添加全局脚本
        $this->addGlobalScript();
    }

    // 处理删除逻辑
    public function handle(Model $model, Request $request)
    {
        try {
            $model->delete();
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
        if (modal.find('input[name="app_key"]').length > 0) {
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
        
        // 调整确认按钮样式
        modal.find('.modal-footer .btn-primary').removeClass('btn-primary').addClass('btn-danger');
        modal.find('.modal-footer .btn-danger').html('<i class="fa fa-trash"></i> 确认注销');
    }
});
SCRIPT;
    }
} 