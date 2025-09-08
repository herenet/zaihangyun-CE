<?php

namespace App\Admin\Extensions\Editormd;

use Encore\Admin\Form\Field;

class MyEditorField extends Field
{
    protected $view = 'editormd.field';

    protected static $css = [
        'vendor/hxsen/editormd/editor.md/css/editormd.min.css'
    ];

    protected static $js = [
        'vendor/hxsen/editormd/editor.md/editormd.min.js'
    ];

    public function render()
    {
        $this->variables = [
            'dynamicMode' => config('admin.extensions.editormd.dynamicMode', false),
            'wideMode' => config('admin.extensions.editormd.wideMode', false),
            'config' => $this->getConfigText(),
        ];

        return parent::render();
    }
    // 获取配置并追加配置
    private function getConfigText(){
        $config = (array) config('admin.extensions.editormd.config');
        $config['readOnly'] = false;

        return json_encode($config);

    }
}
