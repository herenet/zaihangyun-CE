<?php

namespace App\Models;

use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use App\Models\ArticleContentShow;

class Article extends Model implements Sortable
{
    use DefaultDatetimeFormat, SortableTrait;
    protected $table = 'articles';

    protected $primaryKey = 'id';

    public $incrementing = false;
    
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
        // 'ignore_timestamps' => false,
    ];
}