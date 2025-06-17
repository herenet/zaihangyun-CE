<!-- 导航栏 -->
<nav class="navbar">
    <div class="nav-container">
        <div class="logo">
            <img src="/images/logo-baas.png" alt="在行云" style="height: 42px;">
        </div>
        <div class="nav-links">
            <a href="/" class="nav-link{{ request()->is('/') ? ' active' : '' }}">首页</a>
            <a href="/pricing" class="nav-link{{ request()->is('pricing') ? ' active' : '' }}">价格</a>
            <a href="https://github.com/zaihangyun" target="_blank" class="nav-link">社区</a>
            <a href="/about" class="nav-link{{ request()->is('about') ? ' active' : '' }}">关于我们</a>
            <a href="/login" class="cta-button" style="background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); color: white; padding: 8px 20px; border-radius: 20px; font-weight: 600; transition: all 0.3s ease;">注册登录</a>
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
            <a href="https://github.com/zaihangyun" target="_blank" class="nav-link">社区</a>
            <a href="/about" class="nav-link{{ request()->is('about') ? ' active' : '' }}">关于我们</a>
            <a href="/login" class="cta-button" style="background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); color: white; padding: 12px 24px; border-radius: 20px; font-weight: 600; text-align: center; margin-top: 10px;">注册登录</a>
        </div>
    </div>
</nav> 