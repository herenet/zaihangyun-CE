<!-- 导航栏 -->
<nav class="navbar">
    <div class="nav-container">
        <div class="logo">
            <a class="nav-link" style="display:inline-flex" href="/"><img src="/images/logo-baas.png" alt="在行云" style="height: 42px;"></a>
        </div>
        <div class="nav-links">
            <a href="/" class="nav-link{{ request()->is('/') ? ' active' : '' }}">首页</a>
            <a href="/pricing" class="nav-link{{ request()->is('pricing') ? ' active' : '' }}">价格</a>
            <a href="/about" class="nav-link{{ request()->is('about') ? ' active' : '' }}">关于我们</a>
            <a href="https://github.com/herenet/zaihangyun/discussions" target="_blank" class="nav-link">社区</a>
            @auth('admin')
                <a href="/console" class="cta-button" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; padding: 8px 20px; border-radius: 20px; font-weight: 600; transition: all 0.3s ease; border: 1px solid #27ae60;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 6px; vertical-align: -2px;">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    控制台
                </a>
            @else
                <a href="/console/auth/login" class="cta-button" style="background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); color: white; padding: 8px 20px; border-radius: 20px; font-weight: 600; transition: all 0.3s ease;">注册登录</a>
            @endauth
        </div>
        <!-- 移动端汉堡菜单按钮 -->
        <div class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <!-- 移动端菜单 -->
    <div class="mobile-menu" id="mobile-menu">
        <div class="mobile-nav-links">
            <a href="/" class="nav-link{{ request()->is('/') ? ' active' : '' }}">首页</a>
            <a href="/pricing" class="nav-link{{ request()->is('pricing') ? ' active' : '' }}">价格</a>
            <a href="/about" class="nav-link{{ request()->is('about') ? ' active' : '' }}">关于我们</a>
            <a href="https://github.com/herenet/zaihangyun/discussions" target="_blank" class="nav-link">社区</a>
            @auth('admin')
                <a href="/console" class="cta-button" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; padding: 12px 24px; border-radius: 20px; font-weight: 600; text-align: center; margin-top: 10px; border: 1px solid #27ae60;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px; vertical-align: -2px;">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    控制台
                </a>
            @else
                <a href="/console/auth/login" class="cta-button" style="background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); color: white; padding: 12px 24px; border-radius: 20px; font-weight: 600; text-align: center; margin-top: 10px;">注册登录</a>
            @endauth
        </div>
    </div>
</nav> 