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
                            
                            // 获取套餐过期时间信息
                            $subscriptionExpire = Admin::user()->subscription_expires_at;
                            $expireInfo = '';
                            $expireColor = '';
                            $expireIcon = 'fa-clock-o';
                            
                            if ($subscriptionExpire) {
                                $expireDate = \Carbon\Carbon::parse($subscriptionExpire);
                                $now = \Carbon\Carbon::now();
                                if ($expireDate->isFuture()) {
                                    $daysLeft = $now->diffInDays($expireDate);
                                    if ($daysLeft <= 7) {
                                        $expireInfo = "即将到期 ({$daysLeft}天)";
                                        $expireColor = '#ff6b6b';
                                        $expireIcon = 'fa-exclamation-triangle';
                                    } else {
                                        $expireInfo = "剩余 {$daysLeft} 天";
                                        $expireColor = '#51cf66';
                                        $expireIcon = 'fa-check-circle';
                                    }
                                } else {
                                    $expireInfo = '已过期';
                                    $expireColor = '#ff6b6b';
                                    $expireIcon = 'fa-times-circle';
                                }
                            } else {
                                $expireInfo = '永久有效';
                                $expireColor = '#ffd43b';
                                $expireIcon = 'fa-star';
                            }
                        @endphp
                        <span class="hidden-xs" style="margin-left: 5px; line-height: normal;" title="当前版本: {{ $productName }}">{{ Admin::user()->name }}</span>
                        <span class="hidden-xs" style="background-color: {{ $color }}; color: white; font-size: 10px; padding: 1px 3px; border-radius: 2px; margin-left: 3px; vertical-align: baseline; line-height: 1;">{{ $productName }}</span>
                        <span class="hidden-xs" style="margin-left: 2px; font-size: 14px;">
                            <i class="fa fa-caret-down"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu" style="border-radius: 10px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); border: none; overflow: hidden; min-width: 260px;">
                        <!-- 用户信息区域 - 横向布局 -->
                        <li style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 18px 20px;">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 42px; height: 42px; border-radius: 50%; overflow: hidden; border: 2px solid rgba(255,255,255,0.3); margin-right: 14px; flex-shrink: 0;">
                                    <img src="{{ Admin::user()->avatar }}" alt="User Image" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="color: white; font-size: 15px; font-weight: 600; margin-bottom: 3px; line-height: 1.2;">
                                        {{ Admin::user()->name }}
                                    </div>
                                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 12px; line-height: 1;">
                                        ID: {{ Admin::user()->id }}
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                        <!-- 套餐信息区域 -->
                        <li style="background: #f8f9fa; padding: 16px 20px; border-bottom: 1px solid #e9ecef;">
                            <!-- 套餐版本 -->
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                                <span style="color: #6c757d; font-size: 13px; font-weight: 500;">当前套餐</span>
                                <span style="background-color: {{ $color }}; color: white; font-size: 12px; padding: 4px 10px; border-radius: 15px; font-weight: 500; line-height: 1;">
                                    {{ $productName }}
                                </span>
                            </div>
                            
                            <!-- 过期时间 -->
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="color: #6c757d; font-size: 13px; font-weight: 500;">有效期</span>
                                <div style="display: flex; align-items: center;">
                                    <i class="fa {{ $expireIcon }}" style="color: {{ $expireColor }}; font-size: 12px; margin-right: 5px;"></i>
                                    <span style="color: {{ $expireColor }}; font-size: 12px; font-weight: 600;">
                                        {{ $expireInfo }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($subscriptionExpire)
                            <div style="margin-top: 8px; text-align: right;">
                                <small style="color: #adb5bd; font-size: 11px;">
                                    到期: {{ \Carbon\Carbon::parse($subscriptionExpire)->format('Y-m-d H:i') }}
                                </small>
                            </div>
                            @endif
                        </li>
                        
                        <li class="user-footer" style="background: white; padding: 12px 20px;">
                            <div class="pull-left">
                                <a href="{{ admin_url('auth/setting') }}" class="btn btn-default btn-flat" style="border-radius: 6px; font-size: 12px; padding: 8px 16px; border: 1px solid #dee2e6;">{{ trans('admin.setting') }}</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ admin_url('auth/logout') }}" class="btn btn-default btn-flat" style="border-radius: 6px; font-size: 12px; padding: 8px 16px; border: 1px solid #dee2e6;">{{ trans('admin.logout') }}</a>
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