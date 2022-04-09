<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    protected $primaryKey = 'id';
    protected $fillable  = ['name', 'appId', 'language', 'client_token', 'os'];
    protected $guarded  = ['id', 'uid'];
    protected $hidden = ['created_at', 'updated_at'];

    // Relations
    public function application()
    {
        return $this->belongsTo('App\Models\Application', 'appId', 'guid');
    }

    // Scopes
    public function scopeOfApplication($query, $appId)
    {
        return $query->where('appId', $appId);
    }
}
