<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">渠道管理</h3>
        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked channel-list">
            @foreach($channels as $channel)
            <li class="{{ $current_channel_id == $channel->id ? 'active' : '' }}">
                <a href="{{ $channel_base_url }}?channel_id={{ $channel->id }}">
                    @if($channel->is_default == 1)
                        <i class="fa fa-star text-yellow"></i> {{ $channel->channel_name }}
                    @else
                        <i class="fa fa-tag text-blue"></i> {{ $channel->channel_name }}
                        <span class="pull-right delete-channel" data-id="{{ $channel->id }}">
                           
                                <i class="fa fa-times text-red" title="删除渠道"></i>
                           
                        </span>
                    @endif
                </a>
            </li>
            @endforeach
        </ul>
        
        <!-- 添加渠道数量显示 -->
        <div class="channel-count" style="padding: 10px; border-top: 1px solid #f4f4f4; text-align: center; color: #777;">
            已添加 {{ $current_channel_count }} / {{ $max_channels }} 个渠道
        </div>
        
        <div class="add-channel-form" style="padding: 10px; border-top: 1px solid #f4f4f4;">
            <div class="input-group">
                <input type="text" class="form-control" id="new-channel-name" placeholder="输入新渠道名称" {{ $current_channel_count >= $max_channels ? 'disabled' : '' }}>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-success btn-flat" id="add-channel-btn" {{ $current_channel_count >= $max_channels ? 'disabled' : '' }}>
                        <i class="fa fa-plus"></i> 添加
                    </button>
                </span>
            </div>
            @if($current_channel_count >= $max_channels)
                <p class="text-danger" style="margin-top: 5px; font-size: 12px;">已达到最大渠道数量限制</p>
            @endif
        </div>
    </div>
    <input type="hidden" id="channel_base_url" value="{{ $channel_base_url }}">
    <input type="hidden" id="current_channel_id" value="{{ $current_channel_id }}">
    <input type="hidden" id="max_channels" value="{{ $max_channels }}"> 
    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
</div>

<style>
.channel-list li a {
    position: relative;
}
.delete-channel {
    opacity: 0.5;
    transition: opacity 0.2s;
}
.channel-list li:hover .delete-channel {
    opacity: 1;
}
#swal2-content {
    font-size: 14px;
    font-weight: bold;
    color: #f05050;
    margin-top: 5px;
}
</style>

<script>
(function() {
    var token = $('#csrf_token').val();
    var baseUrl = $('#channel_base_url').val();
    var max_channels = $('#max_channels').val();
    var current_channel_id = $('#current_channel_id').val();
    var confirm_text = '渠道删除后，该渠道下所有版本数据将同步删除且无法恢复，请谨慎操作';
    // 添加渠道
    $('#add-channel-btn').on('click', function() {
        if ($(this).prop('disabled')) {
            return;
        }
        
        var channelName = $('#new-channel-name').val();
        if (!channelName) {
            toastr.error('渠道名称不能为空');
            return;
        }
        
        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: {
                channel_name: channelName,
                _token: token
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.message);
                    
                    // 添加新渠道到列表
                    var newChannel = response.channel;
                    var newChannelHtml = '<li>' +
                        '<a href="' + baseUrl + '?channel_id=' + newChannel.id + '">' +
                        '<i class="fa fa-tag text-blue"></i> ' + newChannel.channel_name +
                        '<span class="pull-right delete-channel" data-id="' + newChannel.id + '">' +
                        '<i class="fa fa-times text-red" title="删除渠道"></i>' +
                        '</span></a></li>';
                    
                    $('.channel-list').append(newChannelHtml);
                    $('#new-channel-name').val('');
                    
                    // 更新渠道计数
                    var currentCount = parseInt($('.channel-count').text().split('/')[0].trim().split(' ').pop()) + 1;
                    $('.channel-count').html('已添加 ' + currentCount + ' / ' + max_channels + ' 个渠道');
                    
                    // 如果达到最大数量，禁用输入框和按钮
                    if (currentCount >= max_channels) {
                        $('#new-channel-name').prop('disabled', true);
                        $('#add-channel-btn').prop('disabled', true);
                        
                        if ($('.add-channel-form .text-danger').length === 0) {
                            $('.add-channel-form').append('<p class="text-danger" style="margin-top: 5px; font-size: 12px;">已达到最大渠道数量限制</p>');
                        }
                    }
                    
                    // 刷新页面以获取新渠道
                    setTimeout(function() {
                        window.location.href = baseUrl + '?channel_id=' + newChannel.id;
                    }, 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                var message = xhr.responseJSON ? (xhr.responseJSON.message || '添加失败') : '添加失败';
                toastr.error(message);
            }
        });
    });
    
    // 删除渠道（使用委托处理动态添加的元素）
    $(document).on('click', '.delete-channel', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // 如果有版本，不允许删除
        if ($(this).find('.fa-check').length > 0) {
            toastr.error('该渠道存在关联版本，无法删除');
            return;
        }
        
        var channelId = $(this).data('id');
        var $li = $(this).closest('li');
        
        Swal.fire({
            title: '确定要删除此渠道吗？',
            html: confirm_text,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: '确认删除',
            cancelButtonText: '取消'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: baseUrl + '/' + channelId,
                    type: 'DELETE',
                    data: {
                        _token: token
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $li.remove();
                            
                            // 更新渠道计数
                            var currentCount = parseInt($('.channel-count').text().split('/')[0].trim().split(' ').pop()) - 1;
                            $('.channel-count').html('已添加 ' + currentCount + ' / ' + max_channels + ' 个渠道');
                            
                            // 如果低于最大数量，启用输入框和按钮
                            if (currentCount < max_channels) {
                                $('#new-channel-name').prop('disabled', false);
                                $('#add-channel-btn').prop('disabled', false);
                                $('.add-channel-form .text-danger').remove();
                            }
                            
                            // 如果当前渠道被删除，跳转到默认渠道
                            if (current_channel_id == channelId) {
                                window.location.href = baseUrl;
                            }
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON ? (xhr.responseJSON.message || '删除失败') : '删除失败';
                        toastr.error(message);
                    }
                });
            }
        });
    });
    
    // 回车键添加渠道
    $('#new-channel-name').keypress(function(e) {
        if (e.which === 13 && !$(this).prop('disabled')) {
            $('#add-channel-btn').click();
            return false;
        }
    });
})();
</script>