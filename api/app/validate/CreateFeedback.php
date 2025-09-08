<?php

namespace app\validate;

use app\model\Feedback;
use app\lib\ZHYVolidate;


class CreateFeedback extends ZHYVolidate
{
    protected $rule = [];
    protected $message = [];

    public function __construct()
    {
        $this->rule = [
            'content' => 'require|string|max:255',
            'type' => 'require|integer|in:'.implode(',', array_keys(Feedback::$typeMap)),
            'contact' => 'string|max:64',
        ];

        $this->message = [
            'content.required' => '400104|content must be required',
            'content.string' => '400105|content must be string',
            'content.max' => '400106|content must be less than 255 characters',
            'type.required' => '400107|type must be required',
            'type.integer' => '400108|type must be integer',
            'type.in' => '400109|type must be in:'.implode(',', array_keys(Feedback::$typeMap)),
            'contact.string' => '400110|contact must be string',
            'contact.max' => '400111|contact must be less than 64 characters',
        ];
    }
}