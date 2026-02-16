<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    protected $fillable = [
        'ticket_id',
        'author_role',
        'author_email',
        'message',
    ];

    public function ticket()
    {
        return $this->belongsTo(\App\Models\Ticket::class);
    }
}
