<!-- Main Header -->
<header class="main-header" style="height: 60px !important;">

    <!-- Logo -->
    <a href="javascript:void(0)" class="logo" style="height: 60px !important; line-height: 60px !important;" onclick="window.location.href = '{{ admin_url('/') }}'">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">
            <img src="/images/logo-mini.png" style="height: 20px !important;">
        </span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            <img src="/images/logo-baas.png" style="height: 36px !important;">
        </span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" style="min-height: 60px !important;" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" style="padding: 20px 18px;" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <ul class="nav navbar-nav hidden-sm visible-lg-block">
        {!! Admin::getNavbar()->render('left') !!}
        </ul>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                {!! Admin::getNavbar()->render() !!}

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ Admin::user()->avatar }}" class="user-image" alt="User Image" style="margin-right:5px">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        @php
                            $product = Admin::user()->product ?? 'free';
                            $productColors = [
                                'free' => '#95a5a6',     // 灰色
                                'basic' => '#3498db',    // 蓝色
                                'adv' => '#27ae60',      // 绿色
                                'pro' => '#f39c12',      // 橙色
                                'company' => '#8e44ad'   // 紫色
                            ];
                            $color = $productColors[$product] ?? '#95a5a6';
                            $productName = config('product.' . $product . '.name', '未知版本');
                        @endphp
                        <span class="hidden-xs" style="margin-left: 5px; line-height: normal;" title="当前版本: {{ $productName }}">{{ Admin::user()->name }}</span>
                        <span class="hidden-xs" style="background-color: {{ $color }}; color: white; font-size: 10px; padding: 1px 3px; border-radius: 2px; margin-left: 3px; vertical-align: baseline; line-height: 1;">{{ $productName }}</span>
                        <span class="hidden-xs" style="margin-left: 2px; font-size: 14px;">
                            <i class="fa fa-caret-down"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ Admin::user()->avatar }}" class="img-circle" alt="User Image">

                            <p>
                                {{ Admin::user()->name }}
                                <small>ID: {{ Admin::user()->id }}</small>
                            </p>
                            <div style="text-align: center; margin-top: 8px;">
                                <span class="label" style="background-color: {{ $color }}; color: white; font-size: 10px; padding: 3px 8px; border-radius: 3px;">
                                    {{ $productName }}
                                </span>
                            </div>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ admin_url('auth/setting') }}" class="btn btn-default btn-flat">{{ trans('admin.setting') }}</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ admin_url('auth/logout') }}" class="btn btn-default btn-flat">{{ trans('admin.logout') }}</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                {{--<li>--}}
                    {{--<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>--}}
                {{--</li>--}}
            </ul>
        </div>
    </nav>
</header>