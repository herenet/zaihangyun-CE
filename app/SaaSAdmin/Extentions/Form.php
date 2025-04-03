<?php

namespace App\SaaSAdmin\Extentions\Form;

use Encore\Admin\Form as BaseForm;

class MyForm extends BaseForm
{
    public function edit($id) : self
    {
        // $this->builder()->setResourceId($id);
        return parent::edit($id);
    }
}