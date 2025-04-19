<?php

namespace App\SaaSAdmin\Controllers;

use App\Models\Tenant;
use Encore\Admin\Form;
use Encore\Admin\Form\Tools;
use Illuminate\Http\Request;
use Encore\Admin\Layout\Content;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Encore\Admin\Controllers\AuthController as BaseAuthController;

class AuthController extends BaseAuthController
{
    public function getLogin()
    {
        return view('login');
    }

    public function postLogin(Request $request)
    {
        // 获取登录类型
        $login_type = $request->input('login_type', 'password');
        
        // 基本表单验证规则
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
        ]);
        
        if ($login_type === 'password') {
            // 密码登录验证规则
            $validator->addRules([
                'password' => 'required',
            ]);
            
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }
            
            // 密码登录逻辑
            $credentials = $request->only(['phone_number', 'password']);
            $remember = $request->get('remember', false);
            
            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                return redirect()->intended(admin_url('/'));
            }
            
            return back()->withInput()->withErrors([
                'password' => ['密码不正确'],
            ]);
        } else {
            // 验证码登录验证规则
            $validator->addRules([
                'verification_login_code' => 'required',
            ]);
  
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator)->with('hash', '#mode_code');
            }
            
            // 验证码登录逻辑
            $phone = $request->input('phone_number');
            $code = $request->input('verification_login_code');
            
            // 验证码校验逻辑
            $cacheKey = 'verification_login_code_' . $phone;
            $cachedCode = \Cache::get($cacheKey);
            
            if (!$cachedCode || $cachedCode !== $code) {
                return back()->withInput()->withErrors([
                    'verification_login_code' => ['验证码错误或已过期'],
                ])->with('hash', '#mode_code');
            }
            
            // 验证码正确，查找用户
            $user = Tenant::where('phone_number', $phone)->first();
            
            if (!$user) {
                return back()->withInput()->withErrors([
                    'phone_number' => ['该手机号未注册'],
                ])->with('hash', '#mode_code');
            }
            
            // 验证码登录成功，清除缓存中的验证码
            \Cache::forget($cacheKey);
            
            // 直接登录用户
            Auth::guard('admin')->login($user, $request->get('remember', false));
            return redirect()->intended(admin_url('/'));
        }
    }

    public function getSetting(Content $content)
    {
        $form = $this->settingForm();
        $form->tools(
            function (Tools $tools) {
                $tools->disableList();
                $tools->disableDelete();
                $tools->disableView();
            }
        );

        return $content
            ->title(trans('admin.user_setting'))
            ->body($form->edit(SaasAdmin::user()->id));
    }

    /**
     * Update user setting.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putSetting()
    {
        return $this->settingForm()->update(SaaSAdmin::user()->id);
    }

    /**
     * Model-form for user setting.
     *
     * @return Form
     */
    protected function settingForm()
    {
        $class = config('admin.database.users_model');

        $form = new Form(new $class());

        $form->display('phone_number', '手机号');
        $form->text('nickname', '昵称')->rules('required');
        $form->image('avatar', '头像')
            ->rules(['required', 'file', 'max:1024', 'mimes:jpg,png,jpeg'])
            ->move('avatar', function ($file) use ($form) {
                return 'avatar_'.$form->model()->id.'.'.$file->getClientOriginalExtension();
            });
        $form->password('password', '密码')->rules('confirmed|required');
        $form->password('password_confirmation', '确认密码')->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->setAction(admin_url('auth/setting'));

        $form->ignore(['password_confirmation']);

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        $form->saved(function () {
            admin_toastr(trans('admin.update_succeeded'));

            return redirect(admin_url('auth/setting'));
        });

        $form->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });

        return $form;
    }
}
