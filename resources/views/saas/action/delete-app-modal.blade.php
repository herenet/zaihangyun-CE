<div class="modal fade delete-app-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="color: #d9534f;">
                    <i class="fa fa-exclamation-triangle"></i> 危险操作确认
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>警告：</strong>注销应用将会清空所有关于此APP的相关数据且无法恢复，包括但不限于：
                    <ul style="margin-top: 10px; margin-bottom: 0;">
                        <li>所有订单数据</li>
                        <li>所有用户数据</li>
                        <li>所有配置信息</li>
                        <li>所有统计数据</li>
                    </ul>
                    <strong style="color: #d9534f;">此操作不可恢复！</strong>
                </div>
                <div class="form-group">
                    <label>应用标识：</label>
                    <input type="text" class="form-control app-key-input" readonly style="background-color: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>管理员手机号：</label>
                    <input type="text" class="form-control admin-mobile" value="{{ $adminMobile }}" readonly style="background-color: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>短信验证码：</label>
                    <div class="input-group">
                        <input type="text" class="form-control sms-code" placeholder="请输入6位验证码" maxlength="6">
                        <span class="input-group-btn">
                            <button class="btn btn-default send-code-btn" type="button">发送验证码</button>
                        </span>
                    </div>
                    <small class="text-muted">验证码有效期5分钟</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-danger confirm-delete-btn">确认注销</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var sendCodeUrl = @json($sendCodeUrl);
    var handleUrl = @json($handleUrl);
    var elementClass = @json($elementClass);
    var adminMobile = @json($adminMobile);
    
    console.log('DeleteApp Modal Script Loaded');
    console.log('Element Class:', elementClass);
    console.log('Send Code URL:', sendCodeUrl);
    console.log('Handle URL:', handleUrl);
    console.log('Admin Mobile:', adminMobile);
    
    // 使用更安全的事件绑定方式
    $(document).off('click', '.' + elementClass).on('click', '.' + elementClass, function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Delete button clicked');
        
        var $this = $(this);
        var key = $this.data('_key');
        
        console.log('Button element:', $this);
        console.log('Key from data-_key:', key);
        
        // 如果没有获取到 key，尝试其他方法
        if (!key) {
            // 尝试从 href 中获取
            var href = $this.attr('href');
            if (href && href !== 'javascript:void(0);') {
                var matches = href.match(/\/([^\/]+)$/);
                if (matches) {
                    key = matches[1];
                }
            }
        }
        
        // 如果还是没有，从表格行中获取
        if (!key) {
            var $row = $this.closest('tr');
            // 根据你的表格结构调整，这里假设第二列是 app_key
            key = $row.find('td:eq(1)').text().trim();
        }
        
        console.log('Final key:', key);
        
        if (!key) {
            toastr.error('无法获取应用标识');
            console.error('Unable to get app key');
            return false;
        }
        
        // 创建唯一的模态框 ID
        var modalId = 'delete-app-modal-' + Math.random().toString(36).substr(2, 9);
        
        // 创建模态框 HTML
        var modalHtml = '<div class="modal fade" id="' + modalId + '" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog" role="document">' +
                '<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<h4 class="modal-title" style="color: #d9534f;">' +
                            '<i class="fa fa-exclamation-triangle"></i> 危险操作确认' +
                        '</h4>' +
                        '<button type="button" class="close" data-dismiss="modal">' +
                            '<span>&times;</span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="modal-body">' +
                        '<div class="alert alert-danger">' +
                            '<strong>警告：</strong>注销应用将会清空所有关于此APP的相关数据且无法恢复，包括但不限于：' +
                            '<ul style="margin-top: 10px; margin-bottom: 0;">' +
                                '<li>所有订单数据</li>' +
                                '<li>所有用户数据</li>' +
                                '<li>所有配置信息</li>' +
                                '<li>所有统计数据</li>' +
                            '</ul>' +
                            '<strong style="color: #d9534f;">此操作不可恢复！</strong>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label>应用标识：</label>' +
                            '<input type="text" class="form-control app-key-input" value="' + key + '" readonly style="background-color: #f5f5f5;">' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label>管理员手机号：</label>' +
                            '<input type="text" class="form-control admin-mobile" value="' + adminMobile + '" readonly style="background-color: #f5f5f5;">' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label>短信验证码：</label>' +
                            '<div class="input-group">' +
                                '<input type="text" class="form-control sms-code" placeholder="请输入6位验证码" maxlength="6">' +
                                '<span class="input-group-btn">' +
                                    '<button class="btn btn-default send-code-btn" type="button">发送验证码</button>' +
                                '</span>' +
                            '</div>' +
                            '<small class="text-muted">验证码有效期5分钟</small>' +
                        '</div>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                        '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
                        '<button type="button" class="btn btn-danger confirm-delete-btn">确认注销</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        // 移除已存在的模态框
        $('[id^="delete-app-modal-"]').remove();
        
        // 添加到页面
        $('body').append(modalHtml);
        
        var $modal = $('#' + modalId);
        
        console.log('Modal created:', $modal.length);
        
        // 显示模态框
        $modal.modal('show');
        
        // 发送验证码事件
        $modal.find('.send-code-btn').on('click', function() {
            var btn = $(this);
            var mobile = $modal.find('.admin-mobile').val();
            
            if (!mobile) {
                toastr.error('请先设置管理员手机号');
                return;
            }
            
            btn.prop('disabled', true).text('发送中...');
            
            $.ajax({
                method: 'post',
                url: sendCodeUrl,
                data: {
                    mobile: mobile,
                    app_key: key,
                    _token: LA.token
                },
                success: function(data) {
                    if (data.status) {
                        toastr.success('验证码已发送');
                        var countdown = 60;
                        var timer = setInterval(function() {
                            countdown--;
                            btn.text(countdown + 's后重发');
                            if (countdown <= 0) {
                                clearInterval(timer);
                                btn.prop('disabled', false).text('发送验证码');
                            }
                        }, 1000);
                    } else {
                        btn.prop('disabled', false).text('发送验证码');
                        toastr.error(data.message || '发送失败');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).text('发送验证码');
                    toastr.error('网络错误，请重试');
                }
            });
        });
        
        // 确认删除事件
        $modal.find('.confirm-delete-btn').on('click', function() {
            var mobile = $modal.find('.admin-mobile').val();
            var smsCode = $modal.find('.sms-code').val();
            
            if (!smsCode) {
                toastr.error('请输入验证码');
                return;
            }
            
            if (smsCode.length !== 6) {
                toastr.error('验证码必须是6位数字');
                return;
            }
            
            var btn = $(this);
            btn.prop('disabled', true).text('注销中...');
            
            $.ajax({
                method: 'post',
                url: handleUrl,
                data: {
                    _key: key,
                    _action: 'App_Admin_Actions_DeleteApp',
                    _model: 'App_Models_App',
                    mobile: mobile,
                    sms_code: smsCode,
                    _token: LA.token
                },
                success: function (data) {
                    if (typeof data === 'object') {
                        if (data.status) {
                            $modal.modal('hide');
                            swal({
                                title: '注销成功',
                                text: data.message,
                                type: 'success'
                            }, function() {
                                $.pjax.reload('#pjax-container');
                            });
                        } else {
                            btn.prop('disabled', false).text('确认注销');
                            toastr.error(data.message);
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('确认注销');
                    toastr.error('网络错误，请重试');
                }
            });
        });
        
        return false;
    });
    
    // 页面加载完成后检查按钮是否存在
    $(document).ready(function() {
        console.log('Document ready, checking for delete buttons');
        console.log('Found buttons:', $('.' + elementClass).length);
    });
    
})();
</script>