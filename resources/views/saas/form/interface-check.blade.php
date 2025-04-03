<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        <a href="javascript:void(0);" 
           class="btn btn-warning interface-check-btn" 
           data-test-url="{{$testUrl}}"
           data-dependent-fields='{{$dependentFields}}'
           data-field="{{$name}}">
            <i class="fa fa-wrench"></i> {{$buttonText}}
        </a>
        <input type="hidden" name="{{$name}}" value="{{$value}}">
        <span class="interface-check-loading">
            <i class="fa fa-spinner fa-spin"></i> 测试中...
        </span>
        
        <span class="interface-check-result" 
              data-success-text="{{$successText}}"
              data-fail-text="{{$failText}}">
        </span>
        
        @include('admin::form.error')
        @include('admin::form.help-block')
    </div>
</div>