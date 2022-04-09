<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscription';
    protected $primaryKey = 'id';
    protected $fillable  = ['status', 'expire_date'];
    protected $hidden = ['created_at', 'updated_at'];

    // Relations
    public function device()
    {
        return $this->belongsTo('App\Models\Device', 'client_token');
    }
}
