<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // Khai báo bảng nếu tên bảng khác với mặc định (không bắt buộc nếu bảng là 'comments')
    protected $table = 'comments';

    // Các trường được phép thêm dữ liệu
    protected $fillable = [
        'user_id', 
        'product_id', 
        'content', 
        'is_buyer'
    ];

    // Mối quan hệ: 1 Bình luận thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Mối quan hệ: 1 Bình luận thuộc về 1 Sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}