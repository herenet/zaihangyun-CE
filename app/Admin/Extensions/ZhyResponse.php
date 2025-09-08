<?php

namespace App\Admin\Extensions;

use Encore\Admin\Actions\Response;
use App\Admin\Extensions\ZhySweatAlert2;

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