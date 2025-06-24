@extends('layouts.app')

@section('title', '确认订单 - 在行云 BaaS 平台')
@section('description', '确认您的套餐购买信息，选择适合的BaaS服务方案')

@section('content')
<div class="subscription-container">
    <div class="subscription-card">
        <div class="subscription-header">
            <h1 class="subscription-title">确认订单</h1>
            <p class="subscription-subtitle">请确认您的套餐购买信息</p>
        </div>

        <div class="subscription-content">
            @if($error)
                <!-- 错误信息显示 -->
                <div class="error-section">
                    <div class="error-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h2 class="error-title">无法购买此套餐</h2>
                    <p class="error-message">{{ $error }}</p>
                    
                    @if($product_config)
                        <div class="error-product-info">
                            <h3>您尝试购买的套餐：</h3>
                            <div class="product-card">
                                <div class="product-name">{{ $product_config['name'] }}</div>
                                <div class="product-price">¥{{ number_format($product_config['price'] / 100, 2) }}/年</div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="current-subscription">
                        <h3>您当前的套餐状态：</h3>
                        <div class="current-info">
                            <div class="info-row">
                                <span>当前套餐：</span>
                                <span class="highlight">{{ config("product.{$tenant->product}.name") }}</span>
                            </div>
                            @if($tenant->subscription_expires_at)
                                <div class="info-row">
                                    <span>到期时间：</span>
                                    <span>{{ $tenant->subscription_expires_at->format('Y-m-d H:i') }}</span>
                                </div>
                            @else
                                <div class="info-row">
                                    <span>套餐状态：</span>
                                    <span class="highlight">永久有效</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="error-actions">
                        <a href="/pricing" class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                            </svg>
                            返回价格页面
                        </a>
                        <a href="/about#contact-info-section" class="btn btn-secondary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            联系客服
                        </a>
                    </div>
                </div>
            @else
                <!-- 正常的订单确认内容 -->
                <!-- 套餐信息 -->
                <div class="info-section">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                        套餐信息
                    </h3>
                    <div class="info-row">
                        <span class="info-label">套餐名称</span>
                        <span class="info-value" style="color: #667eea;">{{ $product_config['name'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">应用项目</span>
                        <span class="info-value">{{ $product_config['app_limit'] == 9999 ? '不限' : $product_config['app_limit'] }}个</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">API请求</span>
                        <span class="info-value">
                            @if($product_key == 'company')
                                不限
                            @else
                                {{ number_format($product_config['request_limit']) }}次/天
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">数据存储</span>
                        <span class="info-value">{{ $product_config['attach_size'] }}</span>
                    </div>
                </div>

                <!-- 当前套餐状态 -->
                @if($tenant->product !== 'free')
                <div class="info-section" style="background: #fef3f2; border-left-color: #f59e0b;">
                    <h3 style="color: #92400e;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        当前套餐
                    </h3>
                    <div class="info-row">
                        <span class="info-label">当前套餐</span>
                        <span class="info-value">{{ config("product.{$tenant->product}.name") }}</span>
                    </div>
                    @if($tenant->subscription_expires_at)
                    <div class="info-row">
                        <span class="info-label">到期时间</span>
                        <span class="info-value">{{ $tenant->subscription_expires_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <!-- 价格计算 -->
                <div class="price-section">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                        价格明细
                    </h3>
                    <div class="info-row">
                        <span class="info-label">订单类型</span>
                        <span class="info-value">
                            @if($price_info['type'] === 'new_purchase')
                                新购买
                            @elseif($price_info['type'] === 'upgrade')
                                套餐升级
                            @elseif($price_info['type'] === 'renew')
                                套餐续费
                            @endif
                        </span>
                    </div>
                    
                    <div class="info-row" @if(isset($price_info['upgrade_info'])) style="border-bottom: none;" @endif>
                        <span class="info-label">套餐原价</span>
                        <span class="info-value">¥{{ number_format($product_config['price'] / 100, 2) }}</span>
                    </div>

                    <!-- 套餐有效期信息 -->
                    <div class="info-row" style="border-bottom: none;">
                        <span class="info-label">套餐有效期</span>
                        <span class="info-value">
                            @if($product_config['duration'] === 'permanent')
                                永久有效
                            @else
                                {{ $product_config['duration'] }}天
                            @endif
                        </span>
                    </div>

                    @if(isset($price_info['upgrade_info']))
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #bbf7d0;">
                        <div style="font-size: 0.9rem; color: #166534; margin-bottom: 8px; font-weight: 600;">升级计算详情：</div>
                        <div class="info-row" style="border: none; padding: 4px 0;">
                            <span class="info-label" style="font-size: 0.9rem;">当前套餐剩余天数</span>
                            <span class="info-value" style="font-size: 0.9rem;">
                                @if($price_info['upgrade_info']['remaining_days'] === '永久')
                                    永久有效
                                @else
                                    {{ $price_info['upgrade_info']['remaining_days'] }}天
                                @endif
                            </span>
                        </div>
                        <div class="info-row" style="border: none; padding: 4px 0;">
                            <span class="info-label" style="font-size: 0.9rem;">剩余价值</span>
                            <span class="info-value" style="font-size: 0.9rem;">¥{{ number_format($price_info['upgrade_info']['remaining_value'] / 100, 2) }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="final-price">
                        实际支付：¥{{ number_format($price_info['price'] / 100, 2) }}
                    </div>
                </div>

                <!-- 操作按钮 -->
                <div class="action-buttons">
                    <a href="/pricing" class="btn btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                        </svg>
                        返回修改
                    </a>
                    <button onclick="createOrder()" id="confirmBtn" class="btn btn-subscription-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        确认购买
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* 错误页面样式 */
.error-section {
    text-align: center;
    padding: 40px 20px;
}

.error-icon {
    color: #ef4444;
    margin-bottom: 24px;
}

.error-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 16px;
}

.error-message {
    font-size: 1.1rem;
    color: #6b7280;
    margin-bottom: 32px;
    line-height: 1.6;
}

.error-product-info {
    background: #f3f4f6;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    text-align: left;
}

.error-product-info h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 12px;
}

.product-card {
    background: white;
    border-radius: 8px;
    padding: 16px;
    border: 1px solid #e5e7eb;
}

.product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.product-price {
    font-size: 1rem;
    color: #667eea;
    font-weight: 500;
}

.current-subscription {
    background: #fef3f2;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 32px;
    text-align: left;
    border-left: 4px solid #f59e0b;
}

.current-subscription h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #92400e;
    margin-bottom: 12px;
}

.current-info .info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #fed7aa;
}

.current-info .info-row:last-child {
    border-bottom: none;
}

.current-info .highlight {
    color: #92400e;
    font-weight: 600;
}

.error-actions {
    display: flex;
    gap: 16px;
    justify-content: center;
}

@media (max-width: 768px) {
    .error-actions {
        flex-direction: column;
    }
}
</style>
@endpush

@push('scripts')
<script>
function createOrder() {
    const btn = document.getElementById('confirmBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="animation: spin 1s linear infinite;"><path d="M12 4V2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8z"/></svg>创建订单中...';
    
    fetch('/subscription/create-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product: '{{ $product_key }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.href = `/subscription/payment/${data.data.order_id}`;
            }
        } else {
            alert(data.message || '订单创建失败');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('网络错误，请重试');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush 