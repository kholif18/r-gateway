<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    protected $fillable = [
        'client_name', 'session_name', 'api_token', 'created_by', 'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
