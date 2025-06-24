@extends('layouts.app')

@section('title', '扫码支付')

@section('content')
<div class="subscription-container">
    <div class="subscription-card">
        <!-- 页面头部 -->
        <div class="subscription-header">
            <div class="payment-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                    <circle cx="12" cy="15" r="2"/>
                </svg>
            </div>
            <h1 class="subscription-title">扫码支付</h1>
            <p class="subscription-subtitle">请使用微信扫描下方二维码完成支付</p>
        </div>

        <div class="subscription-content">
            <!-- 订单信息 -->
            <div class="info-section">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    订单信息
                </h3>
                
                <div class="info-row">
                    <span class="info-label">订单号</span>
                    <span class="info-value order-id">{{ $order->order_id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">套餐名称</span>
                    <span class="info-value">{{ $order->product_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">支付金额</span>
                    <span class="info-value price-highlight">{{ $order->formatted_final_price }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">订单状态</span>
                    <span class="info-value status-badge" id="orderStatus">{{ $order->status_text }}</span>
                </div>
            </div>

            <!-- 二维码区域 -->
            <div class="qr-section">
                <div class="qr-container">
                    <div id="qrcode" class="qr-code"></div>
                    <div class="qr-label">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.479 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
                        </svg>
                        微信扫一扫
                    </div>
                </div>
            </div>

            <!-- 支付状态 -->
            <div class="payment-status" id="paymentStatus">
                <div class="status-indicator">
                    <div class="loading-spinner"></div>
                    <span class="status-text">等待支付...</span>
                </div>
            </div>

            <!-- 操作按钮 -->
            <div class="action-buttons">
                <button onclick="window.location.href='/pricing'" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    返回套餐选择
                </button>
                <button onclick="checkPaymentStatus()" id="refreshBtn" class="btn btn-subscription-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                    </svg>
                    刷新状态
                </button>
            </div>

            <!-- 支付说明 -->
            <div class="payment-notice">
                <h4>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                    支付说明
                </h4>
                <ul>
                    <li>请在2小时内完成支付，超时订单将自动关闭</li>
                    <li>支付完成后页面会自动跳转，请不要关闭当前页面</li>
                    <li>如遇到问题，请联系客服处理</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- 引入二维码库 - 使用CDNJS -->
<script src="{{ asset('js/qrcode.min.js') }}"></script>
@endsection

@push('styles')
<style>
/* 支付页面特有样式 */
.payment-icon {
    margin-bottom: 16px;
}

.order-id {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.9rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
}

.price-highlight {
    color: #dc2626;
    font-size: 1.25rem;
    font-weight: 700;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    background: #fbbf24;
    color: #92400e;
}

.status-badge.paid {
    background: #10b981;
    color: white;
}

.status-badge.failed {
    background: #ef4444;
    color: white;
}

.qr-section {
    text-align: center;
    margin: 32px 0;
}

.qr-container {
    display: inline-block;
    background: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.qr-code {
    margin-bottom: 16px;
}

.qr-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #10b981;
    font-weight: 600;
    font-size: 1rem;
}

.payment-status {
    text-align: center;
    margin: 24px 0;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 16px 24px;
    background: #f0f9ff;
    border-radius: 12px;
    border: 1px solid #bae6fd;
}

.loading-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #e5e7eb;
    border-top: 2px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.status-text {
    color: #1e40af;
    font-weight: 600;
}

.payment-notice {
    background: #fffbeb;
    border-radius: 12px;
    padding: 20px;
    margin-top: 32px;
    border-left: 4px solid #f59e0b;
}

.payment-notice h4 {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #92400e;
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.payment-notice ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.payment-notice li {
    color: #78350f;
    margin-bottom: 8px;
    padding-left: 16px;
    position: relative;
}

.payment-notice li:before {
    content: '•';
    color: #f59e0b;
    font-weight: bold;
    position: absolute;
    left: 0;
}

.payment-notice li:last-child {
    margin-bottom: 0;
}

/* 成功状态样式 */
.payment-success .status-indicator {
    background: #f0fdf4;
    border-color: #bbf7d0;
}

.payment-success .status-text {
    color: #166534;
}

.payment-success .loading-spinner {
    display: none;
}

/* 失败状态样式 */
.payment-failed .status-indicator {
    background: #fef2f2;
    border-color: #fecaca;
}

.payment-failed .status-text {
    color: #dc2626;
}

.payment-failed .loading-spinner {
    display: none;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* 响应式设计 */
@media (max-width: 768px) {
    .qr-container {
        padding: 16px;
    }
    
    .order-id {
        font-size: 0.8rem;
        word-break: break-all;
    }
    
    .payment-notice {
        padding: 16px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// 生成二维码 - 使用qrcodejs
const qrCodeUrl = '{{ $order->wechat_code_url }}';
if (qrCodeUrl) {
    const qrcode = new QRCode(document.getElementById('qrcode'), {
        text: qrCodeUrl,
        width: 200,
        height: 200,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    });
} else {
    document.getElementById('qrcode').innerHTML = '<p style="color: #ef4444;">二维码获取失败</p>';
}

// 轮询检查支付状态
let pollInterval;
let pollCount = 0;
const maxPollCount = 240; // 最多轮询4分钟

function startPolling() {
    pollInterval = setInterval(() => {
        pollCount++;
        if (pollCount > maxPollCount) {
            clearInterval(pollInterval);
            updatePaymentStatus('timeout', '支付超时，请刷新页面重试');
            return;
        }
        
        checkPaymentStatus(false);
    }, 1000);
}

function updatePaymentStatus(type, message) {
    const statusElement = document.getElementById('paymentStatus');
    const statusBadge = document.getElementById('orderStatus');
    
    statusElement.className = 'payment-status';
    
    switch(type) {
        case 'success':
            statusElement.classList.add('payment-success');
            statusElement.innerHTML = `
                <div class="status-indicator">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#10b981">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    <span class="status-text">${message}</span>
                </div>
            `;
            if (statusBadge) {
                statusBadge.className = 'info-value status-badge paid';
                statusBadge.textContent = '支付成功';
            }
            break;
        case 'failed':
            statusElement.classList.add('payment-failed');
            statusElement.innerHTML = `
                <div class="status-indicator">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#ef4444">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                    <span class="status-text">${message}</span>
                </div>
            `;
            if (statusBadge) {
                statusBadge.className = 'info-value status-badge failed';
                statusBadge.textContent = '支付失败';
            }
            break;
        case 'timeout':
            statusElement.classList.add('payment-failed');
            statusElement.innerHTML = `
                <div class="status-indicator">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#f59e0b">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v5z"/>
                    </svg>
                    <span class="status-text">${message}</span>
                </div>
            `;
            break;
    }
}

function checkPaymentStatus(manual = true) {
    const refreshBtn = document.getElementById('refreshBtn');
    
    if (manual && refreshBtn) {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<div class="loading-spinner" style="width: 16px; height: 16px; margin-right: 8px;"></div>检查中...';
    }
    
    fetch(`/subscription/order-status/{{ $order->order_id }}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusBadge = document.getElementById('orderStatus');
                if (statusBadge) {
                    statusBadge.textContent = data.data.status_text;
                }
                
                if (data.data.is_paid) {
                    clearInterval(pollInterval);
                    updatePaymentStatus('success', '✓ 支付成功！正在跳转...');
                    
                    setTimeout(() => {
                        window.location.href = '/console';
                    }, 2000);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (manual) {
                updatePaymentStatus('failed', '状态检查失败，请重试');
            }
        })
        .finally(() => {
            if (manual && refreshBtn) {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                    </svg>
                    刷新状态
                `;
            }
        });
}

// 页面加载完成后开始轮询
document.addEventListener('DOMContentLoaded', function() {
    startPolling();
});

// 页面隐藏时停止轮询，显示时重新开始
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(pollInterval);
    } else {
        if (!document.getElementById('paymentStatus').classList.contains('payment-success')) {
            startPolling();
        }
    }
});
</script>
@endpush 