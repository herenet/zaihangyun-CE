<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            {!! $appSelector ?? '' !!}
            @each('saas::partials.menu', $custom_menu['menu'], 'item')
        </ul>
        <!-- /.sidebar-menu -->
        <div style="margin: 10px;">
            <a href="{{ admin_url('apps') }}" class="btn btn-success" style="width: 100%; margin-bottom: 10px;color: #fff;">
                <i class="fa fa-plus"></i>
                <span>添加应用</span>
            </a>
            <a href="{{ admin_url('manager') }}" class="btn btn-default" style="width: 100%;">
                <i class="fa fa-cog"></i>
                <span>管理应用</span>
            </a>
        </div>
    </section>
    <!-- /.sidebar -->
</aside>