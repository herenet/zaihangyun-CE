<?php

namespace App\SaaSAdmin\Extensions\Widget;

use Encore\Admin\Widgets\Widget;

class StatsInfoBox extends Widget
{
    protected $view = 'saas.widgets.stats-info-box';

    protected $title;
    protected $icon;
    protected $color;
    protected $todayValue;
    protected $yesterdayValue;

    public function __construct($title, $icon, $color, $todayValue, $yesterdayValue)
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->color = $color;
        $this->todayValue = $todayValue;
        $this->yesterdayValue = $yesterdayValue;
    }

    public function render()
    {
        // 计算增长率
        $growthRate = 0;
        $growthClass = 'growth-neutral';
        $growthIcon = '—';
        
        if ($this->yesterdayValue > 0) {
            $growthRate = (($this->todayValue - $this->yesterdayValue) / $this->yesterdayValue) * 100;
            
            if ($growthRate > 0) {
                $growthClass = 'growth-up';
                $growthIcon = '↗';
            } elseif ($growthRate < 0) {
                $growthClass = 'growth-down';
                $growthIcon = '↘';
            }
        } elseif ($this->todayValue > 0) {
            $growthRate = 100;
            $growthClass = 'growth-up';
            $growthIcon = '↗';
        }

        $variables = [
            'title' => $this->title,
            'icon' => $this->icon,
            'color' => $this->color,
            'todayValue' => $this->formatValue($this->todayValue),
            'yesterdayValue' => $this->formatValue($this->yesterdayValue),
            'growthRate' => abs(round($growthRate, 1)),
            'growthClass' => $growthClass,
            'growthIcon' => $growthIcon,
        ];

        return view($this->view, $variables)->render();
    }

    protected function formatValue($value)
    {
        // 如果是收入相关，格式化为货币
        if (strpos($this->title, '收入') !== false) {
            return '¥' . number_format($value / 100, 2);
        }
        
        // 普通数字
        return number_format($value);
    }
}