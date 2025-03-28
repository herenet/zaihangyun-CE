<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        @include('saas::form.select');

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            @each('manager::partials.menu', $custom_menu['menu'], 'item')
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>