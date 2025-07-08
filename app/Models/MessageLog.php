<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\WhatsappHelper;

class MessageLog extends Model
{
    protected $fillable = [
        'user_id',
        'client_name',
        'session_name',
        'phone',
        'message',
        'status',
        'response',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public $timestamps = false;
}
