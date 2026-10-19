<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InboundTicketReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public User $user,
        public ?string $temporaryPassword = null
    ) {}

    public function build()
    {
        return $this->subject("We received your request #{$this->ticket->id}")
            ->view('emails.inbound_ticket_received');
    }
}
