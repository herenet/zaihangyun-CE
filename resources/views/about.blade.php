<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>关于我们 - 在行云 BaaS 平台</title>
    <meta name="description" content="了解在行云BaaS平台，专为独立开发者和团队打造的后端服务平台。我们的使命是让开发更简单，让创意更快实现。">
    <link rel="icon" href="/favicon.ico">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <style>
        html {
            scroll-behavior: smooth;
        }

        /* 锚点偏移，避免被固定头部遮挡 */
        section[id] {
            scroll-margin-top: 100px;
        }

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

        /* 公司介绍区域 */
        .intro-section {
            padding: 100px 0;
            background: white;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .intro-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .intro-content h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 30px;
            line-height: 1.2;
        }

        .intro-content p {
            font-size: 1.1rem;
            color: #4a5568;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .intro-stats {
            display: flex;
            gap: 40px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e2e8f0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #718096;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }



        .intro-image {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .intro-visual-card {
            position: relative;
            width: 100%;
            max-width: 500px;
            height: 400px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px solid #e2e8f0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .intro-visual-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 80px rgba(0, 0, 0, 0.12);
            border-color: #cbd5e1;
        }

        .visual-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
        }

        /* 动态背景装饰 */
        .bg-element {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .bg-element:nth-child(1) {
            width: 120px;
            height: 120px;
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .bg-element:nth-child(2) {
            width: 80px;
            height: 80px;
            top: 65%;
            right: 15%;
            animation-delay: 2s;
        }

        .bg-element:nth-child(3) {
            width: 50px;
            height: 50px;
            top: 10%;
            right: 20%;
            animation-delay: 4s;
        }

        .bg-element:nth-child(4) {
            width: 90px;
            height: 90px;
            bottom: 15%;
            left: 25%;
            animation-delay: 1s;
        }

        /* 数据点装饰 */
        .data-points {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .data-point {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #667eea;
            border-radius: 50%;
            animation: pulse 3s ease-in-out infinite;
            opacity: 0.7;
        }

        .data-point:nth-child(1) {
            top: 30%;
            left: 30%;
            animation-delay: 0s;
        }

        .data-point:nth-child(2) {
            top: 45%;
            right: 35%;
            animation-delay: 1s;
        }

        .data-point:nth-child(3) {
            bottom: 40%;
            left: 40%;
            animation-delay: 2s;
        }

        .data-point:nth-child(4) {
            bottom: 30%;
            right: 30%;
            animation-delay: 0.5s;
        }

        /* 中心Logo容器 */
        .logo-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 
                0 15px 40px rgba(0, 0, 0, 0.1),
                0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            transform: translate(-50%, -50%) scale(1.03);
            box-shadow: 
                0 20px 50px rgba(0, 0, 0, 0.15),
                0 8px 20px rgba(0, 0, 0, 0.08);
            border-color: #e2e8f0;
        }

        .center-logo {
            width: 100px;
            height: auto;
            display: block;
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
        }

        /* 动画效果 */
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) scale(1);
                opacity: 0.8;
            }
            50% {
                transform: translateY(-20px) scale(1.1);
                opacity: 1;
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.6;
                transform: scale(1);
            }
            50% {
                opacity: 1;
                transform: scale(1.5);
            }
        }

        /* 使命愿景区域 */
        .mission-section {
            padding: 120px 0;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            position: relative;
        }

        .mission-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0) 0%, rgba(248, 250, 252, 1) 100%);
            z-index: 1;
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 50px;
            position: relative;
            z-index: 2;
        }

        .mission-card {
            background: white;
            padding: 50px 40px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }

        .mission-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .mission-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12);
            border-color: #667eea;
        }

        .mission-card:hover::before {
            left: 100%;
        }

        .mission-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .mission-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }

        .mission-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 20px;
        }

        .mission-card p {
            color: #4a5568;
            line-height: 1.6;
            font-size: 1rem;
        }

        /* 团队介绍区域 */
        .team-section {
            padding: 100px 0;
            background: white;
        }

        .section-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #718096;
            margin-bottom: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .team-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .team-avatar {
            width: 100%;
            height: 280px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-bottom: 1px solid #e2e8f0;
        }

        .team-avatar svg {
            width: 140px;
            height: 140px;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.1));
        }

        .feature-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.1));
        }

        .feature-icon svg {
            width: 80px;
            height: 80px;
            color: white;
            filter: none;
        }

        .team-info {
            padding: 30px;
            text-align: center;
        }

        .team-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .team-role {
            font-size: 1rem;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .team-desc {
            color: #4a5568;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* 发展历程区域 */
        .timeline-section {
            padding: 120px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
        }

        .timeline-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="80" height="80" viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23667eea" fill-opacity="0.02"><circle cx="40" cy="40" r="1"/></g></svg>') repeat;
            opacity: 0.6;
        }

        .timeline {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
            transform: translateX(-50%);
        }

        .timeline-item {
            display: flex;
            justify-content: flex-end;
            padding-right: 50%;
            position: relative;
            margin-bottom: 60px;
        }

        .timeline-item:nth-child(even) {
            justify-content: flex-start;
            padding-left: 50%;
            padding-right: 0;
        }

        .timeline-content {
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            width: 90%;
            position: relative;
            transition: all 0.3s ease;
        }

        .timeline-content:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            border-color: #667eea;
        }

        .timeline-item:nth-child(even) .timeline-content {
            margin-left: 20px;
        }

        .timeline-item:nth-child(odd) .timeline-content {
            margin-right: 20px;
        }

        .timeline-dot {
            position: absolute;
            left: 50%;
            top: 30px;
            width: 16px;
            height: 16px;
            background: #667eea;
            border-radius: 50%;
            transform: translateX(-50%);
            border: 4px solid white;
            box-shadow: 0 0 0 2px #e2e8f0;
        }

        .timeline-date {
            font-size: 0.9rem;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .timeline-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .timeline-desc {
            color: #4a5568;
            line-height: 1.6;
        }

        /* 联系信息区域 */
        .contact-info-section {
            padding: 120px 0;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
        }

        .contact-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .contact-info-group {
            background: #f8fafc;
            border-radius: 20px;
            padding: 40px 30px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .contact-info-group:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
            border-color: #667eea;
            background: white;
        }

        .contact-group-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .contact-group-title svg {
            color: #667eea;
        }

        .contact-info-items {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .contact-item-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .contact-item-value {
            font-size: 1rem;
            color: #2d3748;
            line-height: 1.5;
        }

        .contact-item-note {
            font-size: 0.85rem;
            color: #718096;
            font-style: italic;
        }

        .contact-phone,
        .contact-email {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .contact-phone:hover,
        .contact-email:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .wechat-group {
            text-align: center;
        }

        .wechat-qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .wechat-qr-image {
            width: 140px;
            height: 140px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border: 3px solid #fff;
        }

        .wechat-qr-text {
            font-size: 0.9rem;
            color: #718096;
            margin: 0;
        }

        /* 联系我们区域 */
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
        @media (max-width: 1200px) {
            .intro-grid {
                gap: 60px;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2.8rem;
            }

            .page-subtitle {
                font-size: 1.2rem;
            }

            .intro-grid {
                grid-template-columns: 1fr;
                gap: 60px;
            }

            .intro-grid .intro-image {
                order: -1;
                padding: 20px;
            }

            .intro-visual-card {
                height: 300px;
                max-width: 100%;
            }

            .center-logo {
                width: 70px;
            }

            .logo-container {
                padding: 25px;
            }

            .intro-visual-card {
                height: 300px;
            }

            .bg-element {
                animation: none;
            }

            .data-point {
                animation: none;
            }

            .mission-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .timeline::before {
                left: 20px;
            }

            .timeline-item,
            .timeline-item:nth-child(even) {
                justify-content: flex-start;
                padding-left: 60px;
                padding-right: 0;
            }

            .timeline-content {
                width: 100%;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .timeline-dot {
                left: 20px;
                transform: none;
            }

            .contact-buttons {
                flex-direction: column;
                align-items: center;
            }

            .contact-button {
                width: 100%;
                max-width: 300px;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .contact-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 20px;
            }
            
            .page-hero {
                padding: 100px 0 80px;
            }
            
            .intro-section,
            .mission-section,
            .team-section,
            .timeline-section,
            .contact-info-section,
            .contact-section {
                padding: 80px 0;
            }

            .contact-info-grid {
                grid-template-columns: 1fr;
                gap: 30px;
                margin-top: 40px;
            }

            .contact-info-group {
                padding: 30px 20px;
            }

            .contact-group-title {
                font-size: 1.1rem;
                margin-bottom: 20px;
                padding-bottom: 12px;
            }



            .mission-card {
                padding: 40px 30px;
            }

            .intro-stats {
                flex-direction: column;
                gap: 20px;
                margin-top: 30px;
                padding-top: 25px;
            }

            .stat-number {
                font-size: 2rem;
            }

            .timeline-content {
                padding: 25px;
            }

            .contact-info-group {
                padding: 30px 25px;
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
                    <span>关于在行云</span>
                </div>
                <h1 class="page-title">让开发更简单</h1>
                <p class="page-subtitle">
                    我们专注为独立开发者和团队提供优质的BaaS服务<br>
                    让您专注于产品创新，无需为后端基础设施操心
                </p>
            </div>
        </section>

        <!-- 公司介绍 -->
        <section class="intro-section">
            <div class="container">
                <div class="intro-grid">
                    <div class="intro-content">
                        <h2>专为开发者而生的BaaS平台</h2>
                        <p>在行云是一个专门为独立开发者和小团队打造的后端即服务(BaaS)平台。我们深知开发者在构建应用时面临的挑战，因此致力于提供简单易用、功能完善的后端服务。</p>
                        <p>通过在行云，您可以快速集成用户认证、数据存储、支付处理、消息推送等功能，大大缩短产品开发周期，让您能够专注于产品的核心价值和用户体验。</p>
                        <p>我们相信，优秀的工具能够释放开发者的创造力。在行云不仅仅是一个技术平台，更是您实现创意的可靠伙伴。</p>
                    </div>
                    <div class="intro-image">
                        <div class="intro-visual-card">
                            <div class="visual-background">
                                <div class="bg-element"></div>
                                <div class="bg-element"></div>
                                <div class="bg-element"></div>
                                <div class="bg-element"></div>
                            </div>
                            <div class="data-points">
                                <div class="data-point"></div>
                                <div class="data-point"></div>
                                <div class="data-point"></div>
                                <div class="data-point"></div>
                            </div>
                            <div class="logo-container">
                                <img src="{{ asset('images/logo-mini.png') }}" alt="在行云 Logo" class="center-logo" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 使命愿景 -->
        <section class="mission-section">
            <div class="container">
                <h2 class="section-title">我们的使命与愿景</h2>
                <div class="mission-grid">
                    <div class="mission-card">
                        <div class="mission-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                <path d="M2 17l10 5 10-5"/>
                                <path d="M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                        <h3>使命</h3>
                        <p>降低技术门槛，让每个有想法的开发者都能轻松构建出色的应用，专注于产品创新而非基础设施搭建。</p>
                    </div>
                    <div class="mission-card">
                        <div class="mission-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                        </div>
                        <h3>愿景</h3>
                        <p>成为开发者首选的BaaS平台，构建一个繁荣的开发者生态系统，推动中国移动互联网行业的创新发展。</p>
                    </div>
                    <div class="mission-card">
                        <div class="mission-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <h3>价值观</h3>
                        <p>开放、简单、可靠。我们坚信技术应该服务于人，让复杂的事情变得简单，让开发变得更有乐趣。</p>
                    </div>
                </div>
            </div>
        </section>



        <!-- 发展历程 -->
        <section id="timeline-section" class="timeline-section">
            <div class="container">
                <h2 class="section-title">发展历程</h2>
                <div class="timeline">
                                         <div class="timeline-item">
                         <div class="timeline-content">
                             <div class="timeline-date">2023年1月</div>
                             <h3 class="timeline-title">开启APP创业之旅</h3>
                             <p class="timeline-desc">团队开始移动应用创业，在开发过程中逐渐认识到独立开发者在后端开发上面临的挑战和痛点。</p>
                         </div>
                         <div class="timeline-dot"></div>
                     </div>
                     <div class="timeline-item">
                         <div class="timeline-content">
                             <div class="timeline-date">2023年6月</div>
                             <h3 class="timeline-title">抽象共用需求</h3>
                             <p class="timeline-desc">开始为公司内部多个APP项目抽象共用的后端需求，发现用户认证、数据存储等功能存在大量重复开发。</p>
                         </div>
                         <div class="timeline-dot"></div>
                     </div>
                     <div class="timeline-item">
                         <div class="timeline-content">
                             <div class="timeline-date">2024年1月</div>
                             <h3 class="timeline-title">内部平台统一</h3>
                             <p class="timeline-desc">公司内部APP全面迁移到统一的后端平台，大幅提升了开发效率，验证了BaaS模式的可行性。</p>
                         </div>
                         <div class="timeline-dot"></div>
                     </div>
                     <div class="timeline-item">
                         <div class="timeline-content">
                             <div class="timeline-date">2024年8月</div>
                             <h3 class="timeline-title">转向BaaS平台</h3>
                             <p class="timeline-desc">决定将内部成功的后端解决方案转型为面向所有独立开发者的在行云BaaS平台，开始产品化改造。</p>
                         </div>
                         <div class="timeline-dot"></div>
                     </div>
                     <div class="timeline-item">
                         <div class="timeline-content">
                             <div class="timeline-date">2025年1月</div>
                             <h3 class="timeline-title">内测发布</h3>
                             <p class="timeline-desc">在行云BaaS平台内测版正式发布，采用邀请制模式，与优质开发者深度合作，持续优化产品体验。</p>
                         </div>
                         <div class="timeline-dot"></div>
                     </div>
                     <div class="timeline-item">
                         <div class="timeline-content">
                             <div class="timeline-date">2025年6月</div>
                             <h3 class="timeline-title">正式对外发布</h3>
                             <p class="timeline-desc">平台将正式面向所有开发者开放，提供完整的BaaS服务，助力更多独立开发者实现创意。</p>
                         </div>
                         <div class="timeline-dot"></div>
                     </div>
                </div>
            </div>
        </section>

        <!-- 联系我们 -->
        <section class="contact-info-section" id="contact-info-section">
            <div class="container">
                <h2 class="section-title">联系我们</h2>
                <p class="section-subtitle">欢迎随时与我们取得联系，我们将竭诚为您提供专业的技术支持与服务</p>
                
                <div class="contact-info-grid">
                    <!-- 办公地址 -->
                    <div class="contact-info-group">
                        <h3 class="contact-group-title">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            办公地址
                        </h3>
                        <div class="contact-info-items">
                            <div class="contact-info-item">
                                <div class="contact-item-label">北京总部</div>
                                <div class="contact-item-value">北京市海淀区永澄北路2号院1号楼4层A4516号</div>
                                <div class="contact-item-note">技术研发中心</div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-item-label">长沙分部</div>
                                <div class="contact-item-value">湖南省长沙市天心区新开铺街道万科紫台C21栋708</div>
                                <div class="contact-item-note">运营服务中心</div>
                            </div>
                        </div>
                    </div>

                    <!-- 联系方式 -->
                    <div class="contact-info-group">
                        <h3 class="contact-group-title">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            联系电话
                        </h3>
                        <div class="contact-info-items">
                            <div class="contact-info-item">
                                <div class="contact-item-value">
                                    <a href="tel:19973125308" class="contact-phone">199-7312-5308</a>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-item-value">
                                    <a href="tel:15611704771" class="contact-phone">156-1170-4771</a>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-item-note">工作时间：9:00-18:00</div>
                            </div>
                        </div>
                    </div>

                    <!-- 邮箱联系 -->
                    <div class="contact-info-group">
                        <h3 class="contact-group-title">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            邮箱联系
                        </h3>
                        <div class="contact-info-items">
                            <div class="contact-info-item">
                                <div class="contact-item-label">商务合作</div>
                                <div class="contact-item-value">
                                    <a href="mailto:business@zaihangyun.com" class="contact-email">business@zaihangyun.com</a>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-item-label">技术支持</div>
                                <div class="contact-item-value">
                                    <a href="mailto:support@zaihangyun.com" class="contact-email">support@zaihangyun.com</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 企业微信 -->
                    <div class="contact-info-group wechat-group">
                        <h3 class="contact-group-title">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            企业微信
                        </h3>
                        <div class="wechat-qr-container">
                            <img src="{{ asset('images/genqrcode.png') }}" alt="企业微信二维码" class="wechat-qr-image" />
                            <p class="wechat-qr-text">扫码添加企业微信</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 行动呼吁 -->
        <section class="contact-section">
            <div class="container">
                <h2 class="contact-title">加入我们的开发者社区</h2>
                <p class="contact-description">
                    如果您对在行云感兴趣，或者有任何问题和建议，<br>
                    欢迎随时联系我们，让我们一起构建更好的开发者生态。
                </p>
                <div class="contact-buttons">
                    <a href="/console/auth/login" class="contact-button">立即开始</a>
                    <a href="#contact-info-section" class="contact-button secondary">联系我们</a>
                </div>
            </div>
        </section>
    </main>

    @include('layouts.footer')
    @include('layouts.scripts')
</body>
</html> 