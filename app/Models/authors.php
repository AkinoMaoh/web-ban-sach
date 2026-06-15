<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class authors extends Model
{
    public function products()
    {
        return $this->hasMany(products::class, 'author_id');
    }
    protected $table = 'authors';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'bio', 'avatar', 'status', 'created_at', 'updated_at'];
}
