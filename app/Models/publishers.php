<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}