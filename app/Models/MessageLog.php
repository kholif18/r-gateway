<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\WhatsappHelper;

class MessageLog extends Model
{
    protected $fillable = [
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

    public $timestamps = false;

    /**
     * 🔁 Mutator untuk otomatis normalisasi nomor telepon
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = WhatsappHelper::normalizePhoneNumber($value);
    }
}
