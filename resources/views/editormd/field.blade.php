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
        @include('admin::form.help-block')
    </div>
</div>
<script>
    // 定义全局变量，开放用户可以操作的权限
    window.editorMd{{ $column }};
    $(function(){
        @if($dynamicMode)
        // 动态的开关
        $("#editormd-create-btn-{{ $column }}").click(function(){
            $(this).hide();
            @endif
            // 实例化代码的主体
            let config = {!! $config !!};
            editorMd{{ $column }} = editormd('{{ $column }}', config);
            // Fix editormd V1.5.0 bug (Previewing close button default set to show when loaded).
            $("#{{ $column }}").find(".editormd-preview-close-btn").hide();
            // Set the content value type.
            $(".editormd-markdown-textarea").attr("name", '{{ $column }}');
            if( config['saveHTMLToTextarea'] ) {
                $(".editormd-html-textarea").attr("name", '{{ $column }}_html');   
            }
            @if($dynamicMode)
        });
        @endif
    });
</script>
