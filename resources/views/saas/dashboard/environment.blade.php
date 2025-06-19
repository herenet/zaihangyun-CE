<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">APP信息</h3>

        <div class="box-tools pull-right">
            
        </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped">

                @foreach($envs as $env)
                <tr>
                    <td width="120px">{{ $env['name'] }}</td>
                    <td class="text-muted {{ $env['name'] == 'AppSecret' ? 'app-secret' : '' }}">
                        <span class="secret-content">{{ $env['name'] == 'AppSecret' ? str_repeat('*', strlen($env['value'])) : $env['value'] }}</span>
                        <span class="original-content" style="display:none;">{{ $env['value'] }}</span>
                        @if($env['name'] == 'AppSecret')
                            <i class="fa fa-eye-slash toggle-secret" style="cursor:pointer; margin-left:10px;"></i>
                        @endif
                        @if($env['name'] == '今日API调用')
                            <span style="color: #5cb85c; font-size: 12px; margin-left: 8px;">
                                <i class="fa fa-clock-o"></i> 实时统计
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 使用事件委托
    document.addEventListener('click', function(event) {
        // 检查点击的是否是切换按钮
        if (event.target.classList.contains('toggle-secret')) {
            var td = event.target.parentNode;
            var secretContent = td.querySelector('.secret-content');
            var originalContent = td.querySelector('.original-content');
            
            // 切换显示状态
            if (secretContent.style.display !== 'none') {
                secretContent.style.display = 'none';
                originalContent.style.display = 'inline';
                event.target.classList.remove('fa-eye-slash');
                event.target.classList.add('fa-eye');
            } else {
                secretContent.style.display = 'inline';
                originalContent.style.display = 'none';
                event.target.classList.remove('fa-eye');
                event.target.classList.add('fa-eye-slash');
            }
        }
    });
});
</script>