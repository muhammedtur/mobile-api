<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscription';
    protected $primaryKey = 'id';
    protected $fillable  = ['client_token', 'status', 'expire_date'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at', 'expire_date'];

    // Relations
    public function device()
    {
        return $this->belongsTo('App\Models\Device', 'client_token', 'client_token');
    }
}
