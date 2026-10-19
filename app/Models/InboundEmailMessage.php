<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundEmailMessage extends Model
{
    protected $fillable = [
        'provider',
        'provider_message_id',
        'sender_email',
        'ticket_id',
        'user_id',
        'user_created',
        'status',
        'failure_reason',
        'processed_at',
    ];

    protected $casts = [
        'user_created' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
