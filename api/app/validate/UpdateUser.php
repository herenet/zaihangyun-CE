<?php

namespace app\validate;

use app\lib\ZHYVolidate;
class UpdateUser extends ZHYVolidate
{
    protected $rule = [
        'nickname' => 'string|max:32',
        'gender' => 'integer|in:0,1,2',
        'birthday' => 'date',
        'oaid' => 'string|max:128',
        'device_id' => 'string|max:128',
        'password' => 'string|min:6|max:32',
        'email' => 'email',
        'country' => 'string|max:32',
        'province' => 'string|max:32',
        'city' => 'string|max:32',
        'enter_pass' => 'string|min:4|max:8',
        'ext_data' => 'json|max:200',
    ];
    
    protected $message = [
        'nickname.string' => '400101|nickname must be a string',
        'nickname.max' => '400102|nickname length must be less than 32 characters',
        'gender.integer' => '400103|gender must be an integer',
        'gender.in' => '400105|gender must be in 0,1,2',
        'birthday.date' => '400106|birthday must be a date',
        'oaid.string' => '400107|oaid must be a string',
        'oaid.max' => '400108|oaid length must be less than 128 characters',
        'device_id.string' => '400109|device_id must be a string',
        'device_id.max' => '400110|device_id length must be less than 128 characters',
        'password.string' => '400113|password must be a string',
        'password.min' => '400114|password length must be greater than 6 characters',
        'password.max' => '400115|password length must be less than 32 characters',
        'email.email' => '400116|email must be a valid email address',
        'country.string' => '400117|country must be a string',
        'country.max' => '400118|country length must be less than 32 characters',
        'province.string' => '400119|province must be a string',
        'province.max' => '400120|province length must be less than 32 characters',
        'city.string' => '400121|city must be a string',
        'city.max' => '400122|city length must be less than 32 characters',
        'enter_pass.string' => '400123|enter_pass must be a string',
        'enter_pass.min' => '400124|enter_pass length must be greater than 4 characters',
        'enter_pass.max' => '400125|enter_pass length must be less than 8 characters',
        'ext_data.json' => '400126|ext_data must be a valid JSON',
        'ext_data.max' => '400127|ext_data length must be less than 200 characters',
    ];
}