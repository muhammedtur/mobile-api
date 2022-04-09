<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'id';
    protected $guarded  = ['id', 'guid'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'credentials' => 'array'
    ];
}
