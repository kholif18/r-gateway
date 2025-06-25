<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaOtp extends Model
{
    protected $fillable = ['phone', 'otp', 'expires_at'];
    protected $dates = ['expires_at'];
}
