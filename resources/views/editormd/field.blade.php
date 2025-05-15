<style>
    .editormd-create-btn {
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 4px;
        color: #666;
        cursor: pointer;
        text-align: center;
        width: 240px;
        margin: 0 auto;
        box-shadow: 0 0 6px rgba(177, 177, 177, .5) inset;
    }

    .editormd-fullscreen {
        z-index: 9999 !important;
    }

    .editormd-wide-mode-label {
        text-align: center;
        margin-bottom: 10px;
    }
    
    /* 字数统计样式 */
    .editor-word-count {
        position: absolute;
        right: 25px;
        bottom: 5px;
        font-size: 12px;
        color: #999;
        background: rgba(255, 255, 255, 0.8);
        padding: 2px 8px;
        border-radius: 3px;
        z-index: 10;
        box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
    }
    
    /* 超出字数限制时的样式 */
    .editor-word-count.exceeded {
        color: #ff4d4f;
        font-weight: bold;
    }
    
    /* 全屏模式下的字数统计位置调整 */
    .editormd-fullscreen .editor-word-count {
        bottom: 10px;
        right: 30px;
    }
</style>
<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}"
           class="{{ $wideMode ? 'col-sm-12'.' editormd-wide-mode-label' : $viewClass['label'].' control-label' }}">{{$label}}</label>
    <div class="{{ $wideMode ? 'col-sm-12' : $viewClass['field'] }}">
        @include('admin::form.error')
        @if( $dynamicMode)
            <div id="editormd-create-btn-{{$id}}" class="editormd-create-btn">
                点击展开 {{$name}} 编辑器
            </div>
        @endif
        <div id="{{$name}}">
            <textarea {!! $attributes !!} style="display:none;">{{ old($column, $value) }}</textarea>
        </div>
        <!-- 添加字数统计容器 -->
        <div id="word-count-{{$column}}" class="editor-word-count">字符数: 0</div>
        @include('admin::form.help-block')
    </div>
</div>
<script>
    // 定义全局变量，开放用户可以操作的权限
    window.editorMd{{ $column }};
    
    // 字符统计函数 - 与 Laravel 验证规则保持一致（按字符计数）
    function countChars(text) {
        if (!text) return 0;
        return text.length;
    }
    
    // 更新字数统计
    function updateWordCount(editor, countElement) {
        const text = editor.getValue();
        const charCount = countChars(text);
        const charLimit = 10000; // 字符数限制
        
        countElement.text(`字符数: ${charCount}`);
        
        // 超过限制时添加红色警示
        if (charCount > charLimit) {
            countElement.addClass('exceeded');
        } else {
            countElement.removeClass('exceeded');
        }
    }
    
    // 初始化编辑器的函数
    function initEditor{{ $column }}() {
        // 实例化代码的主体
        let config = {!! $config !!};
        
        // 确保 onchange 回调存在
        const originalOnchange = config.onchange;
        
        config.onchange = function() {
            // 调用原始的 onchange 回调（如果存在）
            if (typeof originalOnchange === 'function') {
                originalOnchange.apply(this, arguments);
            }
            
            // 更新字数统计
            updateWordCount(this, $("#word-count-{{$column}}"));
        };
        
        // 创建编辑器实例
        editorMd{{ $column }} = editormd('{{ $column }}', config);
        
        // Fix editormd V1.5.0 bug
        $("#{{ $column }}").find(".editormd-preview-close-btn").hide();
        
        // Set the content value type
        $(".editormd-markdown-textarea").attr("name", '{{ $column }}');
        if (config['saveHTMLToTextarea']) {
            $(".editormd-html-textarea").attr("name", '{{ $column }}_html');   
        }
        
        // 初始化时立即进行一次字数统计
        setTimeout(function() {
            updateWordCount(editorMd{{ $column }}, $("#word-count-{{$column}}"));
        }, 100);
    }
    
    $(function(){
        @if($dynamicMode)
        // 动态的开关
        $("#editormd-create-btn-{{ $column }}").click(function(){
            $(this).hide();
            @endif
            
            // 初始化编辑器
            initEditor{{ $column }}();
            
            @if($dynamicMode)
        });
        @endif
        
        // 监听 pjax 完成事件
        // $(document).on('pjax:complete', function() {
        //     if ($("#{{ $column }}").length > 0) {
        //         setTimeout(initEditor{{ $column }}, 200);
        //     }
        // });
        
        // 监听 ajax 完成事件
        // $(document).on('ajaxComplete', function() {
        //     if ($("#{{ $column }}").length > 0 && !window.editorMd{{ $column }}) {
        //         setTimeout(initEditor{{ $column }}, 200);
        //     }
        // });
    });
</script>
