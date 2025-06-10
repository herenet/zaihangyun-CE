<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{config('admin.title')}} | {{ trans('admin.login') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  @if(!is_null($favicon = Admin::favicon()))
  <link rel="shortcut icon" href="{{$favicon}}">
  @endif

  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/font-awesome/css/font-awesome.min.css") }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css") }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css") }}">

  <style>
    /* 重置页面样式 */
    body.login-page {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* 主容器 */
    .login-container {
      display: flex;
      min-height: 100vh;
      width: 100%;
    }

    /* 左侧品牌区域 - 参考Fireboom比例调整 */
    .brand-section {
      flex: 0 0 40%;
      background: linear-gradient(135deg, #4086F5 0%, #6B9BF7 50%, #1AE2D6 100%);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px 30px;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .brand-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="20" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="30" cy="40" r="1" fill="rgba(255,255,255,0.04)"/><circle cx="70" cy="60" r="1" fill="rgba(255,255,255,0.02)"/><circle cx="20" cy="80" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
      opacity: 0.3;
    }

    /* 左上角logo */
    .brand-logo-corner {
      position: absolute;
      top: 30px;
      left: 30px;
      z-index: 3;
    }

    .brand-logo-corner img {
      height: 30px;
      filter: brightness(0) invert(1);
    }

    .brand-content {
      position: relative;
      z-index: 2;
      text-align: center;
      max-width: 450px;
    }

    /* 主标题 */
    .brand-title {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 30px;
      line-height: 1.2;
      letter-spacing: -0.02em;
    }

    .brand-subtitle {
      font-size: 22px;
      font-weight: 400;
      margin-bottom: 40px;
      opacity: 0.95;
      line-height: 1.4;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 40px;
    }

    .feature-card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 24px;
      text-align: left;
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }

    .feature-card:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
    }

    .feature-icon {
      width: 48px;
      height: 48px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
      font-size: 18px;
    }

    .feature-title {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .feature-desc {
      font-size: 14px;
      opacity: 0.9;
      line-height: 1.4;
    }

    .brand-highlights {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }

    .highlight-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      opacity: 0.9;
    }

    .highlight-item i {
      color: #1AE2D6;
    }

    /* 右侧登录区域 - 调整宽度比例 */
    .auth-section {
      flex: 1;
      background: #FAFBFC;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px;
      min-height: 100vh;
    }

    .auth-container {
      width: 100%;
      max-width: 420px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }

    /* Logo区域 */
    .auth-header {
      text-align: center;
      padding: 40px 30px 30px;
    }

    .auth-logo img {
      height: 70px;
      width: auto;
    }

    /* Tab导航 */
    .auth-tabs {
      padding: 0 30px;
      margin-bottom: 30px;
      margin-top: 20px;
    }

    .tab-nav {
      display: flex;
      background: #F8F9FA;
      border-radius: 10px;
      padding: 4px;
    }

    .tab-btn {
      flex: 1;
      padding: 12px 16px;
      border: none;
      background: transparent;
      color: #6B7280;
      font-weight: 600;
      font-size: 14px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .tab-btn.active {
      background: #4086F5;
      color: white;
      box-shadow: 0 2px 8px rgba(64, 134, 245, 0.3);
    }

    .tab-btn:hover:not(.active) {
      background: rgba(64, 134, 245, 0.1);
      color: #4086F5;
    }

    /* 表单容器 - 固定高度避免跳动 */
    .form-container {
      min-height: 420px; /* 设置最小高度，避免切换时跳动 */
      padding: 0 30px 30px;
      position: relative;
    }

    /* 表单内容 */
    .tab-content {
      display: none;
      width: 100%;
    }

    .tab-content.active {
      display: block;
    }

    .auth-form {
      width: 100%;
    }

    /* 表单组 */
    .form-group {
      margin-bottom: 20px;
    }

    .form-control {
      width: 100%;
      height: 48px;
      padding: 12px 16px;
      border: 2px solid #E5E7EB;
      border-radius: 8px;
      font-size: 14px;
      background: #FAFBFC;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      outline: none;
      border-color: #4086F5;
      background: white;
      box-shadow: 0 0 0 3px rgba(64, 134, 245, 0.1);
    }

    .form-control::placeholder {
      color: #9CA3AF;
    }

    /* 输入组（验证码） */
    .input-group {
      display: flex;
      position: relative;
      align-items: stretch; /* 确保高度一致 */
    }

    .input-group .form-control {
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
      border-right: none;
      flex: 1;
      margin: 0; /* 移除可能的margin */
    }

    .code-btn {
      height: 48px;
      padding: 0 16px;
      border: 2px solid #E5E7EB;
      border-left: none;
      border-top-right-radius: 8px;
      border-bottom-right-radius: 8px;
      background: #F8FAFC;
      color: #4086F5;
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.3s ease;
      white-space: nowrap;
      min-width: 90px;
      flex-shrink: 0;
      margin: 0; /* 移除可能的margin */
      box-sizing: border-box; /* 确保border包含在尺寸内 */
    }

    /* 修复验证码按钮hover样式 */
    .code-btn:hover:not(:disabled) {
      background: #F0F7FF;
      color: #3574E3;
      border-color: #E5E7EB; /* 保持边框颜色不变 */
    }

    .code-btn:disabled {
      background: #F3F4F6;
      color: #9CA3AF;
      cursor: not-allowed;
      border-color: #E5E7EB;
    }

    /* 复选框 */
    .checkbox-label {
      display: flex;
      align-items: center;
      font-size: 14px;
      color: #6B7280;
      cursor: pointer;
      user-select: none;
    }

    .checkbox-label input[type="checkbox"] {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    }

    .checkmark {
      width: 18px;
      height: 18px;
      border: 2px solid #E5E7EB;
      border-radius: 4px;
      margin-right: 8px;
      position: relative;
      transition: all 0.3s ease;
      flex-shrink: 0;
    }

    .checkbox-label input[type="checkbox"]:checked + .checkmark {
      background: #4086F5;
      border-color: #4086F5;
    }

    .checkbox-label input[type="checkbox"]:checked + .checkmark::after {
      content: '';
      position: absolute;
      left: 5px;
      top: 2px;
      width: 4px;
      height: 8px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }

    /* 提交按钮 */
    .submit-btn {
      width: 100%;
      height: 48px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-bottom: 20px;
    }

    .submit-btn {
      background: linear-gradient(135deg, #4086F5 0%, #6B9BF7 100%);
      color: white;
      box-shadow: 0 4px 12px rgba(64, 134, 245, 0.3);
    }

    .submit-btn:hover {
      background: linear-gradient(135deg, #3574E3 0%, #5A8AF6 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(64, 134, 245, 0.4);
    }

    .register-btn {
      background: linear-gradient(135deg, #10B981 0%, #1AE2D6 100%);
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .register-btn:hover {
      background: linear-gradient(135deg, #0D9488 0%, #15C7BB 100%);
      box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
    }

    /* 表单底部 */
    .form-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 15px;
      border-top: 1px solid #F3F4F6;
    }

    .link {
      color: #4086F5;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .link:hover {
      color: #3574E3;
      text-decoration: none;
    }

    /* 错误信息 */
    .has-error .form-control {
      border-color: #EF4444;
      background: #FEF2F2;
    }

    /* 修复input-group在错误状态下的样式一致性 */
    .has-error .input-group .form-control {
      border-color: #EF4444;
      background: #FEF2F2;
    }

    .has-error .input-group .code-btn {
      border-color: #EF4444;
      background: #FEF2F2;
      color: #DC2626;
    }

    .has-error .input-group .code-btn:hover:not(:disabled) {
      background: #FEE2E2;
      color: #B91C1C;
      border-color: #EF4444;
    }

    .has-error .input-group .code-btn:disabled {
      background: #F9FAFB;
      color: #9CA3AF;
      border-color: #EF4444;
    }

    /* 确保input-group在焦点状态下的样式 */
    .input-group .form-control:focus {
      border-color: #4086F5;
      background: white;
      box-shadow: none; /* 移除默认阴影，避免和按钮不匹配 */
      z-index: 1; /* 确保焦点时input在按钮上层 */
      position: relative;
    }

    .input-group .form-control:focus + .code-btn {
      border-color: #4086F5;
    }

    .error-text {
      color: #EF4444;
      font-size: 13px;
      margin-top: 5px;
      display: block;
    }

    /* 响应式 */
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
      }
      
      .brand-section {
        display: none;
      }
      
      .auth-section {
        padding: 20px 15px;
      }
      
      .auth-container {
        max-width: none;
      }
      
      .auth-header {
        padding: 30px 20px 20px;
      }
      
      .auth-tabs, .form-container {
        padding-left: 20px;
        padding-right: 20px;
      }
      
      .form-container {
        min-height: 380px; /* 移动端调整最小高度 */
      }
      
      .form-footer {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
    }

    @media (max-width: 480px) {
      .auth-logo img {
        height: 70px;
      }
      
      .form-control, .code-btn, .submit-btn {
        height: 44px;
      }
      
      .form-container {
        min-height: 360px;
      }
    }

    /* SweetAlert2 必要样式修复 */
    .swal2-popup {
      border-radius: 12px !important;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
      padding: 30px !important;
      font-family: inherit !important;
    }

    .swal2-title {
      font-size: 20px !important;
      font-weight: 600 !important;
      color: #333 !important;
      margin-bottom: 10px !important;
    }

    .swal2-content {
      font-size: 14px !important;
      color: #666 !important;
    }

    .swal2-confirm {
      background: #4086F5 !important;
      border: none !important;
      border-radius: 8px !important;
      padding: 10px 24px !important;
      font-weight: 500 !important;
    }

    .swal2-confirm:hover {
      background: #2563EB !important;
    }

    .swal2-icon.swal2-success {
      color: #10B981 !important;
      border-color: #10B981 !important;
    }

    .swal2-icon.swal2-error {
      color: #EF4444 !important;
      border-color: #EF4444 !important;
    }
  </style>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <link href="{{ admin_asset('vendor/laravel-admin/sweetalert2/dist/sweetalert2.css') }}" rel="stylesheet">
</head>
<body class="login-page">

<div class="login-container">
  <!-- 左侧品牌区域 -->
  <div class="brand-section">
    <!-- 左上角logo -->
    <div class="brand-logo-corner">
      <img src="{{ admin_asset('images/logo-mini.png') }}" alt="在行云">
    </div>
    
    <div class="brand-content">
      <!-- 主标题 -->
      <h1 class="brand-title">在行云BaaS</h1>
      
      <p class="brand-subtitle">无需后端开发，快速为APP接入变现能力</p>
      
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa fa-users"></i>
          </div>
          <div class="feature-title">用户体系</div>
          <div class="feature-desc">支持微信、手机号、Apple ID等多种登录方式</div>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa fa-credit-card"></i>
          </div>
          <div class="feature-title">订单变现</div>
          <div class="feature-desc">集成支付宝、微信、Apple Pay等主流支付</div>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa fa-file-text"></i>
          </div>
          <div class="feature-title">内容管理</div>
          <div class="feature-desc">帮助文档、协议条款、自定义内容管理</div>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa fa-cogs"></i>
          </div>
          <div class="feature-title">版本控制</div>
          <div class="feature-desc">APP版本管理、更新策略、灰度发布</div>
        </div>
      </div>
      
      <div class="brand-highlights">
        <div class="highlight-item">
          <i class="fa fa-check-circle"></i>
          <span>零后端开发</span>
        </div>
        <div class="highlight-item">
          <i class="fa fa-check-circle"></i>
          <span>一键配置后台</span>
        </div>
        <div class="highlight-item">
          <i class="fa fa-check-circle"></i>
          <span>数据安全保障</span>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 右侧登录区域 -->
  <div class="auth-section">
    <div class="auth-container">
      <!-- Logo区域 -->
      <div class="auth-header">
        <div class="auth-logo">
          <img src="{{ admin_asset('images/logo-baas.png') }}" alt="在行云">
        </div>
      </div>
      
      <!-- Tab导航 -->
      <div class="auth-tabs">
        <div class="tab-nav">
          <button class="tab-btn active" data-tab="login">账号登录</button>
          <button class="tab-btn" data-tab="register">手机注册</button>
        </div>
      </div>
      
      <!-- 表单容器 - 固定高度避免跳动 -->
      <div class="form-container">
        <!-- 登录表单 -->
        <div id="login-tab" class="tab-content active">
          <form action="{{ admin_url('auth/login') }}" method="post" class="auth-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="login_type" value="password" id="login_type">
            
            <div class="form-group {{ $errors->has('phone_number') ? 'has-error' : '' }}">
              <input type="text" class="form-control" placeholder="请输入手机号" name="phone_number" value="{{ old('phone_number') }}" required>
              @if($errors->has('phone_number'))
                <span class="error-text">{{ $errors->first('phone_number') }}</span>
              @endif
            </div>
            
            <!-- 密码输入 -->
            <div class="form-group password-input {{ $errors->has('password') ? 'has-error' : '' }}">
              <input type="password" class="form-control" placeholder="请输入密码" name="password" required>
              @if($errors->has('password'))
                <span class="error-text">{{ $errors->first('password') }}</span>
              @endif
            </div>
            
            <!-- 验证码输入 -->
            <div class="form-group code-input {{ $errors->has('verification_login_code') ? 'has-error' : '' }}" style="display: none;">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="请输入验证码" name="verification_login_code" value="{{ old('verification_login_code') }}">
                <button type="button" class="code-btn" id="get-login-code">获取验证码</button>
              </div>
              @if($errors->has('verification_login_code'))
                <span class="error-text">{{ $errors->first('verification_login_code') }}</span>
              @endif
            </div>
            
            @if(config('admin.auth.remember'))
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" name="remember" value="1" {{ (!old('username') || old('remember')) ? 'checked' : '' }}>
                <span class="checkmark"></span>
                {{ trans('admin.remember_me') }}
              </label>
            </div>
            @endif
            
            <button type="submit" class="submit-btn">{{ trans('admin.login') }}</button>
            
            <div class="form-footer">
              <a href="#" class="link forgot-password">忘记密码？</a>
              <a href="#" class="link back-to-password" style="display: none;">密码登录</a>
            </div>
          </form>
        </div>
        
        <!-- 注册表单 -->
        <div id="register-tab" class="tab-content">
          <form action="{{ admin_url('register') }}" method="post" class="auth-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <div class="form-group {{ $errors->has('register_phone') ? 'has-error' : '' }}">
              <input type="text" class="form-control" placeholder="请输入手机号" name="register_phone" value="{{ old('register_phone') }}" required>
              @if($errors->has('register_phone'))
                <span class="error-text">{{ $errors->first('register_phone') }}</span>
              @endif
            </div>
            
            <div class="form-group {{ $errors->has('verification_code') ? 'has-error' : '' }}">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="请输入验证码" name="verification_code" value="{{ old('verification_code') }}" required>
                <button type="button" class="code-btn" id="get-register-code">获取验证码</button>
              </div>
              @if($errors->has('verification_code'))
                <span class="error-text">{{ $errors->first('verification_code') }}</span>
              @endif
            </div>
            
            <div class="form-group {{ $errors->has('register_password') ? 'has-error' : '' }}">
              <input type="password" class="form-control" placeholder="请设置密码（6位以上）" name="register_password" required minlength="6">
              @if($errors->has('register_password'))
                <span class="error-text">{{ $errors->first('register_password') }}</span>
              @endif
            </div>
            
            <div class="form-group {{ $errors->has('register_password_confirmation') ? 'has-error' : '' }}">
              <input type="password" class="form-control" placeholder="请确认密码" name="register_password_confirmation" required minlength="6">
              @if($errors->has('register_password_confirmation'))
                <span class="error-text">{{ $errors->first('register_password_confirmation') }}</span>
              @endif
            </div>
            
            <div class="form-group {{ $errors->has('agree_terms') ? 'has-error' : '' }}">
              <label class="checkbox-label">
                <input type="checkbox" name="agree_terms" required>
                <span class="checkmark"></span>
                同意用户协议和隐私政策
              </label>
              @if($errors->has('agree_terms'))
                <span class="error-text">{{ $errors->first('agree_terms') }}</span>
              @endif
            </div>
            
            <button type="submit" class="submit-btn register-btn">立即注册</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery 2.1.4 -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js")}}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js")}}"></script>
<!-- iCheck -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js")}}"></script>
<!-- SweetAlert2 -->
<script src="{{ admin_asset('vendor/laravel-admin/sweetalert2/dist/sweetalert2.min.js') }}"></script>

<script>
$(document).ready(function() {
  // Tab切换
  $('.tab-btn').click(function() {
    var targetTab = $(this).data('tab');
    
    $('.tab-btn').removeClass('active');
    $(this).addClass('active');
    
    $('.tab-content').removeClass('active');
    $('#' + targetTab + '-tab').addClass('active');
    
    // 更新URL
    if (targetTab === 'register') {
      window.location.hash = '#tab_register';
    } else {
      window.location.hash = '';
    }
  });
  
  // 检查初始状态
  if (window.location.hash === '#tab_register' || {{ $errors->has('register_phone') ? 'true' : 'false' }}) {
    $('.tab-btn[data-tab="register"]').click();
  }
  
  // 检查是否需要显示验证码登录
  if ({{ $errors->has('verification_login_code') ? 'true' : 'false' }}) {
    showCodeMode();
  }
  
  // 忘记密码 - 切换到验证码模式
  $('.forgot-password').click(function(e) {
    e.preventDefault();
    showCodeMode();
  });
  
  // 返回密码登录
  $('.back-to-password').click(function(e) {
    e.preventDefault();
    showPasswordMode();
  });
  
  // 显示验证码模式（忘记密码）
  function showCodeMode() {
    $('.password-input').hide();
    $('.code-input').show();
    $('.forgot-password').hide();
    $('.back-to-password').show();
    $('#login_type').val('verification_code');
    
    // 移除密码字段的required属性，添加验证码字段的required属性
    $('input[name="password"]').removeAttr('required');
    $('input[name="verification_login_code"]').attr('required', 'required');
  }
  
  // 显示密码模式
  function showPasswordMode() {
    $('.code-input').hide();
    $('.password-input').show();
    $('.back-to-password').hide();
    $('.forgot-password').show();
    $('#login_type').val('password');
    
    // 恢复密码字段的required属性，移除验证码字段的required属性
    $('input[name="password"]').attr('required', 'required');
    $('input[name="verification_login_code"]').removeAttr('required');
  }
  
  // 发送验证码 - 恢复实际请求逻辑
  $('#get-login-code, #get-register-code').click(function() {
    var $btn = $(this);
    var $form = $btn.closest('form');
    var phone = $form.find('input[name="phone_number"], input[name="register_phone"]').val();
    
    if (!phone || !/^1\d{10}$/.test(phone)) {
      Swal.fire({
        icon: 'error',
        title: '输入错误',
        text: '请输入正确的手机号码'
      });
      return;
    }
    
    $btn.prop('disabled', true);
    
    // 判断是登录验证码还是注册验证码
    var isLoginCode = $btn.attr('id') === 'get-login-code';
    var url = isLoginCode ? '{{ admin_url("send-login-verification") }}' : '{{ admin_url("send-verification") }}';
    
    // 发送验证码请求
    $.ajax({
      url: url,
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        phone: phone
      },
      success: function(response) {
        if(response.success) {
          startCountdown($btn);
          Swal.fire({
            icon: 'success',
            title: '发送成功',
            text: '验证码已发送，请注意查收',
            timer: 2000,
            showConfirmButton: false
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: '发送失败',
            text: response.message || '发送失败，请稍后再试'
          });
          $btn.prop('disabled', false);
        }
      },
      error: function() {
        Swal.fire({
          icon: 'error',
          title: '发送失败',
          text: '网络错误，请稍后再试'
        });
        $btn.prop('disabled', false);
      }
    });
  });
  
  // 倒计时
  function startCountdown($btn) {
    var seconds = 60;
    $btn.prop('disabled', true).text(seconds + '秒后重发');
    
    var timer = setInterval(function() {
      seconds--;
      $btn.text(seconds + '秒后重发');
      
      if (seconds <= 0) {
        clearInterval(timer);
        $btn.prop('disabled', false).text('获取验证码');
      }
    }, 1000);
  }
});
</script>
</body>
</html>
