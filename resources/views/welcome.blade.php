<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在行云 - APP开发者的后端难题终结者</title>
    <meta name="description" content="专为APP开发者打造的轻量级BaaS平台，无需写一行后端代码，即可拥有完整的用户、支付、内容管理系统">
    <meta name="keywords" content="在行云,BaaS,后端服务,APP开发,独立开发者,无服务器,云服务">
    <link rel="icon" href="/favicon.ico">
  <style>
        * {
        margin: 0;
        padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        /* 导航栏 */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 15px 0;
            transition: all 0.3s ease;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            height: 32px;
            width: auto;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 600;
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #4086F5;
        }

        .nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
            border-radius: 1px;
        }

        .cta-button {
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(64, 134, 245, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(64, 134, 245, 0.4);
            background: linear-gradient(135deg, #3574E3 0%, #15C7BB 100%);
        }

        /* Hero Section */
        .hero {
        min-height: 100vh;
            background: linear-gradient(135deg, #4086F5 0%, #6B9BF7 50%, #1AE2D6 100%);
        display: flex;
            align-items: center;
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="dots" width="40" height="40" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23dots)"/></svg>');
            opacity: 0.6;
        }

        .hero-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 100px;
            align-items: center;
            padding: 0 40px;
            position: relative;
            z-index: 1;
        }

        .hero-content {
            color: white;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes scrollRight {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-340px * 6)); }
        }

        @keyframes scrollLeft {
            0% { transform: translateX(calc(-340px * 6)); }
            100% { transform: translateX(0); }
        }

        .scroll-container {
            animation-timing-function: linear;
        }

        .scroll-container:hover {
            animation-play-state: paused;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes dataFlow {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }

        @keyframes particle1 {
            0% { transform: translate(0, 0) scale(1); opacity: 0; }
            10% { opacity: 1; }
            50% { transform: translate(100px, -50px) scale(1.2); opacity: 0.8; }
            90% { opacity: 1; }
            100% { transform: translate(200px, -100px) scale(0.8); opacity: 0; }
        }

        @keyframes particle2 {
            0% { transform: translate(0, 0) scale(1); opacity: 0; }
            10% { opacity: 1; }
            50% { transform: translate(-80px, 60px) scale(1.1); opacity: 0.9; }
            90% { opacity: 1; }
            100% { transform: translate(-160px, 120px) scale(0.9); opacity: 0; }
        }

        @keyframes particle3 {
            0% { transform: translate(0, 0) scale(1); opacity: 0; }
            10% { opacity: 1; }
            50% { transform: translate(120px, -80px) scale(1.3); opacity: 0.7; }
            90% { opacity: 1; }
            100% { transform: translate(240px, -160px) scale(0.7); opacity: 0; }
        }

        .hero h1 {
            font-size: 4.5rem;
            font-weight: 800;
            margin-bottom: 30px;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .hero .subtitle {
            font-size: 1.4rem;
            margin-bottom: 50px;
            opacity: 0.95;
            line-height: 1.6;
            font-weight: 400;
        }

        .hero-visual {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            height: 700px;
        }

        .hero-svg {
        width: 100%;
            max-width: 700px;
            height: 100%;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.1));
        }

        .hero-buttons {
        display: flex;
            gap: 20px;
        justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary, .btn-secondary {
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background: white;
            color: #4086F5;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
            color: #3574E3;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background: #f8fafc;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title p {
            font-size: 1.2rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-bottom: 80px;
        }

        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
            border-radius: 20px;
            display: flex;
        align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            color: white;
            box-shadow: 0 10px 30px rgba(64, 134, 245, 0.3);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .feature-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: rotate(45deg);
            transition: all 0.6s ease;
            opacity: 0;
        }

        .feature-card:hover .feature-icon::before {
            animation: shimmer 1.5s ease-in-out;
            opacity: 1;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .feature-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Product Modules - 大卡片重设计 */
        .modules {
            padding: 120px 0;
            background: linear-gradient(135deg, #F8FAFC 0%, #EDF2F7 100%);
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .module-card {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            border: 1px solid #E5E7EB;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
        }

        .module-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(64, 134, 245, 0.15);
            border-color: #4086F5;
        }

        .module-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
            border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
            margin-bottom: 24px;
            font-size: 32px;
            color: white;
            box-shadow: 0 8px 20px rgba(64, 134, 245, 0.3);
        }

        .module-card h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 16px;
            color: #1F2937;
        }

        .module-card p {
            color: #6B7280;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .module-features {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .module-features li {
            color: #4B5563;
            font-size: 0.9rem;
            margin-bottom: 8px;
            position: relative;
            padding-left: 20px;
        }

        .module-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #4086F5;
            font-weight: bold;
        }

        /* Pricing Section */
        .pricing {
            padding: 100px 0;
            background: #f8fafc;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }

        .pricing-card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
        text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 2px solid #eee;
        transition: all 0.3s ease;
            position: relative;
        }

        .pricing-card.featured {
            border-color: #4086F5;
            transform: scale(1.05);
        }

        .pricing-card.featured::before {
            content: '推荐';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
            color: white;
            padding: 5px 20px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .pricing-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .pricing-card .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #4086F5;
        margin-bottom: 20px;
        }

        .pricing-card .price span {
            font-size: 1rem;
            color: #666;
        }

        .pricing-features {
            list-style: none;
            margin-bottom: 30px;
        }

        .pricing-features li {
            padding: 8px 0;
            color: #666;
        }

        .pricing-features li:before {
            content: '✓';
            color: #4086F5;
        font-weight: bold;
            margin-right: 10px;
        }

        /* Contact Section */
        .contact {
            padding: 100px 0;
            background: white;
            text-align: center;
        }

        .contact-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .wechat-qr {
        width: 200px;
        height: 200px;
            margin: 30px auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* Footer - 丰富页脚 */
        .footer {
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            color: white;
            padding: 60px 0 30px;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #4086F5, #1AE2D6, transparent);
    }
    
    .footer-content {
            max-width: 1400px;
        margin: 0 auto;
            padding: 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr auto;
            gap: 480px;
            margin-bottom: 40px;
            align-items: start;
        }

        .footer-nav-group {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 60px;
            justify-self: end;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .footer-brand img {
            height: 40px;
            filter: brightness(0) invert(1);
        }

        .footer-brand-text {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .footer-desc {
            color: #9CA3AF;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .footer-section h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
            position: relative;
            padding-bottom: 8px;
        }

        .footer-section h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 2px;
            background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%);
            border-radius: 1px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #9CA3AF;
            text-decoration: none;
            transition: all 0.3s ease;
        font-size: 14px;
            display: inline-block;
        }

        .footer-links a:hover {
            color: #4086F5;
            transform: translateX(5px);
        }

        .footer-bottom {
            border-top: 1px solid #374151;
            padding-top: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .footer-bottom p {
        margin: 0;
            color: #9CA3AF;
            font-size: 14px;
    }
    
        .footer-bottom a {
            color: #4086F5;
        text-decoration: none;
    }
    
        .footer-bottom a:hover {
        text-decoration: underline;
    }
    
        .footer-social {
            display: flex;
            gap: 16px;
        }

        .footer-social a {
            width: 36px;
            height: 36px;
            background: #374151;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9CA3AF;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-social a:hover {
            background: #4086F5;
            color: white;
        }

        /* App Showcase Animations */

        .app-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1) !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero .subtitle {
                font-size: 1.1rem;
            }

            .hero-buttons {
            flex-direction: column;
                align-items: center;
            }

            .nav-links {
                display: none;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .modules-grid {
                grid-template-columns: 1fr;
            }

            .apps-row {
                flex-direction: column !important;
                align-items: center !important;
                animation: none !important;
            }

            .app-card {
                margin-bottom: 20px !important;
            }

            .pricing-card.featured {
                transform: none;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .footer-nav-group {
                grid-template-columns: 1fr;
            gap: 30px;
                justify-self: start;
            }

            .footer-section h4::after {
                width: 50px;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .footer-bottom p {
            font-size: 12px;
        }
    }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }

        @keyframes dataFlow {
            0% { transform: translateX(-100%); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateX(100%); opacity: 0; }
        }

        @keyframes cloudPulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.8; }
            50% { transform: translate(-50%, -50%) scale(1.05); opacity: 1; }
        }

        @keyframes particle1 {
            0% { transform: translate(0, 0) scale(1); opacity: 0.6; }
            25% { transform: translate(40px, -30px) scale(1.3); opacity: 1; }
            50% { transform: translate(-20px, -60px) scale(0.7); opacity: 0.4; }
            75% { transform: translate(-50px, -20px) scale(1.1); opacity: 0.8; }
            100% { transform: translate(0, 0) scale(1); opacity: 0.6; }
        }

        @keyframes particle2 {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); opacity: 0.5; }
            30% { transform: translate(-35px, 25px) rotate(108deg) scale(1.4); opacity: 1; }
            60% { transform: translate(30px, -35px) rotate(216deg) scale(0.8); opacity: 0.7; }
            100% { transform: translate(0, 0) rotate(360deg) scale(1); opacity: 0.5; }
        }

        @keyframes particle3 {
            0% { transform: translate(0, 0) scale(1); opacity: 0.8; }
            20% { transform: translate(25px, -45px) scale(0.6); opacity: 0.4; }
            40% { transform: translate(-30px, -25px) scale(1.5); opacity: 1; }
            60% { transform: translate(35px, 15px) scale(0.9); opacity: 0.6; }
            80% { transform: translate(-15px, 30px) scale(1.3); opacity: 0.9; }
            100% { transform: translate(0, 0) scale(1); opacity: 0.8; }
        }

        /* APP专用科技感动画效果 */
        @keyframes gridFlow {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }

        @keyframes serverPulse {
            0%, 100% { 
                transform: translate(-50%, -50%) scale(1); 
                box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            }
            50% { 
                transform: translate(-50%, -50%) scale(1.02); 
                box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            }
        }

        @keyframes statusLight {
            0%, 100% { 
                opacity: 0.8; 
                transform: scale(1); 
            }
            50% { 
                opacity: 1; 
                transform: scale(1.1); 
            }
        }

        @keyframes dataProcess {
            0%, 100% { 
                opacity: 0.5; 
                transform: scaleY(0.6); 
            }
            50% { 
                opacity: 1; 
                transform: scaleY(1.2); 
            }
        }

        @keyframes appFloat {
            0%, 100% { 
                transform: translateY(0px); 
            }
            50% { 
                transform: translateY(-8px); 
            }
        }

        @keyframes dataFlow {
            0% { 
                opacity: 0.3; 
                transform: scaleX(0.5); 
            }
            50% { 
                opacity: 1; 
                transform: scaleX(1); 
            }
            100% { 
                opacity: 0.3; 
                transform: scaleX(0.5); 
            }
        }

        @keyframes dataUpload {
            0% { 
                transform: translateY(20px); 
                opacity: 0; 
            }
            50% { 
                opacity: 1; 
            }
            100% { 
                transform: translateY(-20px); 
                opacity: 0; 
            }
        }

        @keyframes dataDownload {
            0% { 
                transform: translateY(-20px); 
                opacity: 0; 
            }
            50% { 
                opacity: 1; 
            }
            100% { 
                transform: translateY(20px); 
                opacity: 0; 
            }
        }

        @keyframes serviceOrbit {
            0% { 
                transform: rotate(0deg) translateX(60px) rotate(0deg); 
                opacity: 0.7;
            }
            50% {
                opacity: 1;
            }
            100% { 
                transform: rotate(360deg) translateX(60px) rotate(-360deg); 
                opacity: 0.7;
            }
        }

        @keyframes floatParticle {
            0%, 100% { 
                opacity: 0.4; 
                transform: translateY(0px) scale(0.8); 
            }
            25% { 
                opacity: 0.8; 
                transform: translateY(-10px) scale(1); 
            }
            50% { 
                opacity: 1; 
                transform: translateY(-15px) scale(1.2); 
            }
            75% { 
                opacity: 0.8; 
                transform: translateY(-10px) scale(1); 
            }
        }

        /* 响应式设计 */
        @media (max-width: 768px) {
            .nav-container {
                max-width: 100%;
                padding: 0 20px;
            }
            
            .hero-container {
                grid-template-columns: 1fr !important;
                gap: 40px !important;
                text-align: center;
                max-width: 100%;
                padding: 0 20px;
            }
            
            .hero h1 {
                font-size: 3rem !important;
            }
            
            .hero-stats {
                justify-content: center !important;
            }
            
            .hero-visual {
                height: 400px !important;
            }
            
            .container {
                max-width: 100%;
                padding: 0 20px;
            }
            
            /* 为什么选择在行云 - 移动端适配 */
            .features {
                padding: 60px 0 !important;
            }
            
            .features h2 {
                font-size: 2.5rem !important;
            }
            
            .features > div {
                padding: 0 20px !important;
            }
            
            /* 对比式设计移动端 */
            .features > div > div:first-of-type {
                grid-template-columns: 1fr !important;
                gap: 40px !important;
            }
            
            /* VS分隔符移动端 */
            .features > div > div:first-of-type > div:nth-child(2) {
                order: 2;
                margin: 20px 0;
            }
            
            /* 传统方式移动端 */
            .features > div > div:first-of-type > div:first-child {
                order: 1;
            }
            
            /* 在行云方式移动端 */
            .features > div > div:first-of-type > div:last-child {
                order: 3;
            }
            
            /* 核心优势总结移动端 */
            .features > div > div:last-child {
                padding: 30px 20px !important;
                margin: 0 -20px;
            }
            
            .features > div > div:last-child h3 {
                font-size: 2rem !important;
            }
            
            .features > div > div:last-child p {
                font-size: 1.1rem !important;
            }
            
            .features > div > div:last-child > div > div:last-child {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 30px !important;
            }
            
            .features > div > div:last-child > div > div:last-child > div > div:first-child {
                font-size: 2.5rem !important;
            }
        }

        .animate-on-scroll {
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
    }
  </style>
</head>
<body>
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
        </div>
    </nav>

    <!-- Hero Section - Bold Redesign -->
    <section class="hero" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #4086F5 100%); position: relative; overflow: hidden; display: flex; align-items: center; padding: 140px 0 100px;">
        <!-- 动态背景元素 -->
        <div class="hero-bg-elements" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;">
            <!-- 大型几何图形 -->
            <div style="position: absolute; top: -200px; right: -200px; width: 600px; height: 600px; background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(64,134,245,0.2)); border-radius: 50%; animation: float 6s ease-in-out infinite;"></div>
            <div style="position: absolute; bottom: -300px; left: -300px; width: 800px; height: 800px; background: linear-gradient(45deg, rgba(26,226,214,0.1), rgba(255,255,255,0.05)); border-radius: 50%; animation: float 8s ease-in-out infinite reverse;"></div>
            
            <!-- 网格图案 -->
            <svg style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1;" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                        <path d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>
        </div>

        <div class="hero-container" style="max-width: 1400px; margin: 0 auto; padding: 0 40px; display: grid; grid-template-columns: 1.2fr 1fr; gap: 80px; align-items: center; position: relative; z-index: 1;">
            <div class="hero-content" style="color: white;">
                <!-- 新的现代徽章 -->
                <div class="hero-badge" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.1); color: white; padding: 12px 20px; border-radius: 30px; font-size: 14px; font-weight: 500; margin-bottom: 30px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span>专为独立开发者设计</span>
                </div>

                <h1 style="font-size: 4.5rem; font-weight: 900; line-height: 1.1; margin-bottom: 30px; letter-spacing: -0.02em;">
                    不写后端代码<br>
                    <span style="background: linear-gradient(135deg, #1AE2D6 0%, #FFFFFF 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">也能快速上线</span>
                </h1>

                <p style="font-size: 1.4rem; line-height: 1.6; margin-bottom: 40px; opacity: 0.95; max-width: 500px;">
                    专为APP开发者打造的轻量级BaaS平台，提供完整的后端服务支持，让你专注于打造出色的用户体验
                </p>

                <div class="hero-stats" style="display: flex; gap: 40px; margin-bottom: 50px;">
                    <div>
                        <div style="font-size: 2rem; font-weight: 700; color: #1AE2D6;">即开即用</div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">注册即可使用</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: 700; color: #1AE2D6;">0学习成本</div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">简单易用</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: 700; color: #1AE2D6;">99.9%</div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">服务可用性</div>
                    </div>
                </div>

                <div class="hero-buttons" style="display: flex; gap: 20px; align-items: center;">
                    <a href="/login" style="background: linear-gradient(135deg, #1AE2D6 0%, #FFFFFF 100%); color: #4086F5; padding: 16px 32px; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 16px; box-shadow: 0 10px 30px rgba(26,226,214,0.3); transition: all 0.3s ease; border: none;">
                        开启免费体验 →
                    </a>
                    <a href="#features" style="color: white; text-decoration: none; font-weight: 600; padding: 16px 24px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50px; backdrop-filter: blur(10px); transition: all 0.3s ease;">
                        了解更多
                    </a>
                </div>
            </div>

            <!-- 专为APP开发的科技感数据流插画 -->
            <div class="hero-visual" style="position: relative; height: 700px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <!-- 科技背景网格 -->
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: linear-gradient(rgba(64,134,245,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(64,134,245,0.1) 1px, transparent 1px); background-size: 40px 40px; animation: gridFlow 15s linear infinite;"></div>
                
                <!-- 主要场景 -->
                <div class="main-visual" style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                    
                    <!-- 中央BaaS云服务器 -->
                    <div style="position: relative; width: 700px; height: 550px;">
                        
                        <!-- 核心云服务器 -->
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 280px; height: 200px; background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.25)); border-radius: 25px; backdrop-filter: blur(20px); border: 2px solid rgba(255,255,255,0.3); box-shadow: 0 25px 50px rgba(0,0,0,0.2); animation: serverPulse 4s ease-in-out infinite;">
                            
                            <!-- 服务器标识 -->
                            <div style="position: absolute; top: 30px; left: 50%; transform: translateX(-50%); display: flex; align-items: center; gap: 12px;">
                                <div style="width: 16px; height: 16px; background: linear-gradient(135deg, #4086F5, #1AE2D6); border-radius: 50%; animation: statusLight 2s ease-in-out infinite;"></div>
                                <span style="color: rgba(255,255,255,0.9); font-size: 18px; font-weight: 600;">BaaS 云服务</span>
                            </div>
                            
                            <!-- 数据处理指示器 -->
                            <div style="position: absolute; bottom: 35px; left: 50%; transform: translateX(-50%); display: flex; gap: 6px;">
                                <div style="width: 8px; height: 20px; background: linear-gradient(180deg, #4086F5, #1AE2D6); border-radius: 4px; animation: dataProcess 1.5s ease-in-out infinite;"></div>
                                <div style="width: 8px; height: 28px; background: linear-gradient(180deg, #10b981, #059669); border-radius: 4px; animation: dataProcess 1.5s ease-in-out infinite 0.2s;"></div>
                                <div style="width: 8px; height: 16px; background: linear-gradient(180deg, #f59e0b, #d97706); border-radius: 4px; animation: dataProcess 1.5s ease-in-out infinite 0.4s;"></div>
                                <div style="width: 8px; height: 24px; background: linear-gradient(180deg, #8b5cf6, #7c3aed); border-radius: 4px; animation: dataProcess 1.5s ease-in-out infinite 0.6s;"></div>
                            </div>
                        </div>

                        <!-- 多个手机APP终端 -->
                        <!-- iOS APP -->
                        <div style="position: absolute; top: 8%; left: 8%; width: 110px; height: 110px; background: linear-gradient(135deg, rgba(64,134,245,0.3), rgba(64,134,245,0.5)); border-radius: 25px; backdrop-filter: blur(15px); border: 2px solid rgba(64,134,245,0.6); box-shadow: 0 20px 40px rgba(64,134,245,0.3); display: flex; align-items: center; justify-content: center; animation: appFloat 4s ease-in-out infinite;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="rgba(255,255,255,0.9)">
                                    <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
                                </svg>
                                <span style="color: rgba(255,255,255,0.8); font-size: 11px; font-weight: 600;">iOS APP</span>
                            </div>
                        </div>

                        <!-- Android APP -->
                        <div style="position: absolute; top: 8%; right: 8%; width: 110px; height: 110px; background: linear-gradient(135deg, rgba(16,185,129,0.3), rgba(16,185,129,0.5)); border-radius: 25px; backdrop-filter: blur(15px); border: 2px solid rgba(16,185,129,0.6); box-shadow: 0 20px 40px rgba(16,185,129,0.3); display: flex; align-items: center; justify-content: center; animation: appFloat 4s ease-in-out infinite 1s;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="rgba(255,255,255,0.9)">
                                    <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
                                </svg>
                                <span style="color: rgba(255,255,255,0.8); font-size: 11px; font-weight: 600;">Android</span>
                            </div>
                        </div>

                        <!-- React Native APP -->
                        <div style="position: absolute; bottom: 8%; left: 8%; width: 110px; height: 110px; background: linear-gradient(135deg, rgba(26,226,214,0.3), rgba(26,226,214,0.5)); border-radius: 25px; backdrop-filter: blur(15px); border: 2px solid rgba(26,226,214,0.6); box-shadow: 0 20px 40px rgba(26,226,214,0.3); display: flex; align-items: center; justify-content: center; animation: appFloat 4s ease-in-out infinite 2s;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="rgba(255,255,255,0.9)">
                                    <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
                                </svg>
                                <span style="color: rgba(255,255,255,0.8); font-size: 11px; font-weight: 600;">RN APP</span>
                            </div>
                        </div>

                        <!-- Flutter APP -->
                        <div style="position: absolute; bottom: 8%; right: 8%; width: 110px; height: 110px; background: linear-gradient(135deg, rgba(139,92,246,0.3), rgba(139,92,246,0.5)); border-radius: 25px; backdrop-filter: blur(15px); border: 2px solid rgba(139,92,246,0.6); box-shadow: 0 20px 40px rgba(139,92,246,0.3); display: flex; align-items: center; justify-content: center; animation: appFloat 4s ease-in-out infinite 3s;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="rgba(255,255,255,0.9)">
                                    <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
                                </svg>
                                <span style="color: rgba(255,255,255,0.8); font-size: 11px; font-weight: 600;">Flutter</span>
                            </div>
                        </div>

                        <!-- 数据流连接线 -->
                        <!-- 从iOS到服务器 -->
                        <div style="position: absolute; top: 22%; left: 22%; width: 140px; height: 3px; background: linear-gradient(90deg, rgba(64,134,245,0.8), rgba(255,255,255,0.3)); border-radius: 2px; animation: dataFlow 3s ease-in-out infinite; transform: rotate(30deg); box-shadow: 0 0 10px rgba(64,134,245,0.4);"></div>
                        
                        <!-- 从Android到服务器 -->
                        <div style="position: absolute; top: 22%; right: 22%; width: 140px; height: 3px; background: linear-gradient(270deg, rgba(16,185,129,0.8), rgba(255,255,255,0.3)); border-radius: 2px; animation: dataFlow 3s ease-in-out infinite 0.5s; transform: rotate(-30deg); box-shadow: 0 0 10px rgba(16,185,129,0.4);"></div>
                        
                        <!-- 从RN到服务器 -->
                        <div style="position: absolute; bottom: 22%; left: 22%; width: 140px; height: 3px; background: linear-gradient(90deg, rgba(26,226,214,0.8), rgba(255,255,255,0.3)); border-radius: 2px; animation: dataFlow 3s ease-in-out infinite 1s; transform: rotate(-30deg); box-shadow: 0 0 10px rgba(26,226,214,0.4);"></div>
                        
                        <!-- 从Flutter到服务器 -->
                        <div style="position: absolute; bottom: 22%; right: 22%; width: 140px; height: 3px; background: linear-gradient(270deg, rgba(139,92,246,0.8), rgba(255,255,255,0.3)); border-radius: 2px; animation: dataFlow 3s ease-in-out infinite 1.5s; transform: rotate(30deg); box-shadow: 0 0 10px rgba(139,92,246,0.4);"></div>

                        <!-- 数据流粒子效果 -->
                        <!-- 上行数据流 -->
                        <div style="position: absolute; top: 28%; left: 32%; width: 6px; height: 6px; background: rgba(64,134,245,0.8); border-radius: 50%; animation: dataUpload 2s linear infinite; box-shadow: 0 0 12px rgba(64,134,245,0.6);"></div>
                        <div style="position: absolute; top: 28%; right: 32%; width: 6px; height: 6px; background: rgba(16,185,129,0.8); border-radius: 50%; animation: dataUpload 2s linear infinite 0.3s; box-shadow: 0 0 12px rgba(16,185,129,0.6);"></div>
                        
                        <!-- 下行数据流 -->
                        <div style="position: absolute; bottom: 28%; left: 32%; width: 6px; height: 6px; background: rgba(26,226,214,0.8); border-radius: 50%; animation: dataDownload 2s linear infinite; box-shadow: 0 0 12px rgba(26,226,214,0.6);"></div>
                        <div style="position: absolute; bottom: 28%; right: 32%; width: 6px; height: 6px; background: rgba(139,92,246,0.8); border-radius: 50%; animation: dataDownload 2s linear infinite 0.3s; box-shadow: 0 0 12px rgba(139,92,246,0.6);"></div>

                        <!-- 环绕的云服务 -->
                        <!-- 用户认证服务 -->
                        <div style="position: absolute; top: 50%; left: -12%; width: 70px; height: 70px; background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.3)); border-radius: 50%; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; animation: serviceOrbit 10s linear infinite; box-shadow: 0 15px 30px rgba(255,255,255,0.1);">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="rgba(255,255,255,0.8)">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>

                        <!-- 数据存储服务 -->
                        <div style="position: absolute; top: 50%; right: -12%; width: 70px; height: 70px; background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.3)); border-radius: 50%; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; animation: serviceOrbit 10s linear infinite reverse; box-shadow: 0 15px 30px rgba(255,255,255,0.1);">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="rgba(255,255,255,0.8)">
                                <path d="M12 3C7.58 3 4 4.79 4 7s3.58 4 8 4 8-1.79 8-4-3.58-4-8-4zM4 9v3c0 2.21 3.58 4 8 4s8-1.79 8-4V9c0 2.21-3.58 4-8 4s-8-1.79-8-4zM4 14v3c0 2.21 3.58 4 8 4s8-1.79 8-4v-3c0 2.21-3.58 4-8 4s-8-1.79-8-4z"/>
                            </svg>
                        </div>

                        <!-- 推送服务 -->
                        <div style="position: absolute; top: 15%; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.3)); border-radius: 50%; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; animation: serviceOrbit 8s linear infinite; box-shadow: 0 15px 30px rgba(255,255,255,0.1);">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="rgba(255,255,255,0.8)">
                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                            </svg>
                        </div>

                        <!-- 浮动数据粒子 -->
                        <div style="position: absolute; top: 12%; left: 75%; width: 8px; height: 8px; background: radial-gradient(circle, rgba(64,134,245,0.8), rgba(64,134,245,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite; box-shadow: 0 0 16px rgba(64,134,245,0.6);"></div>
                        <div style="position: absolute; top: 80%; left: 20%; width: 7px; height: 7px; background: radial-gradient(circle, rgba(26,226,214,0.8), rgba(26,226,214,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 2s; box-shadow: 0 0 16px rgba(26,226,214,0.6);"></div>
                        <div style="position: absolute; top: 65%; left: 85%; width: 9px; height: 9px; background: radial-gradient(circle, rgba(16,185,129,0.8), rgba(16,185,129,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 3s; box-shadow: 0 0 16px rgba(16,185,129,0.6);"></div>
                        <div style="position: absolute; top: 35%; left: 10%; width: 7px; height: 7px; background: radial-gradient(circle, rgba(139,92,246,0.8), rgba(139,92,246,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 1s; box-shadow: 0 0 16px rgba(139,92,246,0.6);"></div>
                        <div style="position: absolute; top: 25%; left: 90%; width: 6px; height: 6px; background: radial-gradient(circle, rgba(245,101,101,0.8), rgba(245,101,101,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 4s; box-shadow: 0 0 16px rgba(245,101,101,0.6);"></div>
                        <div style="position: absolute; top: 90%; left: 50%; width: 8px; height: 8px; background: radial-gradient(circle, rgba(251,191,36,0.8), rgba(251,191,36,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 2.5s; box-shadow: 0 0 16px rgba(251,191,36,0.6);"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- AI时代，为什么选择在行云 -->
    <section class="features" id="features" style="padding: 100px 0; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); position: relative;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 40px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <div style="display: inline-flex; align-items: center; gap: 12px; background: rgba(64,134,245,0.1); border: 1px solid rgba(64,134,245,0.3); border-radius: 50px; padding: 8px 20px; margin-bottom: 30px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#4086F5">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span style="color: #4086F5; font-weight: 600; font-size: 14px;">AI时代机遇</span>
                </div>
                <h2 style="font-size: 3.5rem; font-weight: 800; color: #1e293b; margin-bottom: 20px; line-height: 1.2;">
                    AI时代，专注创意就够了
                </h2>
                <p style="font-size: 1.3rem; color: #64748b; max-width: 800px; margin: 0 auto; line-height: 1.6;">
                    人工智能降低了开发门槛，在行云解决产品变现能力
                </p>
            </div>

            <!-- 对比式设计 -->
            <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 60px; align-items: start; margin-bottom: 80px;">
                <!-- 传统开发困境 -->
                <div style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; position: relative;">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.8rem; font-weight: 700; color: #ef4444; margin-bottom: 15px;">传统开发困境</h3>
                        <p style="color: #64748b; font-size: 1rem;">重重障碍，创意难以变现</p>
                    </div>
                    
                    <div style="space-y: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#ef4444">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">AI降低了编程门槛，但...</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">后端架构、数据库设计、服务器运维仍然复杂</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#ef4444">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">支付集成依然困难</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">Apple IAP、微信支付、支付宝等各有复杂规则</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#ef4444">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">运维成本居高不下</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">服务器、监控、备份、安全防护等持续投入</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#ef4444">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">合规流程繁琐</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">ICP备案、等保认证、隐私政策等合规要求复杂</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px;">
                            <div style="width: 24px; height: 24px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#ef4444">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">从想法到变现路径漫长</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">技术实现、商业化部署通常需要数月甚至更久</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VS 分隔符 -->
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(64,134,245,0.3);">
                        <span style="color: white; font-weight: 800; font-size: 1.2rem;">VS</span>
                    </div>
                </div>

                <!-- 在行云方式 -->
                <div style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; position: relative;">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.8rem; font-weight: 700; color: #4086F5; margin-bottom: 15px;">AI + 在行云</h3>
                        <p style="color: #64748b; font-size: 1rem;">专注创意，快速变现</p>
                    </div>
                    
                    <div style="space-y: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#4086F5">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">AI助力快速开发</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">专注创意和用户体验，后端服务交给在行云</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#4086F5">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">按需付费，成本可控</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">无需前期投入，用多少付多少，节省90%成本</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#4086F5">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">一键支付集成</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">Apple IAP、微信、支付宝统一接入，商业化从未如此简单</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#4086F5">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">一站式合规托管</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">ICP备案、等保认证、隐私政策等合规流程全包</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px;">
                            <div style="width: 24px; height: 24px; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#4086F5">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">即开即用</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">注册账号即可使用，无需服务器配置和部署</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 核心优势总结 -->
            <div style="background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); border-radius: 25px; padding: 50px; text-align: center; color: white; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -100px; right: -100px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -80px; left: -80px; width: 160px; height: 160px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                
                <div style="position: relative; z-index: 1;">
                    <h3 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 20px;">AI助力开发，在行云助力变现</h3>
                    <p style="font-size: 1.3rem; margin-bottom: 40px; opacity: 0.95; max-width: 800px; margin-left: auto; margin-right: auto;">
                        在AI时代，创意比技术更重要。在行云解决产品变现的技术难题，让你专注于打造用户喜爱的产品。
                    </p>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-top: 50px;">
                        <div style="text-align: center;">
                            <div style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">90%</div>
                            <div style="font-size: 1.1rem; opacity: 0.9;">开发成本节省</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">即开</div>
                            <div style="font-size: 1.1rem; opacity: 0.9;">即用</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">0行</div>
                            <div style="font-size: 1.1rem; opacity: 0.9;">后端代码</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">24/7</div>
                            <div style="font-size: 1.1rem; opacity: 0.9;">专业运维</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Modules -->
    <section class="modules" id="modules">
    <div class="container">
            <div class="section-title">
                <h2>核心功能模块</h2>
                <p>一站式轻量解决方案，覆盖APP开发常用后端需求</p>
            </div>
            
            <div class="modules-grid">
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
            </div>
                    <h4>用户管理系统</h4>
                    <p>完整的用户生命周期管理，从注册认证到权限控制，为你的APP提供企业级用户体系支撑。</p>
                    <ul class="module-features">
                        <li>多种登录方式（手机号、微信、Apple ID）</li>
                        <li>用户信息管理与数据统计</li>
                        <li>权限角色分配与访问控制</li>
                        <li>用户行为分析与画像</li>
                    </ul>
        </div>
                
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                        </svg>
                    </div>
                    <h4>订单支付系统</h4>
                    <p>完整的电商级订单管理和支付系统，支持多种支付方式，助力APP快速实现商业变现。</p>
                    <ul class="module-features">
                        <li>支付宝、微信、Apple Pay集成</li>
                        <li>订单管理与状态跟踪</li>
                        <li>会员订阅与续费管理</li>
                        <li>退款处理与财务对账</li>
                    </ul>
    </div>
    
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                        </svg>
                    </div>
                    <h4>内容管理系统</h4>
                    <p>灵活的内容发布与管理平台，支持富文本编辑，让你轻松管理APP内的各类文档内容。</p>
                    <ul class="module-features">
                        <li>帮助文档与FAQ管理</li>
                        <li>用户协议与隐私政策</li>
                        <li>公告通知与版本说明</li>
                        <li>富文本编辑与多媒体支持</li>
                    </ul>
                </div>
                
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                        </svg>
                    </div>
                    <h4>数据分析系统</h4>
                    <p>深度洞察用户行为与APP运营数据，为产品优化提供数据驱动的决策支持。</p>
                    <ul class="module-features">
                        <li>用户行为埋点与分析</li>
                        <li>业务数据统计与报表</li>
                        <li>实时监控与告警通知</li>
                        <li>自定义数据看板</li>
                    </ul>
                </div>
                
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,17.56L16.07,16.43L16.62,10.33H9.38L9.2,8.3H16.8L17,6.31H7L7.56,12.32H14.45L14.22,14.9L12,15.5L9.78,14.9L9.64,13.24H7.64L7.93,16.43L12,17.56M4.07,3H19.93L18.5,19.2L12,21L5.5,19.2L4.07,3Z"/>
                        </svg>
                    </div>
                    <h4>版本控制系统</h4>
                    <p>专业的APP版本发布与管理工具，支持灰度发布、强制更新等策略，确保APP稳定迭代。</p>
                    <ul class="module-features">
                        <li>版本发布与回滚管理</li>
                        <li>灰度发布与A/B测试</li>
                        <li>强制更新与兼容性检查</li>
                        <li>发布审核与流程控制</li>
                    </ul>
                </div>
                
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11C15.4,11 16,11.4 16,12V16C16,16.6 15.6,17 15,17H9C8.4,17 8,16.6 8,16V12C8,11.4 8.4,11 9,11V10C9,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.2,9.2 10.2,10V11H13.8V10C13.8,9.2 12.8,8.2 12,8.2Z"/>
                        </svg>
                    </div>
                    <h4>安全防护系统</h4>
                    <p>企业级安全保障，从数据加密到访问控制，全方位保护你的APP和用户数据安全。</p>
                    <ul class="module-features">
                        <li>数据加密与传输安全</li>
                        <li>API访问频率限制</li>
                        <li>异常行为检测与防护</li>
                        <li>安全审计与日志追踪</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>



    <!-- 成功案例展示 - 循环滚动展示 -->
    <section style="padding: 100px 0; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); position: relative; overflow: hidden;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 40px;">
            <div style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 3rem; font-weight: 800; color: white; margin-bottom: 20px;">
                    优秀开发者的选择
                </h2>
                <p style="font-size: 1.1rem; color: #94a3b8; max-width: 700px; margin: 0 auto; white-space: nowrap;">
                    各行各业的独立开发者都在使用在行云快速构建他们的梦想APP
                </p>
            </div>

            <!-- 循环滚动容器 -->
            <div style="position: relative; overflow: hidden; margin-bottom: 60px;">
                <!-- 第一行 - 向右滚动 -->
                <div class="scroll-container" style="display: flex; gap: 20px; animation: scrollRight 60s linear infinite; margin-bottom: 30px; width: max-content;">
                    <!-- 第一组 -->
                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">云端笔记</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">跨平台笔记应用，支持实时同步</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #1AE2D6; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #4086F5; border-radius: 50%;"></div>
                        </div>
                    </div>
                    
                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,17L7,12L8.41,10.59L12,14.17L15.59,10.59L17,12L12,17Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">轻松健身</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">个人健身教练，智能训练计划</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #059669; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">智能记账</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">AI驱动的个人财务管理工具</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #d97706; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,19H5V5H19V19Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">悦读时光</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">沉浸式阅读体验，个性化推荐</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #8b5cf6; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #7c3aed; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">高效办公</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">团队协作与项目管理平台</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #dc2626; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M14.6,16.6L19.2,12L14.6,7.4L13.2,8.8L16.4,12L13.2,15.2L14.6,16.6M9.4,16.6L10.8,15.2L7.6,12L10.8,8.8L9.4,7.4L4.8,12L9.4,16.6Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">漫步旅行</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">智能旅行规划与分享社区</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #06b6d4; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #0891b2; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <!-- 重复第一组以实现无缝循环 -->
                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">云端笔记</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">跨平台笔记应用，支持实时同步</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #1AE2D6; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #4086F5; border-radius: 50%;"></div>
                        </div>
                    </div>
                    
                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,17L7,12L8.41,10.59L12,14.17L15.59,10.59L17,12L12,17Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">轻松健身</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">个人健身教练，智能训练计划</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #059669; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">智能记账</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">AI驱动的个人财务管理工具</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #d97706; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,19H5V5H19V19Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">悦读时光</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">沉浸式阅读体验，个性化推荐</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #8b5cf6; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #7c3aed; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">高效办公</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">团队协作与项目管理平台</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #dc2626; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M14.6,16.6L19.2,12L14.6,7.4L13.2,8.8L16.4,12L13.2,15.2L14.6,16.6M9.4,16.6L10.8,15.2L7.6,12L10.8,8.8L9.4,7.4L4.8,12L9.4,16.6Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">漫步旅行</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">智能旅行规划与分享社区</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #06b6d4; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #0891b2; border-radius: 50%;"></div>
                        </div>
                    </div>
                </div>

                <!-- 第二行 - 向左滚动 -->
                <div class="scroll-container" style="display: flex; gap: 20px; animation: scrollLeft 50s linear infinite; width: max-content;">
                    <!-- 第二组 -->
                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M7.5,4A5.5,5.5 0 0,0 2,9.5C2,10 2,10.5 2.1,11H6.1L10.5,9L15,11H22C22,9.5 21.5,8 20.5,7C19.5,6 18,5.5 16.5,5.5H16.3C15.8,4.6 14.8,4 13.5,4C11.1,4 9.1,5.6 8.5,7.8C8,6.4 7.8,4.9 7.5,4M2.1,13C2,13.5 2,14 2,14.5A5.5,5.5 0 0,0 7.5,20A5.5,5.5 0 0,0 13,14.5H22V13H2.1Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">休闲游戏</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">轻松有趣的休闲游戏平台</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #ec4899; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #be185d; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">投资理财</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">智能投资建议和理财规划</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #0ea5e9; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #0284c7; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M12,2A2,2 0 0,1 14,4C14,4.74 13.6,5.39 13,5.73V7H14A7,7 0 0,1 21,14H22A1,1 0 0,1 23,15V18A1,1 0 0,1 22,19H21V20A2,2 0 0,1 19,22H5A2,2 0 0,1 3,20V19H2A1,1 0 0,1 1,18V15A1,1 0 0,1 2,14H3A7,7 0 0,1 10,7H11V5.73C10.4,5.39 10,4.74 10,4A2,2 0 0,1 12,2M7.5,13A2.5,2.5 0 0,0 5,15.5A2.5,2.5 0 0,0 7.5,18A2.5,2.5 0 0,0 10,15.5A2.5,2.5 0 0,0 7.5,13M16.5,13A2.5,2.5 0 0,0 14,15.5A2.5,2.5 0 0,0 16.5,18A2.5,2.5 0 0,0 19,15.5A2.5,2.5 0 0,0 16.5,13Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">创意设计</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">专业的设计工具和创意平台</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #a855f7; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #9333ea; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M12,3L1,9L12,15L21,10.09V17H23V9M5,13.18V17.18L12,21L19,17.18V13.18L12,17L5,13.18Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">在线教育</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">优质的在线学习和教育资源</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #f97316; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #ea580c; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M18.06 22.99H1.94C1.28 22.99 0.729999 22.45 0.729999 21.79V2.21C0.729999 1.55 1.28 1.01 1.94 1.01H18.06C18.72 1.01 19.27 1.55 19.27 2.21V21.79C19.27 22.45 18.72 22.99 18.06 22.99ZM8.5 6C7.67 6 7 6.67 7 7.5S7.67 9 8.5 9 10 8.33 10 7.5 9.33 6 8.5 6ZM8.5 11C7.67 11 7 11.67 7 12.5S7.67 14 8.5 14 10 13.33 10 12.5 9.33 11 8.5 11ZM8.5 16C7.67 16 7 16.67 7 17.5S7.67 19 8.5 19 10 18.33 10 17.5 9.33 16 8.5 16Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">美食地图</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">发现身边的美食和餐厅推荐</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #84cc16; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #65a30d; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M17.753,14A2.25,2.25 0 0,1 20,16.25C20,17.77 18.77,19 17.25,19H6.75C5.23,19 4,17.77 4,16.25A2.25,2.25 0 0,1 6.25,14H6.5A4.5,4.5 0 0,1 11,9.5A4.5,4.5 0 0,1 15.5,14H17.753M12,2A1,1 0 0,1 13,3A1,1 0 0,1 12,4A1,1 0 0,1 11,3A1,1 0 0,1 12,2M8,5A1,1 0 0,1 9,6A1,1 0 0,1 8,7A1,1 0 0,1 7,6A1,1 0 0,1 8,5M16,5A1,1 0 0,1 17,6A1,1 0 0,1 16,7A1,1 0 0,1 15,6A1,1 0 0,1 16,5Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">智能助手</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">AI驱动的个人智能助手</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #6366f1; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #4f46e5; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <!-- 重复第二组以实现无缝循环 -->
                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M7.5,4A5.5,5.5 0 0,0 2,9.5C2,10 2,10.5 2.1,11H6.1L10.5,9L15,11H22C22,9.5 21.5,8 20.5,7C19.5,6 18,5.5 16.5,5.5H16.3C15.8,4.6 14.8,4 13.5,4C11.1,4 9.1,5.6 8.5,7.8C8,6.4 7.8,4.9 7.5,4M2.1,13C2,13.5 2,14 2,14.5A5.5,5.5 0 0,0 7.5,20A5.5,5.5 0 0,0 13,14.5H22V13H2.1Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">休闲游戏</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">轻松有趣的休闲游戏平台</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #ec4899; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #be185d; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">投资理财</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">智能投资建议和理财规划</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #0ea5e9; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #0284c7; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M12,2A2,2 0 0,1 14,4C14,4.74 13.6,5.39 13,5.73V7H14A7,7 0 0,1 21,14H22A1,1 0 0,1 23,15V18A1,1 0 0,1 22,19H21V20A2,2 0 0,1 19,22H5A2,2 0 0,1 3,20V19H2A1,1 0 0,1 1,18V15A1,1 0 0,1 2,14H3A7,7 0 0,1 10,7H11V5.73C10.4,5.39 10,4.74 10,4A2,2 0 0,1 12,2M7.5,13A2.5,2.5 0 0,0 5,15.5A2.5,2.5 0 0,0 7.5,18A2.5,2.5 0 0,0 10,15.5A2.5,2.5 0 0,0 7.5,13M16.5,13A2.5,2.5 0 0,0 14,15.5A2.5,2.5 0 0,0 16.5,18A2.5,2.5 0 0,0 19,15.5A2.5,2.5 0 0,0 16.5,13Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">创意设计</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">专业的设计工具和创意平台</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #a855f7; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #9333ea; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M12,3L1,9L12,15L21,10.09V17H23V9M5,13.18V17.18L12,21L19,17.18V13.18L12,17L5,13.18Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">在线教育</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">优质的在线学习和教育资源</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #f97316; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #ea580c; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M18.06 22.99H1.94C1.28 22.99 0.729999 22.45 0.729999 21.79V2.21C0.729999 1.55 1.28 1.01 1.94 1.01H18.06C18.72 1.01 19.27 1.55 19.27 2.21V21.79C19.27 22.45 18.72 22.99 18.06 22.99ZM8.5 6C7.67 6 7 6.67 7 7.5S7.67 9 8.5 9 10 8.33 10 7.5 9.33 6 8.5 6ZM8.5 11C7.67 11 7 11.67 7 12.5S7.67 14 8.5 14 10 13.33 10 12.5 9.33 11 8.5 11ZM8.5 16C7.67 16 7 16.67 7 17.5S7.67 19 8.5 19 10 18.33 10 17.5 9.33 16 8.5 16Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">美食地图</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">发现身边的美食和餐厅推荐</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #84cc16; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #65a30d; border-radius: 50%;"></div>
                        </div>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M17.753,14A2.25,2.25 0 0,1 20,16.25C20,17.77 18.77,19 17.25,19H6.75C5.23,19 4,17.77 4,16.25A2.25,2.25 0 0,1 6.25,14H6.5A4.5,4.5 0 0,1 11,9.5A4.5,4.5 0 0,1 15.5,14H17.753M12,2A1,1 0 0,1 13,3A1,1 0 0,1 12,4A1,1 0 0,1 11,3A1,1 0 0,1 12,2M8,5A1,1 0 0,1 9,6A1,1 0 0,1 8,7A1,1 0 0,1 7,6A1,1 0 0,1 8,5M16,5A1,1 0 0,1 17,6A1,1 0 0,1 16,7A1,1 0 0,1 15,6A1,1 0 0,1 16,5Z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">智能助手</h3>
                        <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">AI驱动的个人智能助手</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                            <div style="width: 8px; height: 8px; background: #6366f1; border-radius: 50%; margin-bottom: 4px;"></div>
                            <div style="width: 6px; height: 6px; background: #4f46e5; border-radius: 50%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <p style="color: #94a3b8; font-size: 1rem; margin-bottom: 30px;">
                    还有更多优秀开发者正在使用在行云构建他们的创意项目
                </p>
                <a href="/login" style="background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); color: white; padding: 14px 28px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(64, 134, 245, 0.3);">
                    加入他们 →
                </a>
            </div>
        </div>
    </section>

    <!-- 用户反馈评价 -->
    <section style="padding: 100px 0; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); position: relative;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 40px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="font-size: 3rem; font-weight: 800; color: #1e293b; margin-bottom: 20px;">
                    开发者的真实反馈
                </h2>
                <p style="font-size: 1.2rem; color: #64748b; max-width: 600px; margin: 0 auto;">
                    听听那些已经在使用在行云的开发者们怎么说
                </p>
            </div>

            <!-- 评价卡片网格 -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-bottom: 60px;">
                
                <!-- 评价卡片 1 -->
                <div style="background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; position: relative; transition: transform 0.3s ease;">
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; font-weight: 700; font-size: 1.2rem;">
                            李
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">李先生</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">独立开发者 · 智能记账APP</p>
                        </div>
                    </div>
                    <div style="display: flex; margin-bottom: 15px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <p style="color: #374151; line-height: 1.6; font-size: 1rem;">
                        "作为一个前端开发者，在行云让我能够快速构建完整的APP后端。支付集成特别简单，Apple IAP的对接只用了半天就完成了，以前这至少要折腾一周。"
                    </p>
                    <div style="position: absolute; top: 20px; right: 20px; opacity: 0.1;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="#4086F5">
                            <path d="M14,17H17L19,13V7H13V13H16M6,17H9L11,13V7H5V13H8L6,17Z"/>
                        </svg>
                    </div>
                </div>

                <!-- 评价卡片 2 -->
                <div style="background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; position: relative; transition: transform 0.3s ease;">
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; font-weight: 700; font-size: 1.2rem;">
                            王
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">王女士</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">产品经理 · 健身社交APP</p>
                        </div>
                    </div>
                    <div style="display: flex; margin-bottom: 15px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <p style="color: #374151; line-height: 1.6; font-size: 1rem;">
                        "没有技术背景的我也能快速搭建APP后端，用户管理、数据统计、推送通知都有现成的方案。团队开发效率提升了至少3倍，成本节省了80%以上。"
                    </p>
                    <div style="position: absolute; top: 20px; right: 20px; opacity: 0.1;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="#10b981">
                            <path d="M14,17H17L19,13V7H13V13H16M6,17H9L11,13V7H5V13H8L6,17Z"/>
                        </svg>
                    </div>
                </div>

                <!-- 评价卡片 3 -->
                <div style="background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; position: relative; transition: transform 0.3s ease;">
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; font-weight: 700; font-size: 1.2rem;">
                            张
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">张老板</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">创业者 · 时间管理工具</p>
                        </div>
                    </div>
                    <div style="display: flex; margin-bottom: 15px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <p style="color: #374151; line-height: 1.6; font-size: 1rem;">
                        "在行云帮我快速搭建了时间管理工具的后端。用户数据同步、提醒推送、统计分析都很完善，从想法到上线只用了1个月，现在已经有3万+活跃用户。"
                    </p>
                    <div style="position: absolute; top: 20px; right: 20px; opacity: 0.1;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="#f59e0b">
                            <path d="M14,17H17L19,13V7H13V13H16M6,17H9L11,13V7H5V13H8L6,17Z"/>
                        </svg>
                    </div>
                </div>

                <!-- 评价卡片 4 -->
                <div style="background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; position: relative; transition: transform 0.3s ease;">
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; font-weight: 700; font-size: 1.2rem;">
                            陈
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">陈设计师</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">UI设计师 · 创意工具APP</p>
                        </div>
                    </div>
                    <div style="display: flex; margin-bottom: 15px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <p style="color: #374151; line-height: 1.6; font-size: 1rem;">
                        "作为设计师转型做产品，在行云让我能够把设计想法快速变成可用的APP。API文档清晰，技术支持响应很快，真正做到了让非技术人员也能做产品。"
                    </p>
                    <div style="position: absolute; top: 20px; right: 20px; opacity: 0.1;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="#8b5cf6">
                            <path d="M14,17H17L19,13V7H13V13H16M6,17H9L11,13V7H5V13H8L6,17Z"/>
                        </svg>
                    </div>
                </div>

                <!-- 评价卡片 5 -->
                <div style="background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; position: relative; transition: transform 0.3s ease;">
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; font-weight: 700; font-size: 1.2rem;">
                            刘
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">刘工</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">全栈开发者 · 密码管理器</p>
                        </div>
                    </div>
                    <div style="display: flex; margin-bottom: 15px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <p style="color: #374151; line-height: 1.6; font-size: 1rem;">
                        "即使作为有经验的全栈开发者，在行云也大大提升了我的开发效率。密码管理器需要高安全性的数据存储和同步，在行云的安全机制让我很放心。"
                    </p>
                    <div style="position: absolute; top: 20px; right: 20px; opacity: 0.1;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="#ef4444">
                            <path d="M14,17H17L19,13V7H13V13H16M6,17H9L11,13V7H5V13H8L6,17Z"/>
                        </svg>
                    </div>
                </div>

                <!-- 评价卡片 6 -->
                <div style="background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; position: relative; transition: transform 0.3s ease;">
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; font-weight: 700; font-size: 1.2rem;">
                            赵
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">小赵</h4>
                            <p style="color: #64748b; font-size: 0.9rem;">计算机专业学生 · 校园服务APP</p>
                        </div>
                    </div>
                    <div style="display: flex; margin-bottom: 15px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24" style="margin-right: 2px;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#fbbf24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <p style="color: #374151; line-height: 1.6; font-size: 1rem;">
                        "作为学生党，预算有限但想法很多。在行云的免费额度让我能够免费验证想法，付费价格也很亲民。现在我的校园APP已经有5000+用户了！"
                    </p>
                    <div style="position: absolute; top: 20px; right: 20px; opacity: 0.1;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="#06b6d4">
                            <path d="M14,17H17L19,13V7H13V13H16M6,17H9L11,13V7H5V13H8L6,17Z"/>
                        </svg>
                    </div>
                </div>
            </div>


        </div>
    </section>



    <!-- 右侧悬浮技术支持 -->
    <div id="floating-support" style="position: fixed; right: 30px; bottom: 30px; z-index: 1000;">
        <div style="background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); border-radius: 50px; padding: 15px 25px; box-shadow: 0 8px 25px rgba(64, 134, 245, 0.3); cursor: pointer; display: flex; align-items: center; gap: 12px; color: white; font-weight: 600; transition: all 0.3s ease;" onclick="toggleSupportPanel()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
            </svg>
            <span>技术咨询</span>
        </div>
        
        <!-- 支持面板 -->
        <div id="support-panel" style="position: absolute; bottom: 70px; right: 0; width: 320px; background: white; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); padding: 25px; display: none; border: 1px solid #E5E7EB;">
            <h4 style="margin: 0 0 15px 0; color: #1F2937; font-size: 16px; font-weight: 600;">需要帮助？</h4>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="/about" style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #F8FAFC; border-radius: 10px; text-decoration: none; color: #374151; transition: background 0.3s ease;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                    </svg>
                    <span style="font-size: 14px;">查看文档</span>
                </a>
                <a href="https://github.com/zaihangyun" target="_blank" style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #F8FAFC; border-radius: 10px; text-decoration: none; color: #374151; transition: background 0.3s ease;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                    </svg>
                    <span style="font-size: 14px;">GitHub 社区</span>
                </a>
                <a href="/about" style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #F8FAFC; border-radius: 10px; text-decoration: none; color: #374151; transition: background 0.3s ease;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <span style="font-size: 14px;">联系客服</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-grid">
                <!-- 品牌信息 -->
                <div class="footer-column">
                    <div class="footer-brand">
                        <img src="/images/logo-baas.png" alt="在行云">
                        <span class="footer-brand-text">在行云</span>
                    </div>
                    <p class="footer-desc">
                        专为APP开发者打造的轻量级BaaS平台，让独立开发者专注于打造优质的前端体验，无需后端开发即可拥有完整的系统支撑。
                    </p>
                    <div class="footer-social">
                        <a href="https://github.com/zaihangyun" target="_blank" title="GitHub">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                        <a href="/about" title="联系我们">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- 导航组 -->
                <div class="footer-nav-group">
                    <!-- 产品服务 -->
                    <div class="footer-section">
                        <h4>产品服务</h4>
                        <ul class="footer-links">
                            <li><a href="/pricing">定价方案</a></li>
                            <li><a href="/">功能特性</a></li>
                            <li><a href="/about">技术架构</a></li>
                            <li><a href="/about">安全保障</a></li>
                        </ul>
                    </div>

                    <!-- 开发者资源 -->
                    <div class="footer-section">
                        <h4>开发者资源</h4>
                        <ul class="footer-links">
                            <li><a href="https://github.com/zaihangyun" target="_blank">GitHub</a></li>
                            <li><a href="/about">API文档</a></li>
                            <li><a href="/about">SDK下载</a></li>
                            <li><a href="/about">技术支持</a></li>
                        </ul>
                    </div>

                    <!-- 公司信息 -->
                    <div class="footer-section">
                        <h4>公司信息</h4>
                        <ul class="footer-links">
                            <li><a href="/about">关于我们</a></li>
                            <li><a href="/about">服务条款</a></li>
                            <li><a href="/about">隐私政策</a></li>
                            <li><a href="/about">联系我们</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="footer-bottom" style="display: flex; justify-content: space-between; align-items: center; padding-top: 30px; border-top: 1px solid rgba(229, 231, 235, 0.3);">
                <div>
                    <p style="margin: 0; color: #6b7280; font-size: 14px;">© 2016 - 2025 孔目湖（北京）科技有限公司 Powered by <a href="https://www.zaihangyun.com/" style="color: #4086F5; text-decoration: none;">在行云</a></p>
                    <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;"><a href="https://beian.miit.gov.cn/" target="_blank" style="color: #6b7280; text-decoration: none;">京ICP备17023015号-2</a></p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; color: #6b7280; font-size: 14px; font-weight: 500;">让每个开发者都能专注于创造优秀的产品体验</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // 滚动时导航栏样式变化
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = 'none';
            }
        });

        // 平滑滚动到锚点
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // 监听滚动，添加动画效果
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // 为所有卡片添加滚动动画
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.feature-card, .module-card, .pricing-card');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });
        });

        // 悬浮支持面板
        function toggleSupportPanel() {
            const panel = document.getElementById('support-panel');
            if (panel) {
                panel.style.display = panel.style.display === 'none' || panel.style.display === '' ? 'block' : 'none';
            }
        }

        // 点击其他地方关闭面板
        document.addEventListener('click', function(e) {
            const support = document.getElementById('floating-support');
            const panel = document.getElementById('support-panel');
            if (support && panel && !support.contains(e.target)) {
                panel.style.display = 'none';
            }
        });
    </script>
</body>
</html>
</body>
</html>