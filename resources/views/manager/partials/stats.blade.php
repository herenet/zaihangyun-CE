@php
$stats = [
    [
        'title' => '总用户数',
        'value' => $total_users ?? 0,
        'icon' => 'users',
        'color' => '#0073b7'
    ],
    [
        'title' => '新增用户',
        'value' => $user_increate ?? 0,
        'icon' => 'user-plus',
        'color' => '#00c0ef'
    ],
    [
        'title' => '总收入',
        'value' => '¥' . number_format($total_income ?? 0, 2),
        'icon' => 'rmb',
        'color' => '#dd4b39'
    ],
    [
        'title' => '新增订单',
        'value' => $order_increate ?? 0,
        'icon' => 'shopping-cart',
        'color' => '#00a65a'
    ],
    [
        'title' => '新增收入',
        'value' => '¥' . number_format($income_increate ?? 0, 2),
        'icon' => 'line-chart',
        'color' => '#f39c12'
    ]
];
@endphp

<div class="stats-container">
    <div class="stats-grid">
        @foreach($stats as $stat)
            <div class="stat-item">
                <span class="stat-icon" style="background-color: {{ $stat['color'] }}">
                    <i class="fa fa-{{ $stat['icon'] }}"></i>
                </span>
                <div class="stat-title">{{ $stat['title'] }}</div>
                <div class="stat-value">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>
</div>

<style>
.stats-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
}
.stat-item {
    text-align: center;
    padding: 15px;
    border-radius: 6px;
    transition: all 0.3s ease;
}
.stat-item:hover {
    transform: translateY(-2px);
}
.stat-icon {
    font-size: 24px;
    margin-bottom: 10px;
    color: #fff;
    width: 50px;
    height: 50px;
    line-height: 50px;
    border-radius: 25px;
    display: inline-block;
}
.stat-title {
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}
.stat-value {
    font-size: 20px;
    font-weight: bold;
    color: #333;
}
</style>