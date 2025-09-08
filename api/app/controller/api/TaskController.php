<?php

namespace app\controller\api;

use app\process\Task;
use Webman\Http\Request;
use Webman\Http\Response;

class TaskController
{
    /**
     * 手动执行清理注销用户任务
     */
    public function clearCanceledUser(Request $request): Response
    {
        try {
            $task = new Task();
            $task->clearCanceledUser();
            
            return json([
                'code' => 200,
                'msg' => '清理注销用户任务执行完成',
                'time' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg' => '任务执行失败: ' . $e->getMessage()
            ]);
        }
    }
} 