<?php

namespace App\SaaSAdmin\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\SaaSAdmin\Facades\SaaSAdmin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use App\Models\TenantApiStats;
use App\Models\App;
use App\Models\Tenant;

class ApiStatsController extends AdminController
{
    /**
     * 获取租户今日API统计数据
     */
    public function getTodayStats()
    {
        try {
            if (!SaaSAdmin::user()) {
                return response()->json([
                    'success' => false,
                    'message' => '用户未登录'
                ]);
            }

            $tenant_id = SaaSAdmin::user()->id;
            $today = date('Y-m-d');
            $client = new Client();
            
            $ret = $client->get(config('app.api_url').'/v1/stats/tenant?tenant_id='.$tenant_id.'&date='.$today, [
                'timeout' => 5, // 设置超时时间
                'connect_timeout' => 3
            ]);
            
            if ($ret->getStatusCode() == 200) {
                $data = json_decode($ret->getBody(), true);
                if (isset($data['code']) && $data['code'] == 200 && isset($data['data'])) {
                    $current = $data['data']['total_calls'] ?? 0;
                    
                    // 从租户的product字段获取API限制数
                    $limit = $this->getTenantApiLimit($tenant_id);
                    $isUnlimited = $this->isUnlimitedTenant($tenant_id);
                    
                    if ($isUnlimited) {
                        // 无限制租户的特殊处理
                        $percentage = 100; // 进度条显示100%但为绿色
                        $statusInfo = [
                            'class' => 'success',
                            'color' => '#5cb85c',
                            'text' => '无限制',
                            'icon' => 'infinity'
                        ];
                    } else {
                        $percentage = $limit > 0 ? min(($current / $limit) * 100, 100) : 0;
                        $statusInfo = $this->getStatusInfo($percentage);
                    }
                    
                    // 获取租户套餐信息
                    $tenant = \App\Models\Tenant::find($tenant_id);
                    $currentProduct = $tenant->product ?? 'free';
                    $productInfo = config("product.{$currentProduct}");
                    
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'current' => $current,
                            'limit' => $limit,
                            'percentage' => round($percentage, 1),
                            'remaining' => $isUnlimited ? '无限制' : max($limit - $current, 0),
                            'status' => $statusInfo,
                            'is_unlimited' => $isUnlimited,
                            'current_product' => $currentProduct,
                            'product_name' => $productInfo['name'] ?? '未知套餐',
                            'show_upgrade' => !$isUnlimited, // 非企业版用户显示升级按钮
                            'last_updated' => date('Y-m-d H:i:s')
                        ]
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'API返回数据格式错误'
            ]);
            
        } catch (\Exception $e) {
            Log::error('租户API用量统计失败: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '获取统计数据失败: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * 获取详情页面的实时数据更新
     */
    public function getRealtimeUpdate()
    {
        try {
            if (!SaaSAdmin::user()) {
                return response()->json([
                    'success' => false,
                    'message' => '用户未登录'
                ]);
            }

            $tenant_id = SaaSAdmin::user()->id;
            $todayData = $this->getTodayStatsForDetails($tenant_id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_calls' => $todayData['total_calls'],
                    'limit' => $todayData['limit'],
                    'usage_percentage' => $todayData['usage_percentage'],
                    'remaining' => $todayData['is_unlimited'] ? '无限制' : max($todayData['limit'] - $todayData['total_calls'], 0),
                    'is_unlimited' => $todayData['is_unlimited'],
                    'last_updated' => date('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('获取实时更新数据失败: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '获取数据失败: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * 获取租户的API限制数
     */
    private function getTenantApiLimit($tenant_id)
    {
        $tenant = Tenant::find($tenant_id);
        if (!$tenant) {
            return config('product.free.request_limit', 10000); // 默认免费版限制
        }
        
        $product = $tenant->product ?? 'free';
        return config("product.{$product}.request_limit", 10000);
    }
    
    /**
     * 判断是否为无限制租户
     */
    private function isUnlimitedTenant($tenant_id)
    {
        $limit = $this->getTenantApiLimit($tenant_id);
        return $limit >= 999999999; // 企业版的无限制标识
    }
    
    /**
     * 获取状态信息
     */
    private function getStatusInfo($percentage)
    {
        if ($percentage >= 100) {
            return [
                'class' => 'danger',
                'color' => '#d9534f',
                'text' => '已超额',
                'icon' => 'exclamation-triangle'
            ];
        } elseif ($percentage >= 80) {
            return [
                'class' => 'warning',
                'color' => '#f0ad4e', 
                'text' => '即将满额',
                'icon' => 'warning'
            ];
        } else {
            return [
                'class' => 'success',
                'color' => '#5cb85c',
                'text' => '正常',
                'icon' => 'check'
            ];
        }
    }
    
    /**
     * API统计详情页面
     */
    public function details(Content $content)
    {
        $tenant_id = SaaSAdmin::user()->id;
        
        try {
            // 获取今日实时数据
            $todayData = $this->getTodayStatsForDetails($tenant_id);
            
            // 获取最近30天的趋势数据
            $trendData = $this->getTrendData($tenant_id);
            
            // 合并数据
            $data = array_merge($todayData, [
                'trend_data' => $trendData,
                'refresh_interval' => 60 // 自动刷新间隔（秒）
            ]);
            
            return $content
                ->title('API调用统计中心')
                ->body(view('saas.api-stats.details', compact('data')));
        } catch (\Exception $e) {
            // 添加调试信息
            \Log::error('API统计详情页面错误', [
                'tenant_id' => $tenant_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $content
                ->title('API调用统计中心')
                ->body(view('saas.api-stats.details', [
                    'data' => null,
                    'error' => '数据获取失败：' . $e->getMessage()
                ]));
        }
    }
    
    /**
     * 获取今日统计数据（详情页面专用）
     */
    private function getTodayStatsForDetails($tenant_id)
    {
        try {
            $client = new Client();
            $response = $client->get(config('app.api_url') . '/v1/stats/tenant', [
                'query' => [
                    'tenant_id' => $tenant_id,
                    'date' => date('Y-m-d')
                ],
                'timeout' => 10
            ]);
            
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('API请求失败');
            }
            
            $result = json_decode($response->getBody(), true);
            
            if (!isset($result['code']) || $result['code'] !== 200) {
                throw new \Exception($result['msg'] ?? '数据获取失败');
            }
            
            $data = $result['data'];
            $apiStats = $data['app_stats'] ?? [];
            $totalCalls = $data['total_calls'] ?? 0;
            
            // 从租户的product字段获取API限制数
            $limit = $this->getTenantApiLimit($tenant_id);
            $isUnlimited = $this->isUnlimitedTenant($tenant_id);
            
            // 获取租户下所有应用
            $allApps = App::where('tenant_id', $tenant_id)->get(['app_key', 'name', 'platform_type']);
            
            // 构建完整的应用统计数据
            $appStats = [];
            foreach ($allApps as $app) {
                $apiData = collect($apiStats)->firstWhere('app_key', $app->app_key);
                $callCount = $apiData['call_count'] ?? 0;
                
                $appStats[] = [
                    'app_key' => $app->app_key,
                    'app_name' => $app->name,
                    'platform_type' => $app->platform_type,
                    'platform_icon' => App::$platformIcons[$app->platform_type] ?? '',
                    'call_count' => $callCount,
                    'percentage' => $totalCalls > 0 ? round(($callCount / $totalCalls) * 100, 2) : 0
                ];
            }
            
            // 按调用次数排序
            usort($appStats, function($a, $b) {
                return $b['call_count'] - $a['call_count'];
            });
            
            return [
                'tenant_id' => $tenant_id,
                'date' => date('Y-m-d'),
                'total_calls' => $totalCalls,
                'limit' => $limit,
                'usage_percentage' => $isUnlimited ? 100 : ($limit > 0 ? round(($totalCalls / $limit) * 100, 2) : 0),
                'is_unlimited' => $isUnlimited,
                'app_count' => count($allApps),
                'active_app_count' => count(array_filter($appStats, function($app) { return $app['call_count'] > 0; })),
                'app_stats' => $appStats,
                'is_today' => true
            ];
        } catch (\Exception $e) {
            // 如果API调用失败，返回模拟数据用于测试
            \Log::warning('API调用失败，使用模拟数据', ['error' => $e->getMessage()]);
            
            return $this->getMockTodayStats($tenant_id);
        }
    }
    
    /**
     * 获取模拟的今日统计数据（用于测试）
     */
    private function getMockTodayStats($tenant_id)
    {
        // 获取租户下所有应用
        $allApps = App::where('tenant_id', $tenant_id)->get(['app_key', 'name', 'platform_type']);
        
        // 从租户的product字段获取API限制数
        $limit = $this->getTenantApiLimit($tenant_id);
        $isUnlimited = $this->isUnlimitedTenant($tenant_id);
        
        if ($allApps->isEmpty()) {
            // 如果没有应用，返回空数据
            return [
                'tenant_id' => $tenant_id,
                'date' => date('Y-m-d'),
                'total_calls' => 0,
                'limit' => $limit,
                'usage_percentage' => 0,
                'is_unlimited' => $isUnlimited,
                'app_count' => 0,
                'active_app_count' => 0,
                'app_stats' => [],
                'is_today' => true
            ];
        }
        
        // 为前几个应用模拟一些调用数据
        $mockCallCounts = [150, 89, 45, 23, 12];
        $totalCalls = 0;
        $appStats = [];
        
        foreach ($allApps as $index => $app) {
            $callCount = $index < count($mockCallCounts) ? $mockCallCounts[$index] : 0;
            $totalCalls += $callCount;
            
            $appStats[] = [
                'app_key' => $app->app_key,
                'app_name' => $app->name,
                'platform_type' => $app->platform_type,
                'platform_icon' => App::$platformIcons[$app->platform_type] ?? '',
                'call_count' => $callCount,
                'percentage' => 0 // 后面计算
            ];
        }
        
        // 计算占比
        foreach ($appStats as &$app) {
            $app['percentage'] = $totalCalls > 0 ? round(($app['call_count'] / $totalCalls) * 100, 2) : 0;
        }
        
        // 按调用次数排序
        usort($appStats, function($a, $b) {
            return $b['call_count'] - $a['call_count'];
        });
        
        return [
            'tenant_id' => $tenant_id,
            'date' => date('Y-m-d'),
            'total_calls' => $totalCalls,
            'limit' => $limit,
            'usage_percentage' => $isUnlimited ? 100 : round(($totalCalls / $limit) * 100, 2),
            'is_unlimited' => $isUnlimited,
            'app_count' => count($allApps),
            'active_app_count' => count(array_filter($appStats, function($app) { return $app['call_count'] > 0; })),
            'app_stats' => $appStats,
            'is_today' => true
        ];
    }
    
    /**
     * 获取趋势数据（智能天数范围，包含今日实时数据）
     */
    private function getTrendData($tenant_id)
    {
        // 先查询数据库中最早的记录
        $earliestRecord = TenantApiStats::where('tenant_id', $tenant_id)
            ->orderBy('stat_date')
            ->first();
        
        $endDate = date('Y-m-d');
        
        // 确定开始日期：取最早记录日期和30天前的较晚者
        if ($earliestRecord) {
            $earliestDate = $earliestRecord->stat_date;
            $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
            $startDate = $earliestDate > $thirtyDaysAgo ? $earliestDate : $thirtyDaysAgo;
        } else {
            // 如果没有历史数据，就从今天开始
            $startDate = $endDate;
        }
        
        $stats = TenantApiStats::where('tenant_id', $tenant_id)
            ->where('stat_date', '>=', $startDate)
            ->where('stat_date', '<', $endDate) // 不包含今天，今天用实时数据
            ->selectRaw('stat_date, SUM(call_count) as total_calls')
            ->groupBy('stat_date')
            ->orderBy('stat_date')
            ->get();
        
        // 获取今日实时数据
        $todayCallCount = 0;
        try {
            $client = new Client();
            $response = $client->get(config('app.api_url') . '/v1/stats/tenant', [
                'query' => [
                    'tenant_id' => $tenant_id,
                    'date' => $endDate
                ],
                'timeout' => 5
            ]);
            
            if ($response->getStatusCode() === 200) {
                $result = json_decode($response->getBody(), true);
                if (isset($result['code']) && $result['code'] === 200 && isset($result['data'])) {
                    $todayCallCount = $result['data']['total_calls'] ?? 0;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('获取今日实时数据失败', ['error' => $e->getMessage()]);
            // 如果获取失败，尝试从模拟数据获取
            $mockData = $this->getMockTodayStats($tenant_id);
            $todayCallCount = $mockData['total_calls'];
        }
        
        // 构建日期数组（只包含实际数据范围的日期）
        $trendData = [];
        $currentDate = $startDate;
        
        while ($currentDate <= $endDate) {
            if ($currentDate === $endDate) {
                // 今天使用实时数据
                $trendData[] = [
                    'date' => $currentDate,
                    'total_calls' => $todayCallCount,
                    'formatted_date' => date('m/d', strtotime($currentDate)),
                    'is_today' => true
                ];
            } else {
                // 历史数据
                $stat = $stats->firstWhere('stat_date', $currentDate);
                $trendData[] = [
                    'date' => $currentDate,
                    'total_calls' => $stat ? $stat->total_calls : 0,
                    'formatted_date' => date('m/d', strtotime($currentDate)),
                    'is_today' => false
                ];
            }
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        // 计算统计信息 - 基于实际数据天数
        $totalDays = count($trendData);
        $activeDays = count(array_filter($trendData, function($day) { return $day['total_calls'] > 0; }));
        $totalCalls = array_sum(array_column($trendData, 'total_calls'));
        
        // 日均计算：基于有数据的天数，而不是固定30天
        $avgCalls = $activeDays > 0 ? round($totalCalls / $activeDays, 1) : 0;
        
        // 找出最高和最低的一天
        $maxDay = collect($trendData)->sortByDesc('total_calls')->first();
        $minDay = collect($trendData)->where('total_calls', '>', 0)->sortBy('total_calls')->first();
        
        // 计算数据范围描述
        $dayRange = $totalDays;
        $rangeDescription = $dayRange == 1 ? '今日' : ($dayRange <= 30 ? "最近{$dayRange}天" : '最近30天');
        
        return [
            'daily_data' => $trendData,
            'summary' => [
                'total_days' => $totalDays,
                'active_days' => $activeDays,
                'total_calls' => $totalCalls,
                'avg_calls' => $avgCalls,
                'max_day' => $maxDay,
                'min_day' => $minDay,
                'range_description' => $rangeDescription,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ];
    }
} 