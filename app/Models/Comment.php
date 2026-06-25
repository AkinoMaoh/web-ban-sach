<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';

    // Bảng comments của bạn không có cột updated_at
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'content',
        'created_at'
    ];
}