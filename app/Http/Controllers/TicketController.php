<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewTicketSubmitted;
use App\Mail\TicketReplied;

class TicketController extends Controller
{
    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requester_email' => ['required', 'email', 'max:255'],
            'subject'         => ['required', 'string', 'max:150'],
            'description'     => ['required', 'string', 'max:5000'],
            'category'        => ['required', 'string', 'max:50'],
            'priority'        => ['required', 'string', 'max:20'],
        ]);

        $ticket = Ticket::create([
            ...$validated,
            'status' => 'Open',
        ]);

        $opsEmail = env('OPS_NOTIFY_EMAIL');
        if ($opsEmail) {
            Mail::to($opsEmail)->send(new NewTicketSubmitted($ticket));
        }

        return back()->with('success', "Ticket #{$ticket->id} submitted successfully.");
    }

    public function index()
    {
       $tickets = \App\Models\Ticket::orderByDesc('created_at')->get();
       return view('ops.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
       $ticket->load('replies');
       return view('ops.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
       $validated = $request->validate([
           'status' => ['required', 'in:Open,In Progress,Waiting,Closed'],
       ]);

       $ticket->status = $validated['status'];
       $ticket->save();

       return back()->with('success', 'Status updated.');
    }

    public function storeReply(Request $request, Ticket $ticket)
    {
       $validated = $request->validate([
           'message' => ['required', 'string', 'max:5000'],
           'author_email' => ['nullable', 'email', 'max:255'],
       ]);

       $reply = $ticket->replies()->create([
            'author_role'  => 'ops',
            'author_email' => $validated['author_email'] ?? null,
            'message'      => $validated['message'],
        ]);
        Mail::to($ticket->requester_email)->send(new TicketReplied($ticket, $reply));

       if ($ticket->status === 'Open') {
           $ticket->update(['status' => 'In Progress']);
       }

       return back()->with('success', 'Reply posted.');
    }

}
