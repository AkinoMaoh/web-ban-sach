<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\products;

class publishers extends Model
{
    protected $table = 'publishers';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
        'website'
    ];

    public function products()
    {
        return $this->hasMany(products::class, 'publisher_id');
    }
}
