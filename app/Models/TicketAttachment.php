<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_id',
        'original_name',
        'mime_type',
        'size',
        'storage_disk',
        'storage_path',
        'source',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
