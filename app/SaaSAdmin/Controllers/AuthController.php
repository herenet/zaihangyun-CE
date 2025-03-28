<?php

namespace App\SaaSAdmin\Controllers;

use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseAuthController
{
    public function getLogin()
    {
        return view('login');
    }

    public function postLogin(Request $request)
    {
        $credentials = $request->only('phone_number', 'password');
        if(empty($credentials['phone_number']) || empty($credentials['password'])){
            return redirect()->back()->withErrors(['phone_number' => '手机号或密码不能为空.']);
        }

        if (Auth::attempt($credentials)) {
            return redirect()->intended('admin');
        }

        return redirect()->back()->withErrors(['phone_number' => 'These credentials do not match our records.']);
    }
}
