<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'requester_email',
        'subject',
        'description',
        'category',
        'priority',
        'status',
    ];

    public function replies()
    {
        return $this->hasMany(\App\Models\TicketReply::class)
            ->orderBy('created_at');
    }
}
