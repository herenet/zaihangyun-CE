<div class="stats-card stats-card-{{ $color }}">
    <div class="stats-header">
        <span class="stats-title">{{ $title }}</span>
        <div class="stats-icon">
            <i class="fa fa-{{ $icon }}"></i>
        </div>
    </div>
    
    <div class="stats-body">
        <div class="stats-number">{{ $todayValue }}</div>
        <div class="stats-meta">
            <span class="stats-period">今日数据</span>
            <div class="stats-compare">
                <span class="compare-text">较昨日</span>
                <span class="compare-badge {{ $growthClass }}">
                    {{ $growthIcon }} {{ $growthRate }}%
                </span>
            </div>
        </div>
    </div>
    
    <div class="stats-footer">
        <span class="yesterday-data">昨日: {{ $yesterdayValue }}</span>
    </div>
</div>

<style>
/* === 基础统计卡片样式 === */
.stats-card {
    background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid #E5E7EB;
    padding: 16px 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: auto;
    min-height: 120px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}

/* 移除顶部色边 */
.stats-card::before {
    display: none;
}

.stats-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    transform: translateY(-3px);
    background: linear-gradient(135deg, #FAFBFC 0%, #F6F8FA 100%);
}

/* === 不同主题色背景 === */

/* 蓝色主题 (aqua) */
.stats-card-aqua {
    background: linear-gradient(135deg, rgba(64, 134, 245, 0.08) 0%, rgba(107, 155, 247, 0.04) 50%, #FFFFFF 100%) !important;
    /* 移除左边色边 */
    border-left: 1px solid #E5E7EB !important;
    /* 移除顶部色边 */
    border-top: 1px solid #E5E7EB !important;
}

.stats-card-aqua::before {
    display: none;
}

.stats-card-aqua .stats-icon {
    background: linear-gradient(135deg, #4086F5 0%, #6B9BF7 100%) !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 15px rgba(64, 134, 245, 0.3) !important;
}

.stats-card-aqua:hover .stats-number {
    color: #4086F5 !important;
}

/* 绿色主题 */
.stats-card-green {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(26, 226, 214, 0.04) 50%, #FFFFFF 100%) !important;
    /* 移除左边色边 */
    border-left: 1px solid #E5E7EB !important;
    /* 移除顶部色边 */
    border-top: 1px solid #E5E7EB !important;
}

.stats-card-green::before {
    display: none;
}

.stats-card-green .stats-icon {
    background: linear-gradient(135deg, #10B981 0%, #1AE2D6 100%) !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3) !important;
}

.stats-card-green:hover .stats-number {
    color: #10B981 !important;
}

/* 橙色主题 */
.stats-card-yellow {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(251, 191, 36, 0.04) 50%, #FFFFFF 100%) !important;
    /* 移除左边色边 */
    border-left: 1px solid #E5E7EB !important;
    /* 移除顶部色边 */
    border-top: 1px solid #E5E7EB !important;
}

.stats-card-yellow::before {
    display: none;
}

.stats-card-yellow .stats-icon {
    background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%) !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3) !important;
}

.stats-card-yellow:hover .stats-number {
    color: #F59E0B !important;
}

/* 青色主题 */
.stats-card-cyan {
    background: linear-gradient(135deg, rgba(6, 182, 212, 0.08) 0%, rgba(26, 226, 214, 0.04) 50%, #FFFFFF 100%) !important;
    /* 移除左边色边 */
    border-left: 1px solid #E5E7EB !important;
    /* 移除顶部色边 */
    border-top: 1px solid #E5E7EB !important;
}

.stats-card-cyan::before {
    display: none;
}

.stats-card-cyan .stats-icon {
    background: linear-gradient(135deg, #06B6D4 0%, #1AE2D6 100%) !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3) !important;
}

.stats-card-cyan:hover .stats-number {
    color: #06B6D4 !important;
}

/* 红色主题 */
.stats-card-red {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(248, 113, 113, 0.04) 50%, #FFFFFF 100%) !important;
    /* 移除左边色边 */
    border-left: 1px solid #E5E7EB !important;
    /* 移除顶部色边 */
    border-top: 1px solid #E5E7EB !important;
}

.stats-card-red::before {
    display: none;
}

.stats-card-red .stats-icon {
    background: linear-gradient(135deg, #EF4444 0%, #F87171 100%) !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3) !important;
}

.stats-card-red:hover .stats-number {
    color: #EF4444 !important;
}

/* === 卡片内部元素样式 === */
.stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.stats-title {
    font-size: 14px;
    color: #6B7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-icon {
    width: 36px;
    height: 36px;
    background: #F3F4F6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4B5563;
    font-size: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stats-card:hover .stats-icon {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
}

.stats-body {
    margin-bottom: 12px;
}

.stats-number {
    font-size: 32px;
    font-weight: 700;
    color: #1F2937;
    line-height: 1;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.stats-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.stats-period {
    font-size: 12px;
    color: #9CA3AF;
    font-weight: 500;
}

.stats-compare {
    display: flex;
    align-items: center;
    gap: 6px;
}

.compare-text {
    font-size: 12px;
    color: #9CA3AF;
}

.compare-badge {
    font-size: 12px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 3px;
}

.growth-up {
    background: #F0FDF4;
    color: #16A34A;
    border: 1px solid #BBF7D0;
}

.growth-down {
    background: #FEF2F2;
    color: #DC2626;
    border: 1px solid #FECACA;
}

.growth-neutral {
    background: #F9FAFB;
    color: #6B7280;
    border: 1px solid #E5E7EB;
}

.stats-footer {
    padding-top: 10px;
    border-top: 1px solid #F3F4F6;
}

.yesterday-data {
    font-size: 13px;
    color: #9CA3AF;
    font-weight: 500;
}

/* === 响应式设计 === */
@media (max-width: 1200px) {
    .stats-card {
        padding: 14px 18px;
        min-height: 110px;
    }
    
    .stats-number {
        font-size: 28px;
    }
    
    .stats-icon {
        width: 32px;
        height: 32px;
        font-size: 15px;
    }
}

@media (max-width: 768px) {
    .stats-card {
        padding: 12px 16px;
        min-height: 100px;
        margin-bottom: 16px;
    }
    
    .stats-number {
        font-size: 24px;
    }
    
    .stats-icon {
        width: 28px;
        height: 28px;
        font-size: 14px;
    }
    
    .stats-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .stats-title {
        font-size: 13px;
    }
}

@media (min-width: 1400px) {
    .stats-card {
        padding: 18px 22px;
        min-height: 130px;
    }
    
    .stats-number {
        font-size: 36px;
    }
    
    .stats-title {
        font-size: 15px;
    }
    
    .stats-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
}

/* === 光泽动画效果 === */
@keyframes shine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.stats-card .stats-icon::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 50%, transparent 70%);
    opacity: 0;
    transition: all 0.3s ease;
}

.stats-card:hover .stats-icon::after {
    opacity: 1;
    animation: shine 1.2s ease-in-out;
}

/* === 数据加载状态 === */
.stats-card.loading .stats-number {
    background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    color: transparent;
    border-radius: 4px;
    height: 36px;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style> 