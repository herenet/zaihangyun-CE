<?php
/**
 * 手动执行定时任务脚本
 * 使用方法：
 * php manual_task.php clearCanceledUser
 * php manual_task.php syncApiStats
 * php manual_task.php cleanExpiredApiStats
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/support/bootstrap.php';

use app\process\Task;

if ($argc < 2) {
    echo "使用方法:\n";
    echo "php manual_task.php clearCanceledUser    # 清理取消的用户\n";
    echo "php manual_task.php syncApiStats         # 同步API统计数据\n";
    echo "php manual_task.php cleanExpiredApiStats # 清理过期的Redis统计数据\n";
    exit(1);
}

$taskName = $argv[1];
$task = new Task();

echo "开始执行任务: {$taskName}\n";
echo "执行时间: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat('-', 50) . "\n";

try {
    switch ($taskName) {
        case 'clearCanceledUser':
            $task->clearCanceledUser();
            echo "✅ 清理取消用户任务执行完成\n";
            break;
            
        case 'syncApiStats':
            $task->syncApiStats();
            echo "✅ API统计数据同步任务执行完成\n";
            break;
            
        case 'cleanExpiredApiStats':
            $task->cleanExpiredApiStats();
            echo "✅ 清理过期Redis统计数据任务执行完成\n";
            break;
            
        default:
            echo "❌ 未知的任务名称: {$taskName}\n";
            exit(1);
    }
    
    echo str_repeat('-', 50) . "\n";
    echo "任务执行完成时间: " . date('Y-m-d H:i:s') . "\n";
    
} catch (Exception $e) {
    echo "❌ 任务执行失败: " . $e->getMessage() . "\n";
    echo "错误文件: " . $e->getFile() . "\n";
    echo "错误行号: " . $e->getLine() . "\n";
    exit(1);
} 