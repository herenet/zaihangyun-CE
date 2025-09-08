<?php

namespace App\Admin\Extensions;

use Encore\Admin\Actions\SweatAlert2;

class ZhySweatAlert2 extends SweatAlert2
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $text;

    /**
     * @param string $type
     * @param string $title
     * @param string $text
     * @return $this
     */
    public function show($type, $title = '', $text = '')
    {
        $this->type = $type;
        $this->title = $title;
        $this->text = $text;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'swal' => [
                'type'  => $this->type,
                'title' => $this->title,
                'text' => $this->text,
                'width' => '600px',
                'fontSize' => '16px',
            ],
        ];
    }
}
