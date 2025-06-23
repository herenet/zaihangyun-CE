<li class="api-stats-nav" style="margin-right: 15px; display: flex; align-items: center; height: 60px;">
    <div id="api-stats-container" style="
        padding: 8px 14px; 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        background: #f8f9fa; 
        border-radius: 18px; 
        border: 1px solid #e9ecef;
        height: 34px;
        box-sizing: border-box;
    ">
        <div style="display: flex; align-items: center; gap: 6px;">
            <div style="
                width: 5px; 
                height: 5px; 
                background: #28a745; 
                border-radius: 50%;
            "></div>
            <span style="color: #495057; font-size: 13px; font-weight: 600; line-height: 1;">今日API</span>
        </div>
        
        <div id="api-stats-content" style="display: flex; align-items: center; gap: 8px;">
            <!-- 加载状态 -->
            <div id="api-stats-loading" style="display: block;">
                <span style="color: #6c757d; font-size: 12px; line-height: 1;">
                    <i class="fa fa-spinner fa-spin" style="margin-right: 3px;"></i>加载中...
                </span>
            </div>
            
            <!-- 数据显示 -->
            <div id="api-stats-data" style="display: none; align-items: center; gap: 8px;">
                <span id="api-stats-numbers" style="
                    color: #212529; 
                    font-weight: 700; 
                    font-size: 13px; 
                    background: rgba(40, 167, 69, 0.08);
                    padding: 2px 6px;
                    border-radius: 8px;
                    line-height: 1;
                ">0/10000</span>
                
                <div style="
                    width: 35px; 
                    height: 5px; 
                    background: #e9ecef; 
                    border-radius: 3px; 
                    overflow: hidden;
                ">
                    <div id="api-stats-progress" style="
                        width: 0%; 
                        height: 100%; 
                        background: #28a745; 
                        transition: width 0.3s ease;
                    "></div>
                </div>
                
                <span id="api-stats-status" style="
                    color: #28a745; 
                    font-size: 11px; 
                    font-weight: 600;
                    line-height: 1;
                ">
                    <i class="fa fa-check" style="margin-right: 2px;"></i>正常
                </span>
            </div>
            
            <!-- 错误状态 -->
            <div id="api-stats-error" style="display: none;">
                <span id="api-stats-error-text" style="
                    color: #dc3545; 
                    font-size: 12px;
                    line-height: 1;
                ">获取失败</span>
            </div>
        </div>
        
        <div style="display: flex; align-items: center; gap: 4px;">
            <!-- 升级套餐按钮 -->
            <a id="upgrade-package-btn" href="/pricing" target="_blank" style="
                background: linear-gradient(135deg, #ffc107, #ff8c00); 
                border: 1px solid #ffc107; 
                color: #fff; 
                cursor: pointer; 
                padding: 0; 
                border-radius: 4px; 
                text-decoration: none; 
                transition: all 0.2s ease;
                width: 20px;
                height: 20px;
                display: none;
                align-items: center;
                justify-content: center;
                box-shadow: 0 1px 3px rgba(255, 193, 7, 0.3);
            " title="升级套餐获得更多API调用额度"
            onmouseover="this.style.background='linear-gradient(135deg, #ff8c00, #ffc107)'; this.style.transform='scale(1.05)'" 
            onmouseout="this.style.background='linear-gradient(135deg, #ffc107, #ff8c00)'; this.style.transform='scale(1)'">
                <i class="fa fa-rocket" style="font-size: 11px;"></i>
            </a>
            
            <button id="refresh-api-stats" style="
                background: #fff; 
                border: 1px solid #dee2e6; 
                color: #6c757d; 
                cursor: pointer; 
                padding: 0; 
                border-radius: 4px; 
                transition: all 0.2s ease;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            " title="刷新统计数据" 
            onmouseover="this.style.background='#f8f9fa'; this.style.borderColor='#adb5bd'" 
            onmouseout="this.style.background='#fff'; this.style.borderColor='#dee2e6'">
                <i class="fa fa-refresh" style="font-size: 11px;"></i>
            </button>
            
            <a href="{{ admin_url('api-stats/details') }}" style="
                background: #fff; 
                border: 1px solid #dee2e6; 
                color: #6c757d; 
                cursor: pointer; 
                padding: 0; 
                border-radius: 4px; 
                text-decoration: none; 
                transition: all 0.2s ease;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            " title="查看详细统计"
            onmouseover="this.style.background='#f8f9fa'; this.style.borderColor='#adb5bd'" 
            onmouseout="this.style.background='#fff'; this.style.borderColor='#dee2e6'">
                <i class="fa fa-bar-chart" style="font-size: 11px;"></i>
            </a>
        </div>
    </div>
</li> 