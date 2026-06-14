<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class publishers extends Model
{
    protected $table = 'publishers';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'address', 'website', 'status'];
}
