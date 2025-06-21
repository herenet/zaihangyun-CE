<!-- 引入Chart.js -->
<script src="{{ asset('js/chart.min.js') }}"></script>

<div class="api-stats-dashboard">
    @if(isset($error))
    <!-- 错误信息 -->
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><i class="icon fa fa-ban"></i> 数据加载失败</h4>
                {{ $error }}
            </div>
        </div>
    </div>
    @endif

    @if(isset($data))
    <!-- 页面头部和实时数据 -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid" style="border-top: 3px solid #00a65a;">
                <div class="box-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="box-title" style="margin-top: 8px; font-size: 20px;">
                                <i class="fa fa-dashboard" style="color: #00a65a;"></i> 
                                API调用统计中心
                                <span class="label label-success" style="margin-left: 10px; font-size: 11px;">实时监控</span>
                            </h3>
                            <p class="text-muted" style="margin: 5px 0 0 0; font-size: 14px;">
                                <span id="last-update-time"><i class="fa fa-clock-o"></i> 最后更新: {{ date('Y-m-d H:i:s') }}</span>
                                <span style="margin-left: 15px;" id="refresh-status"><i class="fa fa-refresh"></i> {{ $data['refresh_interval'] }}秒后自动刷新</span>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" class="btn btn-success btn-sm" id="manual-refresh" style="margin-top: 10px;">
                                <i class="fa fa-refresh"></i> 立即刷新
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API调用使用情况 -->
    <div class="row">
        <div class="col-md-12">
            <div class="api-usage-card">
                <div class="usage-header">
                    <div class="usage-stats">
                        <div class="main-stat">
                            <h2>{{ number_format($data['total_calls']) }}</h2>
                            <span class="stat-label">今日API调用</span>
                        </div>
                        <div class="sub-stats">
                            <div class="sub-stat">
                                <span class="value">
                                    @if($data['is_unlimited'] ?? false)
                                        <i class="fa fa-infinity text-success"></i> 无限制
                                    @else
                                        {{ number_format($data['limit']) }}
                                    @endif
                                </span>
                                <span class="label">调用限额</span>
                            </div>
                            <div class="sub-stat">
                                <span class="value">
                                    @if($data['is_unlimited'] ?? false)
                                        <i class="fa fa-infinity text-success"></i> 无限制
                                    @else
                                        {{ number_format($data['limit'] - $data['total_calls']) }}
                                    @endif
                                </span>
                                <span class="label">剩余次数</span>
                            </div>
                            <div class="sub-stat">
                                <span class="value">
                                    @if($data['is_unlimited'] ?? false)
                                        <i class="fa fa-check-circle text-success"></i> 畅享
                                    @else
                                        {{ $data['usage_percentage'] }}%
                                    @endif
                                </span>
                                <span class="label">使用率</span>
                            </div>
                        </div>
                    </div>
                    <div class="usage-icon">
                        <i class="fa fa-line-chart"></i>
                    </div>
                </div>
                
                <!-- 使用率进度条 -->
                <div class="usage-progress-container">
                    <div class="progress-info">
                        <span class="progress-label">使用进度</span>
                        <span class="progress-percentage">
                            @if($data['is_unlimited'] ?? false)
                                <i class="fa fa-infinity text-success"></i> 畅享无限
                            @else
                                {{ $data['usage_percentage'] }}%
                            @endif
                        </span>
                    </div>
                    <div class="usage-progress-bar">
                        <div class="progress-track"></div>
                        @php
                            $isUnlimited = $data['is_unlimited'] ?? false;
                            $percentage = $data['usage_percentage'];
                            
                            if ($isUnlimited) {
                                $statusClass = 'unlimited';
                                $displayWidth = 100;
                            } else {
                                $displayWidth = min($percentage, 100);
                                $statusClass = 'normal';
                                
                                if ($percentage >= 100) {
                                    $statusClass = 'exceeded';
                                } elseif ($percentage >= 80) {
                                    $statusClass = 'danger';
                                } elseif ($percentage >= 60) {
                                    $statusClass = 'warning';
                                }
                            }
                        @endphp
                        <div class="progress-fill {{ $statusClass }}" 
                             data-percentage="{{ $percentage }}"
                             data-display-width="{{ $displayWidth }}"
                             data-unlimited="{{ $isUnlimited ? 'true' : 'false' }}"
                             style="width: 0%"></div>
                        <div class="progress-glow {{ $statusClass }}"></div>
                    </div>
                    <div class="progress-markers">
                        <span class="marker start">0</span>
                        @if($data['is_unlimited'] ?? false)
                            <span class="marker quarter">畅享</span>
                            <span class="marker half">无限</span>
                            <span class="marker three-quarter">调用</span>
                            <span class="marker end"><i class="fa fa-infinity"></i></span>
                        @else
                            <span class="marker quarter">25%</span>
                            <span class="marker half">50%</span>
                            <span class="marker three-quarter">75%</span>
                            <span class="marker end">{{ number_format($data['limit']) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 图表区域 -->
    <div class="row">
        <!-- 趋势线图 -->
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-line-chart"></i> API调用趋势
                        <small>{{ $data['trend_data']['summary']['range_description'] }}</small>
                    </h3>

                </div>
                <div class="box-body">
                    <canvas id="trend-chart" style="height: 300px;"></canvas>
                </div>
                <div class="box-footer">
                    <div class="row text-center">
                        <div class="col-sm-3">
                            <div class="description-block border-right">
                                <span class="description-percentage text-green">
                                    <i class="fa fa-caret-up"></i> 
                                    {{ $data['trend_data']['summary']['max_day']['total_calls'] ?? 0 }}
                                </span>
                                <h5 class="description-header">峰值</h5>
                                <span class="description-text">
                                    {{ isset($data['trend_data']['summary']['max_day']) ? date('m/d', strtotime($data['trend_data']['summary']['max_day']['date'])) : '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="description-block border-right">
                                <span class="description-percentage text-blue">
                                    <i class="fa fa-caret-down"></i> 
                                    {{ $data['trend_data']['summary']['min_day']['total_calls'] ?? 0 }}
                                </span>
                                <h5 class="description-header">最低</h5>
                                <span class="description-text">
                                    {{ isset($data['trend_data']['summary']['min_day']) ? date('m/d', strtotime($data['trend_data']['summary']['min_day']['date'])) : '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="description-block border-right">
                                <span class="description-percentage text-yellow">
                                    <i class="fa fa-calendar"></i> 
                                    {{ $data['trend_data']['summary']['total_calls'] }}
                                </span>
                                <h5 class="description-header">总计</h5>
                                <span class="description-text">{{ $data['trend_data']['summary']['total_days'] }}天累计</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="description-block">
                                <span class="description-percentage text-red">
                                    <i class="fa fa-line-chart"></i> 
                                    {{ $data['trend_data']['summary']['avg_calls'] }}
                                </span>
                                <h5 class="description-header">日均</h5>
                                <span class="description-text">平均调用</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 饼图：应用占比 -->
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-pie-chart"></i> 应用调用占比
                    </h3>
                </div>
                <div class="box-body">
                    @if($data['total_calls'] > 0)
                        <canvas id="pie-chart" style="height: 300px;"></canvas>
                    @else
                        <div class="text-center" style="padding: 80px 20px;">
                            <i class="fa fa-pie-chart fa-3x text-muted"></i>
                            <h4 class="text-muted">暂无调用数据</h4>
                            <p class="text-muted">今日还没有API调用</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    @else
    <!-- 数据加载中 -->
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body text-center" style="padding: 100px 20px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                    <h3 class="text-muted" style="margin-top: 20px;">数据加载中...</h3>
                    <p class="text-muted">正在获取API统计数据，请稍候</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.api-stats-dashboard .small-box {
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: none;
    margin-bottom: 20px;
    overflow: hidden;
}

.api-stats-dashboard .small-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.api-stats-dashboard .small-box .inner {
    padding: 15px;
}

.api-stats-dashboard .small-box h3 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: white;
}

.api-stats-dashboard .small-box p {
    font-size: 14px;
    color: rgba(255,255,255,0.9);
    margin: 0 0 10px 0;
    font-weight: 500;
}

.api-stats-dashboard .progress-group {
    margin-top: 10px;
}

.api-stats-dashboard .progress-text {
    font-size: 11px;
    color: rgba(255,255,255,0.8);
}

.api-stats-dashboard .float-right {
    float: right;
    font-size: 11px;
    color: white;
}

.api-stats-dashboard .progress {
    height: 4px;
    border-radius: 2px;
    background-color: rgba(255,255,255,0.2);
    margin: 3px 0 0 0;
    overflow: hidden;
}

.api-stats-dashboard .progress-bar {
    height: 100%;
    border-radius: 2px;
    transition: width 0.6s ease;
}

.api-stats-dashboard .small-box .icon {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 0;
    font-size: 70px;
    color: rgba(255,255,255,0.15);
}

.api-stats-dashboard .small-box-footer {
    position: relative;
    text-align: center;
    padding: 8px 0;
    color: rgba(255,255,255,0.8);
    background-color: rgba(0,0,0,0.1);
    text-decoration: none;
    z-index: 10;
    font-size: 12px;
}

.api-stats-dashboard .small-box-footer:hover {
    color: white;
    background-color: rgba(0,0,0,0.15);
}

.api-stats-dashboard .box {
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: none;
}

.api-stats-dashboard .description-block {
    margin-bottom: 0;
}

.api-stats-dashboard .border-right {
    border-right: 1px solid #f0f0f0;
}

/* API使用情况卡片 */
.api-stats-dashboard .api-usage-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 16px;
    padding: 30px;
    color: #334155;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.api-stats-dashboard .api-usage-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.3) 0%, transparent 50%);
    pointer-events: none;
}

.api-stats-dashboard .usage-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
}

.api-stats-dashboard .usage-stats {
    flex: 1;
}

.api-stats-dashboard .main-stat h2 {
    font-size: 48px;
    font-weight: 700;
    margin: 0;
    line-height: 1;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.api-stats-dashboard .stat-label {
    font-size: 16px;
    color: #64748b;
    font-weight: 500;
    margin-top: 5px;
    display: block;
}

.api-stats-dashboard .sub-stats {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

.api-stats-dashboard .sub-stat {
    text-align: center;
}

.api-stats-dashboard .sub-stat .value {
    display: block;
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1;
}

.api-stats-dashboard .sub-stat .label {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-top: 4px;
}

.api-stats-dashboard .usage-icon {
    font-size: 80px;
    color: rgba(100, 116, 139, 0.15);
    line-height: 1;
}

/* 使用率进度条 */
.api-stats-dashboard .usage-progress-container {
    position: relative;
}

.api-stats-dashboard .progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.api-stats-dashboard .progress-label {
    font-size: 14px;
    color: #64748b;
    font-weight: 500;
}

.api-stats-dashboard .progress-percentage {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
}

.api-stats-dashboard .usage-progress-bar {
    position: relative;
    height: 12px;
    margin-bottom: 8px;
}

.api-stats-dashboard .progress-track {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: #e2e8f0;
    border-radius: 6px;
}

.api-stats-dashboard .progress-fill {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    border-radius: 6px;
    transition: all 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 2;
    max-width: 100%; /* 防止超出 */
}

/* 正常状态 - 蓝色 */
.api-stats-dashboard .progress-fill.normal {
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
}

/* 警告状态 - 橙色 */
.api-stats-dashboard .progress-fill.warning {
    background: linear-gradient(90deg, #f59e0b, #d97706);
    box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);
}

/* 危险状态 - 红色 */
.api-stats-dashboard .progress-fill.danger {
    background: linear-gradient(90deg, #ef4444, #dc2626);
    box-shadow: 0 0 15px rgba(239, 68, 68, 0.3);
}

/* 超额状态 - 深红色 */
.api-stats-dashboard .progress-fill.exceeded {
    background: linear-gradient(90deg, #dc2626, #991b1b);
    box-shadow: 0 0 20px rgba(220, 38, 38, 0.4);
    animation: pulse-exceeded 2s infinite;
}

/* 无限制状态 - 绿色彩虹渐变 */
.api-stats-dashboard .progress-fill.unlimited {
    background: linear-gradient(90deg, #10b981, #059669, #047857, #065f46, #064e3b);
    background-size: 200% 100%;
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
    animation: unlimited-flow 3s linear infinite;
}

@keyframes pulse-exceeded {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

@keyframes unlimited-flow {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.api-stats-dashboard .progress-glow {
    position: absolute;
    top: -2px;
    left: 0;
    height: 16px;
    width: 20px;
    border-radius: 50%;
    opacity: 0;
    transition: all 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 3;
}

.api-stats-dashboard .progress-glow.normal {
    background: radial-gradient(ellipse, rgba(59, 130, 246, 0.4) 0%, transparent 70%);
}

.api-stats-dashboard .progress-glow.warning {
    background: radial-gradient(ellipse, rgba(245, 158, 11, 0.4) 0%, transparent 70%);
}

.api-stats-dashboard .progress-glow.danger,
.api-stats-dashboard .progress-glow.exceeded {
    background: radial-gradient(ellipse, rgba(239, 68, 68, 0.4) 0%, transparent 70%);
}

.api-stats-dashboard .progress-glow.unlimited {
    background: radial-gradient(ellipse, rgba(16, 185, 129, 0.6) 0%, transparent 70%);
    animation: unlimited-glow 2s ease-in-out infinite alternate;
}

@keyframes unlimited-glow {
    0% { opacity: 0.6; }
    100% { opacity: 1; }
}

/* 立即刷新按钮样式 */
.api-stats-dashboard #manual-refresh {
    background: #00a65a !important;
    border: 1px solid #00a65a !important;
    color: white !important;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
    outline: none;
}

.api-stats-dashboard #manual-refresh:hover {
    background: #00924a !important;
    border-color: #00924a !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 166, 90, 0.3) !important;
}

.api-stats-dashboard #manual-refresh:active {
    transform: translateY(0);
    box-shadow: 0 1px 4px rgba(0, 166, 90, 0.2) !important;
}

.api-stats-dashboard #manual-refresh:disabled {
    background: #95a5a6 !important;
    border-color: #95a5a6 !important;
    cursor: not-allowed;
    transform: none;
    box-shadow: none !important;
}

.api-stats-dashboard #manual-refresh:disabled:hover {
    background: #95a5a6 !important;
    border-color: #95a5a6 !important;
    transform: none;
    box-shadow: none !important;
}

.api-stats-dashboard .progress-markers {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #64748b;
    margin-top: 5px;
}

.api-stats-dashboard .marker {
    position: relative;
}

.api-stats-dashboard .marker::before {
    content: '';
    position: absolute;
    top: -17px;
    left: 50%;
    transform: translateX(-50%);
    width: 1px;
    height: 8px;
    background: #cbd5e1;
}

.api-stats-dashboard .marker.start::before,
.api-stats-dashboard .marker.end::before {
    background: #94a3b8;
    height: 10px;
    top: -19px;
}

/* 响应式设计 */
@media (max-width: 768px) {
    .api-stats-dashboard .api-usage-card {
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .api-stats-dashboard .usage-header {
        flex-direction: column;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .api-stats-dashboard .main-stat h2 {
        font-size: 36px;
    }
    
    .api-stats-dashboard .sub-stats {
        gap: 20px;
        justify-content: space-around;
    }
    
    .api-stats-dashboard .sub-stat .value {
        font-size: 16px;
    }
    
    .api-stats-dashboard .usage-icon {
        font-size: 60px;
        text-align: center;
    }
    
    .api-stats-dashboard .usage-progress-bar {
        height: 10px;
    }
    
    .api-stats-dashboard .progress-glow {
        height: 14px;
        top: -2px;
    }
}

@media (max-width: 480px) {
    .api-stats-dashboard .api-usage-card {
        padding: 15px;
    }
    
    .api-stats-dashboard .main-stat h2 {
        font-size: 28px;
    }
    
    .api-stats-dashboard .sub-stats {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .api-stats-dashboard .sub-stat {
        padding: 10px;
        background: rgba(226, 232, 240, 0.5);
        border-radius: 8px;
    }
    
    .api-stats-dashboard .progress-markers {
        font-size: 10px;
    }
    
    .api-stats-dashboard .progress-markers .marker:nth-child(2),
    .api-stats-dashboard .progress-markers .marker:nth-child(4) {
        display: none;
    }
}

@media (max-width: 768px) {
    .api-stats-dashboard .small-box h3 {
        font-size: 24px;
    }
    
    .api-stats-dashboard .small-box .icon {
        font-size: 60px;
        top: 10px;
        right: 10px;
    }
    
    .api-stats-dashboard .description-block {
        margin-bottom: 20px;
    }
    
    .api-stats-dashboard .border-right {
        border-right: none;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
    }
}

@media (max-width: 480px) {
    .api-stats-dashboard .small-box h3 {
        font-size: 20px;
    }
    
    .api-stats-dashboard .small-box .icon {
        font-size: 50px;
    }
    
    .api-stats-dashboard .small-box .inner {
        padding: 12px;
    }
}
</style>

<script>
// 页面数据
window.apiStatsData = @json($data ?? []);

// 页面配置
window.refreshInterval = {{ $data['refresh_interval'] ?? 10 }};

// 全局定时器变量
window.apiStatsTimer = null;

$(document).ready(function() {
    // 清理之前的定时器（防止pjax重复创建）
    if (window.apiStatsTimer) {
        clearInterval(window.apiStatsTimer);
        window.apiStatsTimer = null;
    }
    
    // 初始化图表
    initCharts();
    
    // 初始化进度条动画
    initProgressAnimation();
    
    // 手动刷新按钮（使用off先解绑，防止重复绑定）
    $('#manual-refresh').off('click').on('click', function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> 刷新中...');
        
        updateRealtimeData().always(function() {
            setTimeout(function() {
                $btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> 立即刷新');
            }, 800);
        });
    });
    
    // 启动自动刷新
    startAutoRefresh();
});

// 页面卸载时清理定时器
$(window).on('beforeunload', function() {
    if (window.apiStatsTimer) {
        clearInterval(window.apiStatsTimer);
        window.apiStatsTimer = null;
    }
});

// pjax事件处理
$(document).on('pjax:beforeSend', function() {
    // pjax跳转前清理定时器
    if (window.apiStatsTimer) {
        clearInterval(window.apiStatsTimer);
        window.apiStatsTimer = null;
    }
});

// 更新实时数据
function updateRealtimeData() {
    return $.get('/console/api-stats/realtime')
        .done(function(response) {
            if (response.success && response.data) {
                const data = response.data;
                
                // 更新主要数字
                $('.main-stat h2').text(Number(data.total_calls).toLocaleString());
                
                // 更新子统计
                if (data.is_unlimited) {
                    $('.sub-stat:nth-child(1) .value').html('<i class="fa fa-infinity text-success"></i> 无限制');
                    $('.sub-stat:nth-child(2) .value').html('<i class="fa fa-infinity text-success"></i> 无限制');
                    $('.sub-stat:nth-child(3) .value').html('<i class="fa fa-check-circle text-success"></i> 畅享');
                } else {
                    $('.sub-stat:nth-child(1) .value').text(Number(data.limit).toLocaleString());
                    $('.sub-stat:nth-child(2) .value').text(Number(data.remaining).toLocaleString());
                    $('.sub-stat:nth-child(3) .value').text(data.usage_percentage + '%');
                }
                
                // 更新进度条百分比显示
                if (data.is_unlimited) {
                    $('.progress-percentage').html('<i class="fa fa-infinity text-success"></i> 畅享无限');
                } else {
                    $('.progress-percentage').text(data.usage_percentage + '%');
                }
                
                // 更新进度条状态和动画
                updateProgressBar(data.usage_percentage, data.is_unlimited);
                
                // 更新最后更新时间
                $('#last-update-time').html('<i class="fa fa-clock-o"></i> 最后更新: ' + data.last_updated);
                
                console.log('实时数据更新成功:', data);
            } else {
                console.error('数据格式错误:', response);
            }
        })
        .fail(function(xhr, status, error) {
            console.error('获取实时数据失败:', error);
            console.error('响应状态:', xhr.status);
            console.error('响应文本:', xhr.responseText);
        });
}

// 更新进度条
function updateProgressBar(percentage, isUnlimited = false) {
    const $progressFill = $('.progress-fill');
    const $progressGlow = $('.progress-glow');
    
    // 确定状态类
    let statusClass = 'normal';
    let displayWidth = Math.min(percentage, 100);
    
    if (isUnlimited) {
        statusClass = 'unlimited';
        displayWidth = 100; // 无限制用户进度条显示100%
    } else {
        if (percentage >= 100) {
            statusClass = 'exceeded';
        } else if (percentage >= 80) {
            statusClass = 'danger';
        } else if (percentage >= 60) {
            statusClass = 'warning';
        }
    }
    
    // 移除旧的状态类
    $progressFill.removeClass('normal warning danger exceeded unlimited');
    $progressGlow.removeClass('normal warning danger exceeded unlimited');
    
    // 添加新的状态类
    $progressFill.addClass(statusClass);
    $progressGlow.addClass(statusClass);
    
    // 更新进度条宽度
    $progressFill.css('width', displayWidth + '%');
    
    // 更新发光效果位置
    if (displayWidth > 0) {
        $progressGlow.css({
            'left': 'calc(' + displayWidth + '% - 10px)',
            'opacity': '1'
        });
    }
}

// 自动刷新功能
function startAutoRefresh() {
    // 先清理之前的定时器
    if (window.apiStatsTimer) {
        clearInterval(window.apiStatsTimer);
        window.apiStatsTimer = null;
    }
    
    let countdown = window.refreshInterval;
    
    const updateCountdown = function() {
        const $refreshStatus = $('#refresh-status');
        if ($refreshStatus.length > 0) {
            $refreshStatus.html('<i class="fa fa-refresh"></i> ' + countdown + '秒后自动刷新');
        }
        countdown--;
        
        if (countdown < 0) {
            // 执行增量更新
            updateRealtimeData().always(function() {
                // 重置倒计时
                countdown = window.refreshInterval;
            });
        }
    };
    
    // 立即更新一次倒计时显示
    updateCountdown();
    
    // 每秒更新倒计时，保存定时器引用
    window.apiStatsTimer = setInterval(updateCountdown, 6000);
}

// 初始化进度条动画
function initProgressAnimation() {
    setTimeout(() => {
        $('.progress-fill').each(function() {
            const $this = $(this);
            const percentage = $this.data('percentage') || 0;
            const displayWidth = $this.data('display-width') || percentage;
            const isUnlimited = $this.data('unlimited') === 'true';
            
            // 设置进度条宽度
            $this.css('width', displayWidth + '%');
            
            // 设置发光效果位置
            const $glow = $this.siblings('.progress-glow');
            if (displayWidth > 0) {
                $glow.css({
                    'left': 'calc(' + displayWidth + '% - 10px)',
                    'opacity': '1'
                });
            }
        });
    }, 300);
}

// 初始化图表
function initCharts() {
    if (!window.apiStatsData) return;
    
    // 初始化趋势图
    initTrendChart();
    
    // 初始化饼图
    if (window.apiStatsData.total_calls > 0) {
        initPieChart();
    }
}

// 初始化趋势图
function initTrendChart() {
    const ctx = document.getElementById('trend-chart');
    if (!ctx || !window.apiStatsData.trend_data) return;
    
    const trendData = window.apiStatsData.trend_data.daily_data;
    const labels = trendData.map(item => item.formatted_date);
    const values = trendData.map(item => item.total_calls);
    
    // 为今日数据点设置不同的样式
    const pointBackgroundColors = trendData.map(item => item.is_today ? '#ef4444' : '#3b82f6');
    const pointBorderColors = trendData.map(item => item.is_today ? '#dc2626' : '#2563eb');
    const pointRadii = trendData.map(item => item.is_today ? 6 : 4);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'API调用次数',
                data: values,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: pointBackgroundColors,
                pointBorderColor: pointBorderColors,
                pointBorderWidth: 2,
                pointRadius: pointRadii,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        title: function(context) {
                            const index = context[0].dataIndex;
                            return trendData[index].date;
                        },
                        label: function(context) {
                            return '调用次数: ' + context.parsed.y.toLocaleString() + ' 次';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

// 初始化饼图
function initPieChart() {
    const ctx = document.getElementById('pie-chart');
    if (!ctx || !window.apiStatsData.app_stats) return;
    
    // 只显示有调用的应用
    const activeApps = window.apiStatsData.app_stats.filter(app => app.call_count > 0);
    if (activeApps.length === 0) return;
    
    const labels = activeApps.map(item => item.app_name);
    const values = activeApps.map(item => item.call_count);
    const colors = generateColors(activeApps.length);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 2,
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value.toLocaleString() + ' 次 (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '50%'
        }
    });
}

// 生成颜色
function generateColors(count) {
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
        '#9966FF', '#FF9F40', '#C9CBCF', '#4BC0C0'
    ];
    
    return colors.slice(0, count);
}
</script> 