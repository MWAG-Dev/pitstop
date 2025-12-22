<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class MyTicketsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_if(!$user, 403);

        $email = $user->email;

        $tickets = Ticket::where('requester_email', $email)
            ->with('replies')
            ->orderByDesc('id')
            ->paginate(10);

        return view('my_tickets.index', compact('tickets', 'email'));
    }

    public function show(Ticket $ticket)
    {
        $user = auth()->user();
        abort_if(!$user, 403);

        // Ops can view any ticket
        if (method_exists($user, 'isOps') && $user->isOps()) {
            $ticket->load('replies');
            return view('ops.tickets.show', compact('ticket'));
        }

        // Non-ops can only view their own tickets
        abort_if($ticket->requester_email !== $user->email, 403);

        $ticket->load('replies');
        return view('my_tickets.show', compact('ticket'));
    }

    public function storeReply(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        abort_if(!$user, 403);

        // Non-ops can only reply to their own tickets
        if (!(method_exists($user, 'isOps') && $user->isOps())) {
            abort_if($ticket->requester_email !== $user->email, 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Create a reply from the requester/user
        $ticket->replies()->create([
            'author_role'  => 'requester',
            'author_email' => $user->email,
            'message'      => $validated['message'],
        ]);

        return back()->with('success', 'Reply sent.');
    }
}