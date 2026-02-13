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

    public function __construct(
        public Ticket $ticket,
        public TicketReply $reply,
        public string $recipientType = 'requester'
    ) {}

    public function build()
    {
        $subject = $this->recipientType === 'ops'
            ? "New reply on request #{$this->ticket->id}: {$this->ticket->subject}"
            : "Update on your request #{$this->ticket->id}: {$this->ticket->subject}";

        return $this->subject($subject)
            ->view('emails.ticket_replied');
    }
}