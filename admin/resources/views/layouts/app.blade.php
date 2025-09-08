<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '在行云 BaaS 平台')</title>
    <meta name="description" content="@yield('description', '专为独立开发者和小团队打造的BaaS平台，无需后端开发，快速拥有后端能力，专注产品创新。')">
    <meta name="keywords" content="在行云,BaaS,后端服务,APP开发,独立开发者,无服务器,云服务">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.ico">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    
    @stack('styles')
    
    <style>
        .main-content {
            margin-top: 70px;
            min-height: calc(100vh - 70px);
        }
        
        /* 订阅页面特有样式 */
        .subscription-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .subscription-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        
        .subscription-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
        }
        
        .subscription-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 12px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .subscription-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .subscription-content {
            padding: 40px 30px;
        }
        
        .info-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            border-left: 4px solid #667eea;
        }
        
        .info-section h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #4a5568;
            font-weight: 500;
        }
        
        .info-value {
            color: #1a202c;
            font-weight: 600;
        }
        
        .price-section {
            background: linear-gradient(135deg, #f0fff4 0%, #dcfce7 100%);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
            border: 1px solid #bbf7d0;
        }
        
        .price-section h3 {
            color: #166534;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .final-price {
            font-size: 2rem;
            font-weight: 900;
            color: #059669;
            text-align: center;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #bbf7d0;
        }
        
        .action-buttons {
            display: flex;
            gap: 16px;
            margin-top: 32px;
        }
        
        .btn {
            flex: 1;
            padding: 16px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        /* 订阅页面专用的主要按钮样式 */
        .btn-subscription-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-subscription-primary:hover {
            background: linear-gradient(135deg, #4c51bf 0%, #553c9a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        /* 响应式设计 */
        @media (max-width: 768px) {
            .subscription-container {
                padding: 20px 15px;
            }
            
            .subscription-header {
                padding: 30px 20px;
            }
            
            .subscription-title {
                font-size: 2rem;
            }
            
            .subscription-content {
                padding: 30px 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
            
            .info-value {
                color: #667eea;
                font-weight: 700;
            }
        }
    </style>
</head>
<body>
    @include('layouts.header')
    
    <div class="main-content">
        @yield('content')
    </div>
    
    @include('layouts.footer')
    @include('layouts.scripts')
    
    @stack('scripts')
</body>
</html> 