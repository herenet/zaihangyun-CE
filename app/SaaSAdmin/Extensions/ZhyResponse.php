<?php

namespace App\SaaSAdmin\Extensions;

use Encore\Admin\Actions\Response;
use App\SaaSAdmin\Extensions\ZhySweatAlert2;

class ZhyResponse extends Response
{
    public function swal()
    {
        if (!$this->plugin instanceof ZhySweatAlert2) {
            $this->plugin = new ZhySweatAlert2();
        }

        return $this;
    }
}