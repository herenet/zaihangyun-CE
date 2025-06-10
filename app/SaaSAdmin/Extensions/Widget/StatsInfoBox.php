<?php

namespace App\SaaSAdmin\Extensions\Widget;

use Encore\Admin\Widgets\Widget;

class StatsInfoBox extends Widget
{
    protected $view = 'saas.widgets.stats-info-box';

    protected $title;
    protected $icon;
    protected $theme;
    protected $gradient;
    protected $todayValue;
    protected $yesterdayValue;
    protected $secondaryLabel;
    protected $secondaryValue;
    protected $subtitle;

    public function __construct($title, $icon, $theme, $todayValue, $yesterdayValue, $secondaryLabel = null, $secondaryValue = null, $subtitle = null)
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->theme = $this->mapTheme($theme);
        $this->gradient = $this->getGradient($theme);
        $this->todayValue = $todayValue;
        $this->yesterdayValue = $yesterdayValue;
        $this->secondaryLabel = $secondaryLabel;
        $this->secondaryValue = $secondaryValue;
        $this->subtitle = $subtitle;
    }

    /**
     * 映射旧的主题色到新的主题类
     */
    protected function mapTheme($oldTheme)
    {
        $themeMap = [
            'aqua' => 'dual-stats-card-blue',
            'green' => 'dual-stats-card-green',
            'yellow' => 'dual-stats-card-orange',
            'red' => 'dual-stats-card-purple',
            'cyan' => 'dual-stats-card-blue',
        ];

        return $themeMap[$oldTheme] ?? 'dual-stats-card-blue';
    }

    /**
     * 根据主题获取渐变色
     */
    protected function getGradient($theme)
    {
        $gradients = [
            'aqua' => 'linear-gradient(135deg, #4086F5 0%, #6B9BF7 100%)',
            'green' => 'linear-gradient(135deg, #10B981 0%, #1AE2D6 100%)',
            'yellow' => 'linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%)',
            'red' => 'linear-gradient(135deg, #EF4444 0%, #F87171 100%)',
            'cyan' => 'linear-gradient(135deg, #06B6D4 0%, #1AE2D6 100%)',
        ];

        return $gradients[$theme] ?? 'linear-gradient(135deg, #4086F5 0%, #6B9BF7 100%)';
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
            'theme' => $this->theme,
            'gradient' => $this->gradient,
            'todayValue' => $this->formatValue($this->todayValue),
            'yesterdayValue' => $this->formatValue($this->yesterdayValue),
            'secondaryLabel' => $this->secondaryLabel,
            'secondaryValue' => $this->formatValue($this->secondaryValue ?? 0),
            'subtitle' => $this->subtitle,
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