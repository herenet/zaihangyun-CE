<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Form\Tools;
use Illuminate\Http\Request;
use Encore\Admin\Layout\Content;
use App\Admin\Facades\AdminFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        // 基本表单验证规则
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }
        
        // 密码登录逻辑
        $credentials = $request->only(['username', 'password']);
        $remember = $request->get('remember', false);
        
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            return redirect()->intended(admin_url('/'));
        }
        
        return back()->withInput()->withErrors([
            'password' => ['用户名或密码不正确'],
        ]);
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
            ->body($form->edit(AdminFacade::user()->id));
    }

    /**
     * Update user setting.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putSetting()
    {
        return $this->settingForm()->update(AdminFacade::user()->id);
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
        $form->text('nickname', '昵称')->rules('required');
        $form->image('avatar', '头像')
            ->rules(['required', 'file', 'max:512', 'mimes:jpg,png,jpeg'])
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
