<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscription';
    protected $primaryKey = 'id';
    protected $fillable  = ['device_uid', 'appId', 'os', 'subscription', 'subscription_expire_date'];
    protected $hidden = ['created_at', 'updated_at'];
}
