<?php

namespace app\validate;

use app\lib\ZHYVolidate;

class FeedbackList extends ZHYVolidate
{
    protected $rule = [
        'page' => 'integer|min:1',
        'page_size' => 'integer|min:1|max:100',
        'need_reply' => 'integer|in:0,1',
        'need_contact' => 'integer|in:0,1',
    ];

    protected $message = [
        'page.integer' => '400101|page must be integer',
        'page.min' => '400102|page must be greater than 0',
        'page_size.integer' => '400103|page_size must be integer',
        'page_size.min' => '400104|page_size must be greater than 0',
        'page_size.max' => '400105|page_size must be less than 100',
        'need_reply.integer' => '400106|need_reply must be integer',
        'need_reply.in' => '400107|need_reply must be in 0,1',
        'need_contact.integer' => '400108|need_contact must be integer',
        'need_contact.in' => '400109|need_contact must be in 0,1',
    ];
}