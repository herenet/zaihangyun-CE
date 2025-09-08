<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"> 用户反馈详情</h3>
        <div class="box-tools">
            <a href="{{ admin_url('app/manager/' . request('app_key') . '/feedback/list') }}" class="btn btn-sm btn-default" title="返回列表">
                <i class="fa fa-list"></i> 列表
            </a>
        </div>
    </div>
    
    <form id="feedback-reply-form" class="form-horizontal">
        {{ csrf_field() }}
        
        <div class="box-body">
            <div class="fields-group">
                <div class="form-group">
                    <label class="col-sm-2 control-label">反馈类型</label>
                    <div class="col-sm-8">
                        <div class="form-control-static">
                            <span class="badge bg-{{ $feedback->type == 1 ? 'blue' : ($feedback->type == 2 ? 'yellow' : 'gray') }}" style="font-size: 14px;">
                                {{ \App\Models\Feedback::$type[$feedback->type] }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">反馈内容</label>
                    <div class="col-sm-8">
                        <div class="well well-sm" style="background-color: #f9f9f9; margin-bottom: 0;">
                            <pre style="border: none; background: transparent; margin: 0; white-space: pre-wrap;">{{ $feedback->content }}</pre>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">联系方式</label>
                    <div class="col-sm-8">
                        <div class="form-control-static">{{ $feedback->contact ?: '未提供' }}</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">提交时间</label>
                    <div class="col-sm-8">
                        <div class="form-control-static">{{ $feedback->created_at }}</div>
                    </div>
                </div>
                
                <hr style="margin-top: 20px; margin-bottom: 20px;">
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">回复内容</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" name="reply" rows="5" placeholder="请输入回复内容...">{{ $feedback->reply }}</textarea>
                        <span class="help-block">
                            <i class="fa fa-info-circle"></i> 请注意回复内容不要超过200个字符
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="box-footer">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="btn-group pull-right">
                    <button type="button" id="submit-btn" class="btn btn-primary">保存回复</button>
                </div>
                <div class="btn-group pull-left">
                    <a href="{{ admin_url('app/manager/' . request('app_key') . '/feedback/list') }}" class="btn btn-default">取消</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(function() {
    // 自动聚焦到回复文本框
    $('textarea[name="reply"]').focus();
    
    // 提交按钮点击事件
    $('#submit-btn').on('click', function() {
    var btn = $(this);
    var reply = $('textarea[name="reply"]').val();
    
    // 禁用按钮，显示加载状态，但保留原始内容结构
    btn.prop('disabled', true)
       .find('i').removeClass('fa-check').addClass('fa-spinner fa-spin');
    
    // 发送AJAX请求
    $.ajax({
        url: "{{ admin_url('app/manager/' . request('app_key') . '/feedback/' . $feedback->id . '/save-reply') }}",
        type: 'POST',
        data: {
            _token: $('input[name="_token"]').val(),
            reply: reply
        },
        success: function(response) {
            if (response.status) {
                // 成功提示
                toastr.success('回复已保存');
                
                // 更新按钮状态为成功
                btn.removeClass('btn-primary').attr('disabled', true)
                   .find('i').removeClass('fa-spinner fa-spin').addClass('fa-check');
                
                // 延迟返回列表页
                setTimeout(function() {
                    window.location.href = "{{ admin_url('app/manager/' . request('app_key') . '/feedback/list') }}";
                }, 1000);
            } else {
                // 失败提示
                toastr.error(response.message || '保存失败');
                
                // 恢复按钮状态
                btn.prop('disabled', false)
                   .find('i').removeClass('fa-spinner fa-spin').addClass('fa-check');
            }
        },
        error: function(xhr) {
            // 错误处理
            var message = xhr.responseJSON ? (xhr.responseJSON.message || '系统错误') : '网络错误';
            toastr.error(message);
            
            // 恢复按钮状态
            btn.prop('disabled', false)
               .find('i').removeClass('fa-spinner fa-spin').addClass('fa-check');
        }
    });
});
});
</script>