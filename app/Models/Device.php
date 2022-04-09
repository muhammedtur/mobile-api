<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    protected $primaryKey = 'id';
    protected $fillable  = ['uid', 'name', 'appId', 'language', 'client_token', 'os'];
    protected $hidden = ['created_at', 'updated_at'];
}
