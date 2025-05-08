<?php

namespace App\SaaSAdmin\Extensions\Form;

use Illuminate\Support\Arr;
use Encore\Admin\Form;
use Encore\Admin\Form\Field\KeyValue as BaseKeyValue;

class ZhyKeyValue extends BaseKeyValue
{
    protected $keyRules = [];

    public function getView(): string
    {
        if (!empty($this->view)) {
            return $this->view;
        }

        $class = explode('\\', static::class);

        return 'admin::form.keyvalue';
    }
    
    public function keyRules($rules)
    {
        $this->keyRules = $rules;
        return $this;
    }

    public function getValidator(array $input)
    {
        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        if (!is_string($this->column)) {
            return false;
        }

        $rules = $keyRules = $attributes = [];

        if (!$fieldRules = $this->getRules()) {
            return false;
        }

        if (!$keyRules = $this->getKeyRules()) {
            return false;
        }

        if (!Arr::has($input, $this->column)) {
            return false;
        }

        $rules["{$this->column}.keys.*"] = $keyRules;
        $rules["{$this->column}.values.*"] = $fieldRules;
        $attributes["{$this->column}.keys.*"] = __('Key');
        $attributes["{$this->column}.values.*"] = __('Value');

        return validator($input, $rules, $this->getValidationMessages(), $attributes);
    }

    protected function getKeyRules()
    {

        $rules = $this->keyRules;

        if ($rules instanceof \Closure) {
            $rules = $rules->call($this, $this->form);
        }

        if (is_string($rules)) {
            $rules = array_filter(explode('|', $rules));
        }

        if (!$this->form || !$this->form instanceof Form) {
            return $rules;
        }

        if (!$id = $this->form->model()->getKey()) {
            return $rules;
        }

        if (is_array($rules)) {
            foreach ($rules as &$rule) {
                if (is_string($rule)) {
                    $rule = str_replace('{{id}}', $id, $rule);
                }
            }
        }

        return $rules;
    }
}