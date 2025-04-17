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
    .nav-tabs-custom>.nav-tabs>li.active {
      border-top-color: #3c8dbc;
    }
    .countdown {
      color: #999;
      font-size: 12px;
      margin-left: 5px;
    }
    .verification-code-btn {
      width: 100%;
    }
  </style>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- 在 </head> 标签前添加 -->
  <link href="{{ admin_asset('vendor/laravel-admin/sweetalert2/dist/sweetalert2.css') }}" rel="stylesheet">
</head>
<body class="hold-transition login-page" @if(config('admin.login_background_image'))style="background: url({{config('admin.login_background_image')}}) no-repeat;background-size: cover;"@endif>
<div class="login-box" style="width:460px">
  <div class="login-logo">
    <a href="{{ admin_url('/') }}"><b>{{config('admin.name')}}</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body" style="width:460px;margin:auto;">
    
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_login" data-toggle="tab">登录</a></li>
        <li><a href="#tab_register" data-toggle="tab">注册</a></li>
      </ul>
      <div class="tab-content">
        <!-- 登录 Tab -->
        <div class="tab-pane active" id="tab_login">
          <p class="login-box-msg">请登录</p>
          <form action="{{ admin_url('auth/login') }}" method="post">
            <div class="form-group has-feedback {!! !$errors->has('phone_number') ?: 'has-error' !!}">
              @if($errors->has('phone_number'))
                @foreach($errors->get('phone_number') as $message)
                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                @endforeach
              @endif
              <input type="number" class="form-control" placeholder="手机号" name="phone_number" value="{{ old('phone_number') }}">
              <span class="glyphicon glyphicon-phone form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}">
              @if($errors->has('password'))
                @foreach($errors->get('password') as $message)
                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                @endforeach
              @endif
              <input type="password" class="form-control" placeholder="密码" name="password">
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
              <div class="col-xs-8">
                @if(config('admin.auth.remember'))
                <div class="checkbox icheck">
                  <label>
                    <input type="checkbox" name="remember" value="1" {{ (!old('username') || old('remember')) ? 'checked' : '' }}>
                    {{ trans('admin.remember_me') }}
                  </label>
                </div>
                @endif
              </div>
              <!-- /.col -->
              <div class="col-xs-4">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('admin.login') }}</button>
              </div>
              <!-- /.col -->
            </div>
          </form>
        </div>
        
        <!-- 注册 Tab -->
        <div class="tab-pane" id="tab_register">
          <p class="login-box-msg">注册新账号</p>
          <form action="{{ admin_url('register') }}#tab_register" method="post" id="register-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <div class="form-group has-feedback {!! !$errors->has('register_phone') ?: 'has-error' !!}">
              @if($errors->has('register_phone'))
                @foreach($errors->get('register_phone') as $message)
                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                @endforeach
              @endif
              <input type="number" class="form-control" placeholder="手机号" name="register_phone" id="register_phone" value="{{ old('register_phone') }}" required>
              <span class="glyphicon glyphicon-phone form-control-feedback"></span>
            </div>
            
            <div class="form-group has-feedback {!! !$errors->has('verification_code') ?: 'has-error' !!}">
              @if($errors->has('verification_code'))
                @foreach($errors->get('verification_code') as $message)
                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                @endforeach
              @endif
              <div class="row">
                <div class="col-xs-8">
                  <input type="text" class="form-control" placeholder="验证码" name="verification_code" value="{{ old('verification_code') }}" required>
                </div>
                <div class="col-xs-4">
                  <button type="button" class="btn btn-default verification-code-btn" id="send-code-btn">获取验证码</button>
                </div>
              </div>
            </div>
            
            <div class="form-group has-feedback {!! !$errors->has('register_password') ?: 'has-error' !!}">
              @if($errors->has('register_password'))
                @foreach($errors->get('register_password') as $message)
                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                @endforeach
              @endif
              <input type="password" class="form-control" placeholder="设置密码" name="register_password" required minlength="6">
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            
            <div class="form-group has-feedback {!! !$errors->has('register_password_confirmation') ?: 'has-error' !!}">
              @if($errors->has('register_password_confirmation'))
                @foreach($errors->get('register_password_confirmation') as $message)
                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                @endforeach
              @endif
              <input type="password" class="form-control" placeholder="确认密码" name="register_password_confirmation" required minlength="6">
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            
            <div class="form-group has-feedback {!! !$errors->has('agree_terms') ?: 'has-error' !!}">
              <div class="checkbox icheck">
                <label>
                  <input type="checkbox" name="agree_terms" required> 同意用户协议和隐私政策
                </label>
              </div>
              @if($errors->has('agree_terms'))
                @foreach($errors->get('agree_terms') as $message)
                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                @endforeach
              @endif
            </div>
            
            <div class="row">
              <div class="col-xs-8">
                <!-- 可以在这里添加其他注册信息 -->
              </div>
              <!-- /.col -->
              <div class="col-xs-4">
                <button type="submit" class="btn btn-success btn-block btn-flat">注册</button>
              </div>
              <!-- /.col -->
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js")}}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js")}}"></script>
<!-- iCheck -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js")}}"></script>

<!-- 在 jQuery 后、其他脚本前添加 -->
<script src="{{ admin_asset('vendor/laravel-admin/sweetalert2/dist/sweetalert2.min.js') }}"></script>

<script>
  $(function () {
    // 检查 URL 中是否包含 #tab_register 片段
    if (window.location.hash === '#tab_register') {
      $('.nav-tabs a[href="#tab_register"]').tab('show');
    }
    
    // 添加标签页切换事件监听器，当标签页切换时更新 URL
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
      // 获取当前激活的标签页的 href 属性（包含 # 符号）
      var activeTab = $(e.target).attr('href');
      
      // 更新 URL 的片段（fragment）
      if (activeTab === '#tab_login') {
        // 如果是登录标签页，可以移除片段
        if (window.history && window.history.pushState) {
          // 使用 HTML5 历史 API 修改 URL 而不触发页面刷新
          window.history.pushState('', document.title, window.location.pathname + window.location.search);
        } else {
          // 兼容旧浏览器
          window.location.hash = '';
        }
      } else {
        // 如果是注册标签页，添加 #tab_register 片段
        window.location.hash = activeTab;
      }
    });
    
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
    
    // 验证码发送功能
    $('#send-code-btn').click(function() {
      var phone = $('#register_phone').val();
      if(!phone || !/^1\d{10}$/.test(phone)) {
        // 使用 Laravel-admin 内置的 Swal
        Swal.fire({
          icon: 'error',
          title: '输入错误',
          text: '请输入正确的手机号码'
        });
        return;
      }
      
      var btn = $(this);
      btn.prop('disabled', true);
      
      // 发送验证码请求
      $.ajax({
        url: '{{ admin_url("send-verification") }}',
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          phone: phone
        },
        success: function(response) {
          if(response.success) {
            startCountdown(btn);
          } else {
            Swal.fire({
              icon: 'error',
              title: '发送失败',
              text: response.message || '发送失败，请稍后再试'
            });
            btn.prop('disabled', false);
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: '发送失败',
            text: '网络错误，请稍后再试'
          });
          btn.prop('disabled', false);
        }
      });
    });
    
    // 验证码倒计时
    function startCountdown(btn) {
      var seconds = 60;
      btn.html(seconds + '秒后获取');
      
      var timer = setInterval(function() {
        seconds--;
        btn.html(seconds + '秒后获取');
        
        if(seconds <= 0) {
          clearInterval(timer);
          btn.html('获取验证码');
          btn.prop('disabled', false);
        }
      }, 1000);
    }
    
    // 表单验证
    $('#register-form').submit(function(e) {
      var password = $('input[name="register_password"]').val();
      var confirmation = $('input[name="register_password_confirmation"]').val();
      
      if(password !== confirmation) {
        Swal.fire({
          icon: 'error',
          title: '密码不一致',
          text: '两次输入的密码不一致'
        });
      
        e.preventDefault();
        return false;
      }
      
      return true;
    });
  });
</script>
</body>
</html>
