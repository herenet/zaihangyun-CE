<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>价格方案 - 在行云 BaaS 平台</title>
    <meta name="description" content="选择适合您的在行云BaaS方案，从免费开始，随业务增长升级。专为独立开发者和团队打造的灵活定价。">
    <meta name="keywords" content="在行云,BaaS,后端服务,APP开发,独立开发者,无服务器,云服务">
    <link rel="icon" href="/favicon.ico">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <style>
        .main-content {
            margin-top: 70px;
            min-height: calc(100vh - 70px);
        }

        /* 页面标题区域 */
        .page-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 140px 0 120px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="1"/></g></svg>') repeat;
            opacity: 0.3;
        }

        .page-hero-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .page-title {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 25px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .page-subtitle {
            font-size: 1.4rem;
            color: rgba(255,255,255,0.9);
            line-height: 1.7;
            margin-bottom: 50px;
            font-weight: 300;
        }

        .page-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            padding: 12px 24px;
            margin-bottom: 40px;
            color: white;
            font-weight: 600;
            font-size: 15px;
            backdrop-filter: blur(10px);
        }

        /* 价格方案区域 */
        .pricing-section {
            padding: 100px 0;
            background: #f8fafc;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 24px;
            margin-bottom: 80px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .pricing-card {
            background: white;
            border-radius: 12px;
            padding: 28px 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            position: relative;
            min-height: 480px;
            display: flex;
            flex-direction: column;
        }

        .pricing-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .pricing-card.featured {
            border: 2px solid #667eea;
            transform: scale(1.05);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 15px 50px rgba(102, 126, 234, 0.2);
        }

        .pricing-card.featured .price-amount {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .pricing-card.featured .price-currency {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .pricing-card.featured .price-note {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 700;
        }

        .plan-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .plan-name {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .plan-description {
            color: #718096;
            font-size: 1rem;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .plan-price {
            margin-bottom: 28px;
        }

        .price-display {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 6px;
            margin-bottom: 8px;
        }

        .price-currency {
            font-size: 1.3rem;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }

        .price-amount {
            font-size: 3.8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            letter-spacing: -2px;
        }

        .price-period {
            font-size: 1.1rem;
            color: #718096;
            font-weight: 500;
        }

        .price-note {
            font-size: 1rem;
            color: #3b82f6;
            margin-top: 8px;
            font-weight: 600;
            background: rgba(59, 130, 246, 0.1);
            padding: 4px 12px;
            border-radius: 12px;
            display: inline-block;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 32px;
            padding: 0;
            flex-grow: 1;
        }

        .plan-features li {
            padding: 10px 0;
            display: flex;
            align-items: flex-start;
            color: #2d3748;
            font-size: 1rem;
            line-height: 1.4;
        }

        .plan-features li::before {
            content: '✓';
            color: #48bb78;
            font-weight: 900;
            margin-right: 15px;
            width: 18px;
            flex-shrink: 0;
            margin-top: 2px;
            font-size: 1.1rem;
        }

        .plan-features li.unavailable {
            color: #a0aec0;
        }

        .plan-features li.unavailable::before {
            content: '—';
            color: #cbd5e0;
            font-weight: 400;
        }

        .plan-button {
            width: 100%;
            padding: 18px 24px;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-block;
            text-align: center;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
            margin-top: auto;
        }

        .plan-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .plan-button:hover::before {
            left: 100%;
        }

        .plan-button.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .plan-button.primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .plan-button.secondary {
            background: white;
            color: #667eea;
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .plan-button.secondary:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.2);
        }

        /* 功能对比表格 */
        .comparison-section {
            padding: 100px 0;
            background: white;
        }

        .comparison-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 60px;
            letter-spacing: -1px;
        }

        .comparison-table {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .comparison-table table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 20px 25px;
            text-align: left;
            border-bottom: 1px solid #f7fafc;
            white-space: nowrap;
        }

        .comparison-table th {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            font-weight: 700;
            color: #2d3748;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .comparison-table td {
            color: #4a5568;
            font-size: 1rem;
            font-weight: 500;
        }

        .comparison-table .plan-column {
            text-align: center;
            font-weight: 700;
            color: #1a202c;
            font-size: 1.1rem;
        }

        .comparison-table .feature-available {
            color: #48bb78;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 900;
        }

        .comparison-table .feature-unavailable {
            color: #f56565;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 900;
        }

        .comparison-table tbody tr:hover {
            background: #f8fafc;
        }

        /* FAQ 部分 */
        .faq-section {
            padding: 100px 0;
            background: #f8fafc;
        }

        .faq-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 60px;
            letter-spacing: -1px;
        }

        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-item {
            background: white;
            border-radius: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .faq-question {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a202c;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: color 0.3s ease;
            padding: 30px;
        }

        .faq-question:hover {
            color: #667eea;
        }

        .faq-icon {
            width: 24px;
            height: 24px;
            transition: transform 0.3s ease;
            color: #667eea;
        }

        .faq-answer {
            color: #4a5568;
            line-height: 1.7;
            padding: 0 30px 30px;
            display: none;
            font-size: 1.1rem;
        }

        .faq-item.active .faq-answer {
            display: block;
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }

        .faq-item.active .faq-question {
            color: #667eea;
        }

        /* 联系销售 */
        .contact-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .contact-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="1"/></g></svg>') repeat;
            opacity: 0.3;
        }

        .contact-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .contact-description {
            font-size: 1.3rem;
            margin-bottom: 50px;
            opacity: 0.9;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .contact-buttons {
            display: flex;
            gap: 25px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .contact-button {
            background: white;
            color: #667eea;
            padding: 18px 40px;
            border: none;
            border-radius: 16px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            letter-spacing: 0.5px;
        }

        .contact-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.3);
        }

        .contact-button.secondary {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .contact-button.secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
        }

        /* 响应式设计 */
        @media (max-width: 1400px) {
            .container {
                max-width: 1200px;
            }
            
            .pricing-grid {
                gap: 30px;
            }
        }

        @media (max-width: 1200px) {
            .pricing-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
            
            .pricing-card.featured {
                transform: scale(1.02);
            }
        }

        @media (max-width: 900px) {
            .pricing-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2.8rem;
            }

            .page-subtitle {
                font-size: 1.2rem;
            }

            .pricing-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .pricing-card {
                min-height: auto;
                padding: 35px 25px;
            }

            .pricing-card.featured {
                transform: none;
            }

            .comparison-table {
                overflow-x: auto;
            }

            .contact-buttons {
                flex-direction: column;
                align-items: center;
            }

            .contact-button {
                width: 100%;
                max-width: 300px;
            }

            .comparison-title,
            .faq-title,
            .contact-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 20px;
            }
            
            .pricing-card {
                padding: 30px 20px;
            }
            
            .page-hero {
                padding: 80px 0 60px;
            }
            
            .pricing-section,
            .comparison-section,
            .faq-section,
            .contact-section {
                padding: 80px 0;
            }
        }
    </style>
</head>
<body>
    @include('layouts.header')

    <!-- 主要内容 -->
    <main class="main-content">
        <!-- 页面标题区域 -->
        <section class="page-hero">
            <div class="page-hero-content">
                <div class="page-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span>灵活定价，按需选择</span>
                </div>
                <h1 class="page-title">选择更佳的方案</h1>
                <p class="page-subtitle">
                    适用各种规模的开发团队——从独立开发者到大企业<br>
                    从免费开始，随着业务增长升级到更高级的方案
                </p>
            </div>
        </section>

        <!-- 价格方案 -->
        <section class="pricing-section">
            <div class="container">
                <div class="pricing-grid">
                    @foreach($products as $product)
                    <div class="pricing-card @if($product['key'] == 'adv') featured @endif">
                        <div class="plan-header">
                            <h3 class="plan-name">{{ $product['name'] }}</h3>
                            <p class="plan-description">
                                @switch($product['key'])
                                    @case('free')
                                        适用于个人开发者试用
                                        @break
                                    @case('basic')
                                        适用于个人独立开发者
                                        @break
                                    @case('adv')
                                        适用于小团队和多项目
                                        @break
                                    @case('pro')
                                        适用于成熟开发者和小公司
                                        @break
                                    @case('company')
                                        适用于大企业和高要求客户
                                        @break
                                @endswitch
                            </p>
                            <div class="plan-price">
                                <div class="price-display">
                                    <span class="price-currency">¥</span>
                                    <span class="price-amount">{{ $product['price_yuan'] }}</span>
                                    <span class="price-period">/年</span>
                                </div>
                                <div class="price-note">
                                    @if($product['key'] == 'free')
                                        永久免费
                                    @elseif($product['key'] == 'adv')
                                        最受欢迎的方案
                                    @else
                                        约{{ round($product['price_yuan'] / 12, 1) }}元/月
                                    @endif
                                </div>
                            </div>
                        </div>
                        <ul class="plan-features">
                            <li>{{ $product['app_limit'] == 9999 ? '不限' : $product['app_limit'] }}个应用项目</li>
                            <li>
                                @if($product['key'] == 'company')
                                    不限 API请求
                                @else
                                    {{ number_format($product['request_limit']) }}次/天 API请求
                                @endif
                            </li>
                            <li>{{ $product['attach_size'] }} 数据存储</li>
                            <li>应用设置</li>
                            <li>用户管理</li>
                            <li>文档管理</li>
                            <li>用户互动</li>
                            @if($product['module_enable']['sales_mng'])
                                <li>售卖管理</li>
                            @else
                                <li class="unavailable">售卖管理</li>
                            @endif
                            @if($product['key'] != 'free')
                                <li>技术支持</li>
                            @else
                                <li class="unavailable">技术支持</li>
                            @endif
                            @if(in_array($product['key'], ['adv', 'pro', 'company']))
                                <li>数据迁移</li>
                            @else
                                <li class="unavailable">数据迁移</li>
                            @endif
                        </ul>
                        @if($product['key'] == 'free')
                            <a href="/console/auth/login" class="plan-button secondary">
                                免费开始
                            </a>
                        @elseif($product['key'] == 'company')
                            <a href="/about#contact-info-section" class="plan-button secondary">
                                联系销售
                            </a>
                        @else
                            <a href="/subscription/confirm?product={{ $product['key'] }}" class="plan-button @if($product['key'] == 'adv') primary @else secondary @endif">
                                立即购买
                            </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 功能对比表格 -->
        <section class="comparison-section">
            <div class="container">
                <h2 class="comparison-title">版本功能对比</h2>
                <div class="comparison-table">
                    <table>
                        <thead>
                            <tr>
                                <th>功能特性</th>
                                @foreach($products as $product)
                                <th class="plan-column">{{ $product['name'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>基础配置</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>应用项目数量</td>
                                @foreach($products as $product)
                                <td class="plan-column">{{ $product['app_limit'] == 9999 ? '不限' : $product['app_limit'] }}个</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>API调用次数</td>
                                @foreach($products as $product)
                                <td class="plan-column">
                                    @if($product['key'] == 'company')
                                        不限
                                    @else
                                        {{ number_format($product['request_limit']) }}次/天
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>数据存储空间</td>
                                @foreach($products as $product)
                                <td class="plan-column">{{ $product['attach_size'] }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td><strong>应用设置</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>基础应用配置</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>应用版本管理</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>多渠道管理</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td><strong>用户管理</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>用户注册登录</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>微信登录</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>短信验证码登录</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>Apple ID登录</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td><strong>文档管理</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>文档创建编辑</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>文档分类管理</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>Markdown渲染</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td><strong>用户互动</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>用户反馈</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>评论系统</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>通知下发</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td><strong>售卖管理</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商品管理</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>订单管理</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>退款处理</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>微信支付</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>支付宝</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>Apple IAP</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td><strong>技术支持</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>社区支持</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>一对一客服</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>优先技术支持</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td><strong>高级功能</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>数据迁移</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                            <tr>
                                <td>专属客户经理</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-unavailable">✗</td>
                                <td class="feature-available">✓</td>
                                <td class="feature-available">✓</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- FAQ 部分 -->
        <section class="faq-section" id="faq-section">
            <div class="container">
                <h2 class="faq-title">常见问题</h2>
                <div class="faq-container">
                    <div class="faq-item">
                        <div class="faq-question">
                            什么是BaaS服务？
                            <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            BaaS（Backend as a Service）是后端即服务，为开发者提供现成的后端功能，如用户认证、数据存储、支付处理等。使用在行云BaaS，您无需搭建服务器，专注于前端开发即可快速构建应用。
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            如何快速接入在行云？
                            <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            注册账号后，创建应用项目，获取API密钥，然后在您的应用中使用您熟悉的HTTP客户端请求即可。我们提供详细的文档和示例代码，大多数功能只需几行代码就能实现。
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            支持哪些开发平台？
                            <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            我们支持iOS、Android、Flutter、React Native等主流开发平台。未来将提供对HarmonyOS等平台的支持。
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            支持哪些支付方式？
                            <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            我们支持支付宝、微信支付、Apple IAP/订阅等多种支付方式并接收支付平台通知回调处理。所有支付都通过加密通道处理，确保安全可靠。
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            数据可以导出吗？
                            <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            是的，您的数据完全属于您。我们可以将您的所有数据包括附件进行打包交付，您可以自行迁移到其他任何平台。
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            如何获得技术支持？
                            <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            我们提供多层次的技术支持：免费版用户可使用社区论坛，付费用户享受一对一客服支持，进阶版及以上用户还有优先技术支持通道，企业版用户配备专属客户经理。
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 联系销售 -->
        <section class="contact-section">
            <div class="container">
                <h2 class="contact-title">需要定制方案？</h2>
                <p class="contact-description">
                    如果标准方案不能满足您的需求，我们可以为您量身定制企业级解决方案，<br>
                    包括私有化部署、定制功能开发和专业技术支持服务。
                </p>
                <div class="contact-buttons">
                    <a href="/about#contact-info-section" class="contact-button">联系销售团队</a>
                    <a href="/about" class="contact-button secondary">了解更多</a>
                </div>
            </div>
        </section>
    </main>

    @include('layouts.footer')
    @include('layouts.scripts')

    <script>
        // FAQ 交互
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                // 关闭所有其他FAQ项
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // 切换当前FAQ项
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
    </script>
</body>
</html> 