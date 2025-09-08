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

    /* 左侧品牌区域 */
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
      background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"10\" cy=\"10\" r=\"1\" fill=\"rgba(255,255,255,0.05)\"/><circle cx=\"90\" cy=\"20\" r=\"1\" fill=\"rgba(255,255,255,0.03)\"/><circle cx=\"30\" cy=\"40\" r=\"1\" fill=\"rgba(255,255,255,0.04)\"/><circle cx=\"70\" cy=\"60\" r=\"1\" fill=\"rgba(255,255,255,0.02)\"/><circle cx=\"20\" cy=\"80\" r=\"1\" fill=\"rgba(255,255,255,0.05)\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>') repeat;
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

    /* 右侧登录区域 */
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
      padding: 50px 30px 40px;
    }

    .auth-logo img {
      height: 80px;
      width: auto;
    }

    .auth-title {
      font-size: 24px;
      font-weight: 600;
      color: #1F2937;
      margin-top: 20px;
      margin-bottom: 8px;
    }

    .auth-subtitle {
      font-size: 14px;
      color: #6B7280;
      margin-bottom: 0;
    }

    /* 表单容器 */
    .form-container {
      padding: 0 30px 40px;
      position: relative;
    }

    /* 表单内容 */
    .auth-form {
      width: 100%;
    }

    /* 表单组 */
    .form-group {
      margin-bottom: 24px;
    }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      color: #374151;
      margin-bottom: 8px;
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
      box-sizing: border-box;
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

    /* 复选框 */
    .checkbox-group {
      margin-bottom: 30px;
    }

    .checkbox-label {
      display: flex;
      align-items: center;
      font-size: 14px;
      color: #6B7280;
      cursor: pointer;
      user-select: none;
    }

    .checkbox-label input[type=\"checkbox\"] {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    }

    .checkmark {
      width: 18px;
      height: 18px;
      border: 2px solid #E5E7EB;
      border-radius: 4px;
      margin-right: 10px;
      position: relative;
      transition: all 0.3s ease;
      flex-shrink: 0;
    }

    .checkbox-label input[type=\"checkbox\"]:checked + .checkmark {
      background: #4086F5;
      border-color: #4086F5;
    }

    .checkbox-label input[type=\"checkbox\"]:checked + .checkmark::after {
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
      background: linear-gradient(135deg, #4086F5 0%, #6B9BF7 100%);
      color: white;
      box-shadow: 0 4px 12px rgba(64, 134, 245, 0.3);
    }

    .submit-btn:hover {
      background: linear-gradient(135deg, #3574E3 0%, #5A8AF6 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(64, 134, 245, 0.4);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    /* 错误信息 */
    .has-error .form-control {
      border-color: #EF4444;
      background: #FEF2F2;
    }

    .error-text {
      color: #EF4444;
      font-size: 13px;
      margin-top: 6px;
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
        margin: 0;
      }
      
      .auth-header {
        padding: 40px 20px 30px;
      }
      
      .form-container {
        padding: 0 20px 30px;
      }
    }

    @media (max-width: 480px) {
      .auth-logo img {
        height: 70px;
      }
      
      .auth-title {
        font-size: 20px;
      }
      
      .form-control, .submit-btn {
        height: 44px;
      }
      
      .auth-header {
        padding: 30px 20px 25px;
      }
    }
  </style>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src=\"//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>
  <script src=\"//oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>
  <![endif]-->
</head>
<body class="login-page">

<div class="login-container">
  <!-- 左侧品牌区域 -->
  <div class="brand-section">
    <!-- 左上角logo -->
    <div class="brand-logo-corner">
      <a href="/"><img src="{{ admin_asset('images/logo-mini.png') }}" alt="在行云"></a>
    </div>
    
    <div class="brand-content">
      <!-- 主标题 -->
      <h1 class="brand-title">在行云BaaS</h1>
      
      <p class="brand-subtitle">无需后端开发，APP快速构建后端能力</p>
      
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
          <div class="feature-desc">集成支付宝、微信、Apple IAP等主流支付</div>
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
        <h2 class="auth-title">管理员登录</h2>
        <p class="auth-subtitle">请输入您的账号信息</p>
      </div>
      
      <!-- 表单容器 -->
      <div class="form-container">
        <form action="{{ admin_url('auth/login') }}" method="post" class="auth-form">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          
          <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
            <label class="form-label">用户名</label>
            <input type="text" class="form-control" placeholder="请输入用户名" name="username" value="{{ old('username') }}" required autofocus>
            @if($errors->has('username'))
              <span class="error-text">{{ $errors->first('username') }}</span>
            @endif
          </div>
          
          <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
            <label class="form-label">密码</label>
            <input type="password" class="form-control" placeholder="请输入密码" name="password" required>
            @if($errors->has('password'))
              <span class="error-text">{{ $errors->first('password') }}</span>
            @endif
          </div>
          
          @if(config('admin.auth.remember'))
          <div class="checkbox-group">
            <label class="checkbox-label">
              <input type="checkbox" name="remember" value="1" {{ (!old('username') || old('remember')) ? 'checked' : '' }}>
              <span class="checkmark"></span>
              {{ trans('admin.remember_me') }}
            </label>
          </div>
          @endif
          
          <button type="submit" class="submit-btn">{{ trans('admin.login') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- jQuery 2.1.4 -->
<script src="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ admin_asset('vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js') }}"></script>

</body>
</html>
