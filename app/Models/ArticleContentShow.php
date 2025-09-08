<?php

namespace App\Models;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;

class ArticleContentShow extends Model
{
    protected $table = 'article_content_show';
    protected $primaryKey = 'article_id';
    public $incrementing = false;

    // 添加 fillable 属性
    protected $fillable = [
        'article_id',
        'app_key',
        'content'
    ];
}