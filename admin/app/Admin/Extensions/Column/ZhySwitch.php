<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Grid\Displayers\AbstractDisplayer;
use Illuminate\Support\Arr;

class ZhySwitch extends AbstractDisplayer
{

    protected $states = [
        'on'  => ['value' => 1, 'text' => '开启', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '关闭', 'color' => 'primary'],
    ];

    protected function overrideStates($states)
    {
        if (empty($states)) {
            return;
        }

        foreach (Arr::dot($states) as $key => $state) {
            Arr::set($this->states, $key, $state);
        }
    }

    public function display($states = [], $resource = null)
    {
        $this->overrideStates($states);

        $name = $this->column->getName();
        $key = $this->row->getKey();
        $resource = $resource ?: admin_url(request()->path());

        $class = 'grid-switch-' . str_replace('.', '-', $name);
        $checked = $this->states['on']['value'] == $this->value ? 'checked' : '';

        $script = <<<JS
        $('.{$class}').bootstrapSwitch({
            size:'mini',
            onText: '{$this->states['on']['text']}',
            offText: '{$this->states['off']['text']}',
            onColor: '{$this->states['on']['color']}',
            offColor: '{$this->states['off']['color']}',
            onSwitchChange: function(event, state){
                $(this).val(state ? {$this->states['on']['value']} : {$this->states['off']['value']});
                var key = $(this).data('key');
                var value = $(this).val();
                $.ajax({
                    url: "{$resource}/" + key + '?_edit_inline=1',
                    type: "POST",
                    data: {
                        _token: LA.token,
                        _method: 'PUT',
                        {$name}: value,
                    },
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data.message);
                            if (data.then && data.then.action == 'refresh') {
                                $.pjax.reload('#pjax-container');
                            }
                        } else {
                            toastr.warning(data.message);
                        }
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            }
        });
        JS;

        \Encore\Admin\Admin::script($script);
        
        return "<input type='checkbox' class='{$class}' {$checked} data-key='{$key}' />";
    }
}