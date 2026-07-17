<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'slug',
        'image',
        'summary',
        'content',
        'status',
    ];
}