<?php

namespace app\controller\api;

use support\Request;
use app\model\Feedback;
use app\model\MessageConfig;
use app\validate\FeedbackList;
use app\validate\CreateFeedback;

class MessageController
{
    

    public function feedback(Request $request)
    {
        $token_info = $request->token_info;
        $appkey = $token_info['app_key'];
        $uid = $token_info['uid'];

        $message_config_model = new MessageConfig();
        $message_config = $message_config_model->getMessageConfigByAppKey($appkey);

        if(empty($message_config) || $message_config['switch'] == 0){
            return json(['code' => 400101, 'msg' => 'message interface is not enabled']);
        }

        $validate = new CreateFeedback();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $data = [
            'app_key' => $appkey,
            'uid' => $uid,
            'content' => $request->input('content'),
            'type' => $request->input('type'),
            'contact' => $request->input('contact'),
        ];

        $feedback_model = new Feedback();
        if($feedback_model->createFeedback($data)){
            return json(['code' => config('const.request_success'), 'msg' => 'success']);
        }
        return json(['code' => 400250, 'msg' => 'create feedback failed']);
    }

    public function myFeedbackList(Request $request)
    {
        $token_info = $request->token_info;
        $appkey = $token_info['app_key'];
        $uid = $token_info['uid'];
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $message_config_model = new MessageConfig();
        $message_config = $message_config_model->getMessageConfigByAppKey($appkey);

        if(empty($message_config) || $message_config['switch'] == 0){
            return json(['code' => 400101, 'msg' => 'message interface is not enabled']);   
        }

        if($page < 1){
            return json(['code' => 400102, 'msg' => 'page must be greater than 0']);
        }

        if($page_size < 1){
            return json(['code' => 400103, 'msg' => 'page_size must be greater than 0']);
        }

        if($page_size > 100){
            return json(['code' => 400104, 'msg' => 'page_size must be less than 100']);
        }
        

        $start = ($page - 1) * $page_size;
        $feedback_model = new Feedback();
        $total = $feedback_model->getCount($appkey, $uid);
        $feedback_list = $feedback_model->getFeedbackListByUid($appkey, $uid, $start, $page_size);
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => [
            'page' => $page,
            'page_size' => $page_size,
            'total' => $total,
            'list' => $feedback_list
        ]]);
    }  
    
    public function feedbackList(Request $request)
    {
        $token_info = $request->token_info;
        $appkey = $token_info['app_key'];
        $uid = $token_info['uid'];
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);
        $need_reply = $request->input('need_reply', 0);
        $need_contact = $request->input('need_contact', 0);

        $message_config_model = new MessageConfig();
        $message_config = $message_config_model->getMessageConfigByAppKey($appkey);

        if(empty($message_config) || $message_config['switch'] == 0){
            return json(['code' => 400199, 'msg' => 'message interface is not enabled']);   
        }

        $validate = new FeedbackList();
        if (!$validate->check($request->all())) {
            return json($validate->getErrorInfo());
        }

        $start = ($page - 1) * $page_size;
        $feedback_model = new Feedback();
        $total = $feedback_model->getCount($appkey, $uid);
        $fields = ['id', 'content', 'type', 'updated_at', 'created_at'];
        if($need_reply == 1){
            $fields[] = 'reply';
        }
        if($need_contact == 1){
            $fields[] = 'contact';
        }
        $feedback_list = $feedback_model->getFeedbackList($appkey, $start, $page_size, $fields);
        
        return json(['code' => config('const.request_success'), 'msg' => 'success', 'data' => [
            'page' => $page,
            'page_size' => $page_size,
            'total' => $total,
            'list' => $feedback_list
        ]]);
    }
}