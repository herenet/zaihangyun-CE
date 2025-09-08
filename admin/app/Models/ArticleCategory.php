<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class ArticleCategory extends Model
{
    use DefaultDatetimeFormat;
    
    protected $table = 'article_category';

}

