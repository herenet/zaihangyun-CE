<?php

namespace App\SaaSAdmin\Controllers;

use App\Libs\Helpers;
use App\Models\Tenant;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * 处理注册请求
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // 验证表单数据
        $validator = Validator::make($request->all(), [
            'register_phone' => 'required|regex:/^1\d{10}$/',
            'verification_code' => 'required|string',
            'register_password' => 'required|string|min:6|max:20',
            'register_password_confirmation' => 'required|same:register_password',
            'agree_terms' => 'required|accepted',
        ], [
            'register_phone.required' => '手机号不能为空',
            'register_phone.regex' => '请输入正确的手机号',
            'register_phone.unique' => '该手机号已被注册',
            'verification_code.required' => '验证码不能为空',
            'register_password.required' => '密码不能为空',
            'register_password.min' => '密码长度不能少于6位',
            'register_password.max' => '密码长度不能超过20位',
            'register_password_confirmation.required' => '确认密码不能为空',
            'register_password_confirmation.same' => '两次输入的密码不一致',
            'agree_terms.required' => '请同意用户协议和隐私政策',
            'agree_terms.accepted' => '请同意用户协议和隐私政策',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withFragment('tab_register')
                ->withInput();
        }

        // 验证验证码
        $phone = $request->input('register_phone');
        $code = $request->input('verification_code');
        $cacheKey = 'verification_code_' . $phone;
        $cachedCode = Cache::get($cacheKey);

        if (!$cachedCode || $cachedCode !== $code) {
            return back()
                ->withErrors(['verification_code' => '验证码错误或已过期'])
                ->withFragment('tab_register')
                ->withInput();
        }

        // 创建用户
        try {
            $admin = new Tenant();
            $admin->id = Helpers::generateUserId();
            $avatar_path = 'avatar/'.$admin->id.'.png'; 
            Helpers::generateAndSaveAvatar($phone, $avatar_path);

            $admin->phone_number = $phone;
            $admin->nickname = '用户' . substr($phone, -4);
            $admin->password = Hash::make($request->input('register_password')); // 使用MD5加密
            $admin->avatar = $avatar_path;
            $admin->save();

            // 删除验证码缓存
            Cache::forget($cacheKey);

            // 自动登录
            $credentials = [
                'phone_number' => $phone,
                'password' => $request->input('register_password'),
            ];

            if (Auth::attempt($credentials)) {
                // 使用intended()方法，这样会自动跳转到用户之前想访问的页面
                return redirect()->intended(config('admin.route.prefix'));
            }

            return redirect(admin_url('auth/login'))
                ->with('success', '注册成功，请登录');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['register_phone' => '注册失败，请稍后再试: ' . $e->getMessage()])
                ->withFragment('tab_register')
                ->withInput();
        }
    }

    /**
     * 发送短信验证码
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendVerification(Request $request)
    {
        $phone = $request->input('phone');

        // 验证手机号
        if (!preg_match('/^1\d{10}$/', $phone)) {
            return response()->json([
                'success' => false,
                'message' => '请输入正确的手机号码'
            ]);
        }

        // 检查手机号是否已注册
        if (Tenant::where('phone_number', $phone)->exists()) {
            return response()->json([
                'success' => false,
                'message' => '该手机号已被注册'
            ]);
        }

        // 检查发送频率限制
        $throttleKey = 'verification_throttle_' . $phone;
        if (Cache::has($throttleKey)) {
            return response()->json([
                'success' => false,
                'message' => '发送过于频繁，请稍后再试'
            ]);
        }

        // 生成验证码
        $code = mt_rand(100000, 999999);
        $cacheKey = 'verification_code_' . $phone;

        try {
            app(SmsService::class)->sendVerifyCode($phone, $code);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '发送失败，请稍后再试'
            ]);
        }

        // 开发环境下，可以直接打印验证码到日志
        \Log::info("验证码: {$code} 已发送到手机号: {$phone}");

        // 缓存验证码，有效期 5 分钟
        Cache::put($cacheKey, $code, 300);
        
        // 设置发送频率限制，1 分钟内不能重复发送
        Cache::put($throttleKey, 1, 60);

        return response()->json([
            'success' => true,
            'message' => '验证码已发送',
        ]);
    }


    public function sendLoginVerification(Request $request)
    {
        $phone = $request->input('phone');

        // 验证手机号
        if (!preg_match('/^1\d{10}$/', $phone)) {
            return response()->json([
                'success' => false,
                'message' => '请输入正确的手机号码'
            ]);
        }

        // 检查手机号是否已注册
        if (!Tenant::where('phone_number', $phone)->exists()) {
            return response()->json([
                'success' => false,
                'message' => '该手机号未注册'
            ]);
        }

        // 检查发送频率限制
        $throttleKey = 'verification_throttle_' . $phone;
        if (Cache::has($throttleKey)) {
            return response()->json([
                'success' => false,
                'message' => '发送过于频繁，请稍后再试'
            ]);
        }

        // 生成验证码
        $code = mt_rand(100000, 999999);
        $cacheKey = 'verification_login_code_' . $phone;

        try {
            app(SmsService::class)->sendVerifyCode($phone, $code);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '发送失败，请稍后再试'
            ]);
        }

        // 开发环境下，可以直接打印验证码到日志
        \Log::info("验证码: {$code} 已发送到手机号: {$phone}");

        // 缓存验证码，有效期 5 分钟
        Cache::put($cacheKey, $code, 300);
        
        // 设置发送频率限制，1 分钟内不能重复发送
        Cache::put($throttleKey, 1, 60);

        return response()->json([
            'success' => true,
            'message' => '验证码已发送',
        ]);// 验证手机号
        
    }
}