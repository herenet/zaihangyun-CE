<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">功能模块列表</h3>
        <div class="box-tools pull-right">
            <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
        </div>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>模块名称</th>
                        <th>功能描述</th>
                        <th class="text-center">状态</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div style="width: 30px; height: 30px; border-radius: 4px; background-color: {{ $module['color'] }}; display: flex; justify-content: center; align-items: center; margin-right: 10px;">
                                    <i class="fa fa-{{ $module['icon'] }}" style="color: white;"></i>
                                </div>
                                <span>{{ $module['name'] }}</span>
                            </div>
                        </td>
                        <td>{{ $module['description'] }}</td>
                        <td class="text-center">
                            @if($module['enabled'])
                                <span class="label label-success" style="font-size: 12px;">已开通</span>
                            @else
                            <a href="{{ $module['url'] }}" class="btn btn-xs btn-primary" style="margin-left: 5px;font-size: 12px;">
                                        <i class="fa fa-plug"></i> 立即开通
                                    </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>