<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketReplied extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket, public TicketReply $reply) {}

    public function build()
    {
        return $this->subject("Update on your ticket #{$this->ticket->id}: {$this->ticket->subject}")
            ->view('emails.ticket_replied');
    }
}