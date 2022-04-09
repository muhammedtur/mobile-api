<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'id';
    protected $fillable  = ['guid', 'username', 'password', 'name'];
    protected $hidden = ['created_at', 'updated_at'];
}
