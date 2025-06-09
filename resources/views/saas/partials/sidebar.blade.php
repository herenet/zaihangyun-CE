<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            {!! $appSelector ?? '' !!}
            @each('saas::partials.menu', $custom_menu['menu'], 'item')
        </ul>
        <!-- /.sidebar-menu -->
        <div class="sidebar-actions">
            <a href="{{ admin_url('apps') }}" class="btn btn-success" style="width: 100%; margin-bottom: 15px; color: #fff; padding: 10px 16px;">
                <i class="fa fa-plus"></i>
                <span>添加应用</span>
            </a>
            <a href="{{ admin_url('manager') }}" class="btn btn-default" style="width: 100%; padding: 10px 16px;">
                <i class="fa fa-cog"></i>
                <span>管理应用</span>
            </a>
        </div>
    </section>
    <!-- /.sidebar -->
</aside>

<style>
/* === 侧边栏应用操作按钮区域优化 === */
.skin-blue-light .sidebar-app-actions {
    padding: 20px 16px 24px;
    margin-top: 20px;
    border-top: 1px solid var(--gray-200);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, transparent 100%);
}

.skin-blue-light .sidebar-action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 12px 16px;
    margin-bottom: 12px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
}

.skin-blue-light .sidebar-action-btn:last-child {
    margin-bottom: 0;
}

.skin-blue-light .sidebar-action-btn i {
    margin-right: 8px;
    font-size: 14px;
    width: 16px;
    text-align: center;
}

/* 主要按钮样式 - 添加应用 */
.skin-blue-light .sidebar-action-btn--primary {
    background: var(--success-gradient);
    color: var(--white);
    box-shadow: 0 3px 8px rgba(16, 185, 129, 0.3);
}

.skin-blue-light .sidebar-action-btn--primary:hover {
    background: linear-gradient(135deg, #0D9488 0%, #10B981 100%);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
    text-decoration: none;
}

.skin-blue-light .sidebar-action-btn--primary:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
}

/* 次要按钮样式 - 管理应用 */
.skin-blue-light .sidebar-action-btn--secondary {
    background: var(--white);
    color: var(--text-dark);
    border: 1px solid var(--gray-300);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
}

.skin-blue-light .sidebar-action-btn--secondary:hover {
    background: var(--gray-50);
    color: var(--primary-color);
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(64, 134, 245, 0.15);
    text-decoration: none;
}

.skin-blue-light .sidebar-action-btn--secondary:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

/* 按钮光泽效果 */
.skin-blue-light .sidebar-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.skin-blue-light .sidebar-action-btn:hover::before {
    left: 100%;
}

/* 响应式优化 */
@media (max-width: 767px) {
    .skin-blue-light .sidebar-app-actions {
        padding: 16px 12px 20px;
        margin-top: 16px;
    }
    
    .skin-blue-light .sidebar-action-btn {
        padding: 10px 14px;
        font-size: 13px;
        margin-bottom: 10px;
    }
    
    .skin-blue-light .sidebar-action-btn i {
        margin-right: 6px;
        font-size: 13px;
    }
}

/* 确保在侧边栏收起时的样式 */
.skin-blue-light.sidebar-mini.sidebar-collapse .sidebar-app-actions {
    padding: 16px 8px;
}

.skin-blue-light.sidebar-mini.sidebar-collapse .sidebar-action-btn {
    padding: 10px 8px;
    font-size: 0;
}

.skin-blue-light.sidebar-mini.sidebar-collapse .sidebar-action-btn i {
    margin-right: 0;
    font-size: 16px;
}

.skin-blue-light.sidebar-mini.sidebar-collapse .sidebar-action-btn span {
    display: none;
}
</style>