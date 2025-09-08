<div class="dual-stats-container">
    <div class="dual-stats-grid">
        @foreach($stats as $stat)
            <div class="dual-stats-card {{ $loop->first ? 'dual-stats-card-blue' : ($loop->iteration == 2 ? 'dual-stats-card-green' : 'dual-stats-card-orange') }}">
                <div class="stats-header">
                    <span class="stats-title">{{ $stat['title'] }}</span>
                    <div class="stats-icon" style="background: {{ $stat['gradient'] }}">
                        <i class="fa fa-{{ $stat['icon'] }}"></i>
                    </div>
                </div>
                
                <div class="stats-body">
                    <div class="stats-number">{{ $stat['primary']['value'] }}</div>
                    <div class="stats-meta">
                        <span class="stats-period">{{ $stat['primary']['label'] ?? '今日数据' }}</span>
                        @if(isset($stat['primary']['trend']))
                        <div class="stats-compare">
                            <span class="compare-text">较昨日</span>
                            <span class="compare-badge growth-{{ $stat['primary']['trend']['type'] }}">
                                <i class="fa fa-{{ $stat['primary']['trend']['icon'] }}"></i> {{ $stat['primary']['trend']['text'] }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="stats-footer">
                    <div class="footer-row">
                        <span class="total-data">{{ $stat['secondary']['label'] ?? '总计' }}: {{ $stat['secondary']['value'] }}</span>
                        @if(isset($stat['yesterday']))
                        <span class="yesterday-data">昨日: {{ $stat['yesterday']['value'] }}</span>
                        @endif
                    </div>
                    @if(isset($stat['secondary']['subtitle']))
                    <div class="stats-subtitle">{{ $stat['secondary']['subtitle'] }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
/* === 双重统计卡片容器 === */
.dual-stats-container {
    background: transparent;
    padding: 0;
    margin-bottom: 24px;
}

.dual-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

/* === 基础统计卡片样式 === */
.dual-stats-card {
    background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid #E5E7EB;
    padding: 16px 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: auto;
    min-height: 140px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}

.dual-stats-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    transform: translateY(-3px);
    background: linear-gradient(135deg, #FAFBFC 0%, #F6F8FA 100%);
}

/* === 不同主题色背景 === */

/* 蓝色主题 (用户统计) */
.dual-stats-card-blue {
    background: linear-gradient(135deg, rgba(64, 134, 245, 0.08) 0%, rgba(107, 155, 247, 0.04) 50%, #FFFFFF 100%) !important;
}

.dual-stats-card-blue:hover .stats-number {
    color: #4086F5 !important;
}

/* 绿色主题 (收入统计) */
.dual-stats-card-green {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(26, 226, 214, 0.04) 50%, #FFFFFF 100%) !important;
}

.dual-stats-card-green:hover .stats-number {
    color: #10B981 !important;
}

/* 橙色主题 (订单统计) */
.dual-stats-card-orange {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(251, 191, 36, 0.04) 50%, #FFFFFF 100%) !important;
}

.dual-stats-card-orange:hover .stats-number {
    color: #F59E0B !important;
}

/* === 卡片内部元素样式 === */
.stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}

.stats-title {
    font-size: 14px;
    color: #6B7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FFFFFF;
    font-size: 18px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.dual-stats-card:hover .stats-icon {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25) !important;
}

.stats-body {
    margin-bottom: 14px;
}

.stats-number {
    font-size: 36px;
    font-weight: 700;
    color: #1F2937;
    line-height: 1;
    margin-bottom: 10px;
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
    padding-top: 12px;
    border-top: 1px solid #F3F4F6;
}

.footer-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 4px;
}

.total-data {
    font-size: 14px;
    color: #6B7280;
    font-weight: 600;
}

.yesterday-data {
    font-size: 13px;
    color: #9CA3AF;
    font-weight: 500;
    background: #F9FAFB;
    padding: 2px 8px;
    border-radius: 4px;
    border: 1px solid #F3F4F6;
}

.stats-subtitle {
    font-size: 12px;
    color: #9CA3AF;
    font-weight: 500;
    margin-top: 4px;
}

/* === 光泽动画效果 === */
@keyframes shine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.dual-stats-card .stats-icon::after {
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

.dual-stats-card:hover .stats-icon::after {
    opacity: 1;
    animation: shine 1.2s ease-in-out;
}

/* === 响应式设计 === */
@media (max-width: 1200px) {
    .dual-stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
    }
    
    .dual-stats-card {
        padding: 14px 18px;
        min-height: 130px;
    }
    
    .stats-number {
        font-size: 32px;
    }
    
    .stats-icon {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
}

@media (max-width: 768px) {
    .dual-stats-card {
        padding: 12px 16px;
        min-height: 120px;
        margin-bottom: 16px;
    }
    
    .stats-number {
        font-size: 28px;
    }
    
    .stats-icon {
        width: 32px;
        height: 32px;
        font-size: 15px;
    }
    
    .stats-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .footer-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .stats-title {
        font-size: 13px;
    }
    
    .total-data, .yesterday-data {
        font-size: 12px;
    }
}

@media (min-width: 1400px) {
    .dual-stats-card {
        padding: 18px 22px;
        min-height: 150px;
    }
    
    .stats-number {
        font-size: 40px;
    }
    
    .stats-title {
        font-size: 15px;
    }
    
    .stats-icon {
        width: 44px;
        height: 44px;
        font-size: 20px;
    }
    
    .total-data {
        font-size: 15px;
    }
    
    .yesterday-data {
        font-size: 14px;
    }
}
</style>