<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class authors extends Model
{
    protected $table = 'authors';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'bio', 'image', 'status'];
}
