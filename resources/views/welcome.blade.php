<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在行云 - APP开发者的后端难题终结者</title>
    <meta name="description" content="专为APP开发者打造的轻量级BaaS平台，无需写一行后端代码，即可拥有完整的用户、支付、内容管理系统">
    <meta name="keywords" content="在行云,BaaS,后端服务,APP开发,独立开发者,无服务器,云服务">
    <link rel="icon" href="/favicon.ico">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
</head>
<body>
    <!-- 导航栏 -->
    @include('layouts.header')

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
                    <span>专为独立开发者打造</span>
                </div>

                <h1 style="font-size: 4.5rem; font-weight: 900; line-height: 1.1; margin-bottom: 30px; letter-spacing: -0.02em;">
                    无需后端开发<br>
                    <span style="background: linear-gradient(135deg, #1AE2D6 0%, #FFFFFF 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; white-space: nowrap;">APP快速构建后端能力</span>
                </h1>

                <p style="font-size: 1.4rem; line-height: 1.6; margin-bottom: 40px; opacity: 0.95; max-width: 500px;">
                    专为独立开发者打造的BaaS平台，一站式解决后端需求，让您专注产品创新
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
                        <div style="font-size: 2rem; font-weight: 700; color: #1AE2D6;">数据安全</div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">企业级保障</div>
                    </div>
                </div>

                <div class="hero-buttons" style="display: flex; gap: 20px; align-items: center;">
                    <a href="/login" style="background: linear-gradient(135deg, #1AE2D6 0%, #FFFFFF 100%); color: #4086F5; padding: 16px 32px; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 16px; box-shadow: 0 10px 30px rgba(26,226,214,0.3); transition: all 0.3s ease; border: none;">
                        立即免费体验 →
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
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 200px; height: 120px; display: flex; align-items: center; justify-content: center; position: relative;">
                            
                            <!-- 云朵图片 -->
                            <img src="{{ asset('images/logo-mini.png') }}" alt="云服务" style="width: 180px; height: auto;">
                            
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
                        <div style="position: absolute; top: 20%; left: 70%; width: 8px; height: 8px; background: radial-gradient(circle, rgba(64,134,245,0.8), rgba(64,134,245,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite; box-shadow: 0 0 16px rgba(64,134,245,0.6);"></div>
                        <div style="position: absolute; top: 75%; left: 25%; width: 7px; height: 7px; background: radial-gradient(circle, rgba(26,226,214,0.8), rgba(26,226,214,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 2s; box-shadow: 0 0 16px rgba(26,226,214,0.6);"></div>
                        <div style="position: absolute; top: 60%; left: 75%; width: 9px; height: 9px; background: radial-gradient(circle, rgba(16,185,129,0.8), rgba(16,185,129,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 3s; box-shadow: 0 0 16px rgba(16,185,129,0.6);"></div>
                        <div style="position: absolute; top: 40%; left: 20%; width: 7px; height: 7px; background: radial-gradient(circle, rgba(139,92,246,0.8), rgba(139,92,246,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 1s; box-shadow: 0 0 16px rgba(139,92,246,0.6);"></div>
                        <div style="position: absolute; top: 30%; left: 80%; width: 6px; height: 6px; background: radial-gradient(circle, rgba(245,101,101,0.8), rgba(245,101,101,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 4s; box-shadow: 0 0 16px rgba(245,101,101,0.6);"></div>
                        <div style="position: absolute; top: 80%; left: 50%; width: 8px; height: 8px; background: radial-gradient(circle, rgba(251,191,36,0.8), rgba(251,191,36,0.3)); border-radius: 50%; animation: floatParticle 5s ease-in-out infinite 2.5s; box-shadow: 0 0 16px rgba(251,191,36,0.6);"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- AI时代，为什么选择在行云 -->
    <section class="features" id="features" style="padding: 100px 0; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); position: relative;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <div style="display: inline-flex; align-items: center; gap: 12px; background: rgba(64,134,245,0.1); border: 1px solid rgba(64,134,245,0.3); border-radius: 50px; padding: 8px 20px; margin-bottom: 30px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#4086F5">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span style="color: #4086F5; font-weight: 600; font-size: 14px;">AI时代机遇</span>
                </div>
                <h2 style="font-size: 3.5rem; font-weight: 800; color: #1e293b; margin-bottom: 20px; line-height: 1.2;">
                    专注产品创新，后端交给在行云
                </h2>
                <p style="font-size: 1.3rem; color: #64748b; max-width: 800px; margin: 0 auto; line-height: 1.6;">
                    AI时代降低了前端开发门槛，在行云为您解决后端技术难题和变现能力
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
                        <h3 style="font-size: 1.8rem; font-weight: 700; color: #ef4444; margin-bottom: 15px;">传统开发痛点</h3>
                        <p style="color: #64748b; font-size: 1rem;">技术门槛高，变现路径长</p>
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
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">技术实现、产品部署通常需要数月甚至更久</p>
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
                        <h3 style="font-size: 1.8rem; font-weight: 700; color: #4086F5; margin-bottom: 15px;">在行云BaaS</h3>
                        <p style="color: #64748b; font-size: 1rem;">专注产品，极速上线</p>
                    </div>
                    
                    <div style="space-y: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#4086F5">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">专注产品创新</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">无需后端开发，专注产品功能和用户体验</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#4086F5">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">成本可控，按需付费</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">无需自建IDC和运维，一站式服务，大幅降低开发成本</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#4086F5">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">支付集成，一键搞定</h4>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5;">Apple IAP、微信、支付宝统一接入，快速实现商业变现</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="width: 24px; height: 24px; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#4086F5">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">合规托管，省心省力</h4>
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
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 5px;">即开即用，快速上线</h4>
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
                    <h3 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 20px;">专注产品创新，后端能力一键拥有</h3>
                    <p style="font-size: 1.3rem; margin-bottom: 40px; opacity: 0.95; max-width: 800px; margin-left: auto; margin-right: auto;">
                        在行云BaaS为您提供企业级后端服务，从用户管理到支付变现，从内容发布到数据分析，让您的APP快速具备完整变现能力。
                    </p>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-top: 50px;">
                        <div style="text-align: center;">
                            <div style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">90%</div>
                            <div style="font-size: 1.1rem; opacity: 0.9;">开发成本节省</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">0学习成本</div>
                            <div style="font-size: 1.1rem; opacity: 0.9;">简单易用</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">即开即用</div>
                            <div style="font-size: 1.1rem; opacity: 0.9;">注册即可使用</div>
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
                <h2>完整后端能力，开箱即用</h2>
                <p>涵盖APP变现所需的核心后端服务，无需开发即可拥有</p>
            </div>
            
            <div class="modules-grid">
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
            </div>
                    <h4>用户管理系统</h4>
                    <p>多种登录方式集成，完整用户生命周期管理，为您的APP提供企业级用户体系。</p>
                    <ul class="module-features">
                        <li>多种登录方式（手机号、微信、Apple ID等）</li>
                        <li>用户信息管理与数据统计</li>
                        <li>登录互踢策略（管控多设备同时登录）</li>
                        <li>注销延时删除机制（自定义缓冲期）</li>
                    </ul>
        </div>
                
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                        </svg>
                    </div>
                    <h4>订单支付系统</h4>
                    <p>一键集成主流支付方式，完整订单管理流程，助力APP快速实现商业变现。</p>
                    <ul class="module-features">
                        <li>支付宝、微信、Apple IAP集成</li>
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
                    <p>无需服务器即可发布内容，轻松管理用户协议、帮助文档等APP必需内容。</p>
                    <ul class="module-features">
                        <li>帮助文档与FAQ管理</li>
                        <li>用户协议与隐私政策</li>
                        <li>公告通知与版本说明</li>
                        <li>富文本编辑与Markdown支持</li>
                    </ul>
                </div>
                
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                        </svg>
                    </div>
                    <h4>数据分析系统</h4>
                    <p>基础数据统计与实时监控，为业务运营提供关键指标分析和决策支持。</p>
                    <ul class="module-features">
                        <li>API调用统计与实时监控</li>
                        <li>用户增长与订单收入分析</li>
                        <li>多应用数据汇总与对比</li>
                        <li>趋势图表与数据可视化</li>
                    </ul>
                </div>
                
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,3C13.11,3 14,3.89 14,5C14,6.11 13.11,7 12,7C10.89,7 10,6.11 10,5C10,3.89 10.89,3 12,3M12,9C13.11,9 14,9.89 14,11C14,12.11 13.11,13 12,13C10.89,13 10,12.11 10,11C10,9.89 10.89,9 12,9M12,15C13.11,15 14,15.89 14,17C14,18.11 13.11,19 12,19C10.89,19 10,18.11 10,17C10,15.89 10.89,15 12,15M12,21C13.11,21 14,21.89 14,23H10C10,21.89 10.89,21 12,21M12,7V9M12,13V15M6,11C7.11,11 8,11.89 8,13C8,14.11 7.11,15 6,15C4.89,15 4,14.11 4,13C4,11.89 4.89,11 6,11M18,11C19.11,11 20,11.89 20,13C20,14.11 19.11,15 18,15C16.89,15 16,14.11 16,13C16,11.89 16.89,11 18,11M8,13H10M14,13H16"/>
                        </svg>
                    </div>
                    <h4>版本控制系统</h4>
                    <p>APP版本发布与更新管理，支持灰度发布和强制更新，确保产品稳定迭代。</p>
                    <ul class="module-features">
                        <li>版本发布与回滚管理</li>
                        <li>灰度发布与A/B测试</li>
                        <li>强制更新与兼容性检查</li>
                        <li>自定义应用配置发布</li>
                    </ul>
                </div>
                
                <div class="module-card">
                    <div class="module-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11C15.4,11 16,11.4 16,12V16C16,16.6 15.6,17 15,17H9C8.4,17 8,16.6 8,16V12C8,11.4 8.4,11 9,11V10C9,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.2,9.2 10.2,10V11H13.8V10C13.8,9.2 12.8,8.2 12,8.2Z"/>
                        </svg>
                    </div>
                    <h4>安全防护系统</h4>
                    <p>企业级安全保障，数据加密传输与访问控制，全方位保护APP和用户数据安全。</p>
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



    <!-- 成功案例展示 - 真正无缝循环滚动 -->
    <section style="padding: 100px 0; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); position: relative; overflow: hidden;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 3rem; font-weight: 800; color: white; margin-bottom: 20px;">
                    独立开发者的首选
                </h2>
                <p style="font-size: 1.1rem; color: #94a3b8; max-width: 700px; margin: 0 auto;">
                    各行各业的独立开发者和小团队都在使用在行云快速构建完整后端能力
                </p>
            </div>

            <!-- 真正无缝循环容器 -->
            <div style="position: relative; overflow: hidden; margin-bottom: 60px;">
                <!-- 第一行 - 向右滚动 -->
                <div class="seamless-scroll-right" style="margin-bottom: 30px;">
                    <div class="scroll-content">
                        @foreach($cases['row1'] as $case)
                        <div class="case-card" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                            <div style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                <img src="{{ asset('images/icons/' . $case['icon']) }}" 
                                     alt="{{ $case['name'] }}" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges; backface-visibility: hidden; transform: translateZ(0);"
                                     onerror="this.src='{{ asset('images/icons/default.png') }}'">
                        </div>
                            <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">{{ $case['name'] }}</h3>
                            <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">{{ $case['description'] }}</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                                <div style="width: 8px; height: 8px; background: {{ $case['dot_color'] }}; border-radius: 50%; margin-bottom: 4px;"></div>
                                <div style="width: 6px; height: 6px; background: {{ $case['dot_color_small'] }}; border-radius: 50%;"></div>
                        </div>
                    </div>
                        @endforeach
                    </div>
                </div>

                <!-- 第二行 - 向左滚动 -->
                <div class="seamless-scroll-left">
                    <div class="scroll-content">
                        @foreach($cases['row2'] as $case)
                        <div class="case-card" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; width: 320px; flex-shrink: 0; backdrop-filter: blur(10px);">
                        <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                            <div style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                <img src="{{ asset('images/icons/' . $case['icon']) }}" 
                                     alt="{{ $case['name'] }}" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges; backface-visibility: hidden; transform: translateZ(0);"
                                     onerror="this.src='{{ asset('images/icons/default.png') }}'">
                        </div>
                            <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">{{ $case['name'] }}</h3>
                            <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.4;">{{ $case['description'] }}</p>
                        <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.3;">
                                <div style="width: 8px; height: 8px; background: {{ $case['dot_color'] }}; border-radius: 50%; margin-bottom: 4px;"></div>
                                <div style="width: 6px; height: 6px; background: {{ $case['dot_color_small'] }}; border-radius: 50%;"></div>
                        </div>
                    </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <p style="color: #94a3b8; font-size: 1rem; margin-bottom: 30px;">
                    还有更多独立开发者正在使用在行云快速构建完整后端能力
                </p>
                <a href="/console/auth/login" style="background: linear-gradient(135deg, #4086F5 0%, #1AE2D6 100%); color: white; padding: 14px 28px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(64, 134, 245, 0.3);">
                    加入他们 →
                </a>
            </div>
        </div>
    </section>

    <!-- 用户反馈评价 -->
    <section style="padding: 100px 0; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); position: relative;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="font-size: 3rem; font-weight: 800; color: #1e293b; margin-bottom: 20px;">
                    开发者真实评价
                </h2>
                <p style="font-size: 1.2rem; color: #64748b; max-width: 600px; margin: 0 auto;">
                    听听正在使用在行云的独立开发者和小团队怎么说
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
                            <p style="color: #64748b; font-size: 0.9rem;">小团队 · 防沉迷APP</p>
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
                        "我们是小规模团队，专注开发防沉迷APP。在行云BaaS提供的标准模块解决了除核心管控功能之外的全部需求，让我们可以专注于各种品牌机型的适配和管控算法的实现。相比自建后端，节省了大量计算资源和运维成本。"
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
                            <p style="color: #64748b; font-size: 0.9rem;">产品经理 · 桌面组件APP</p>
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
                        "没有技术背景的我也能快速搭建桌面组件APP的后端，用户登录、数据存储、内容管理都有现成的方案。不用担心服务器运维，专注产品功能开发，团队效率提升了至少3倍。"
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
                            <p style="color: #64748b; font-size: 0.9rem;">全栈开发者 · 解压缩工具</p>
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
                        "即使作为有经验的全栈开发者，在行云也大大提升了我的开发效率。解压缩工具的用户登录和应用内购买功能都交给在行云处理，我只需专注核心功能开发，省去了大量后端工作。"
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
                            <p style="color: #64748b; font-size: 0.9rem;">计算机专业学生 · 编程学习APP</p>
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
                <a href="/docs/1.x" target="_blank" style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #F8FAFC; border-radius: 10px; text-decoration: none; color: #374151; transition: background 0.3s ease;">
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
                <a href="/about#contact-info-section" style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #F8FAFC; border-radius: 10px; text-decoration: none; color: #374151; transition: background 0.3s ease;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <span style="font-size: 14px;">联系客服</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    @include('layouts.footer')

    <!-- 案例展示滚动动画脚本 -->
    <script>
    // 案例展示无缝滚动动画
    document.addEventListener('DOMContentLoaded', function() {
        // 为滚动容器添加CSS动画
        const rightScroll = document.querySelector('.seamless-scroll-right .scroll-content');
        const leftScroll = document.querySelector('.seamless-scroll-left .scroll-content');
        
        if (rightScroll) {
            // 克隆内容以实现无缝效果
            const originalContent = rightScroll.innerHTML;
            rightScroll.innerHTML = originalContent + originalContent;
            
            // 添加CSS动画
            rightScroll.style.animation = 'scrollRightSafe 60s linear infinite';
        }
        
        if (leftScroll) {
            // 克隆内容以实现无缝效果
            const originalContent = leftScroll.innerHTML;
            leftScroll.innerHTML = originalContent + originalContent;
            
            // 添加CSS动画
            leftScroll.style.animation = 'scrollLeftSafe 60s linear infinite';
        }
    });
    </script>

    <!-- 引入公共脚本 -->
    @include('layouts.scripts')
    

</body>
</html>