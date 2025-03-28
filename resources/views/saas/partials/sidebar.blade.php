<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        @include('saas::form.select');

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
        
            @each('saas::partials.menu', $custom_menu['menu'], 'item')

        </ul>
        <!-- /.sidebar-menu -->
        <div class="user-panel">
            <a href="{{ admin_url('apps') }}" class="btn btn-success" style="width: 100%;">
                <i class="fa fa-plus"></i>
                <span class="hidden-xs">添加应用</span>
            </a>
        </div>
    </section>
    <!-- /.sidebar -->
</aside>