<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
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
            'subject'     => ['required', 'string', 'max:150'],
            'description'     => ['required', 'string', 'max:5000'],
            'category'        => ['required', 'string', 'max:50'],
            'priority'        => ['required', 'string', 'max:20'],
            'attachments'   => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt'],
        ]);

        $ticketData = Arr::except($validated, ['attachments']);

        $ticket = Ticket::create([
            ...$ticketData,
            'requester_email' => auth()->user()->email,
            'status' => 'Open',
        ]);

        // Store optional attachments (screenshots, PDFs, etc.)
        // NOTE: This stores files on disk, but does not persist metadata in the DB yet.
        // If you want these viewable/downloadable on the ticket page, we can add a
        // `ticket_attachments` table + relationship next.
        $storedAttachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                // Store under: storage/app/public/tickets/{ticketId}/...
                $path = $file->store("tickets/{$ticket->id}", 'public');
                $storedAttachments[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
            }

            if (!empty($storedAttachments)) {
                Log::info('Ticket attachments stored', [
                    'ticket_id' => $ticket->id,
                    'attachments' => $storedAttachments,
                ]);
            }
        }

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
           'attachments'   => ['nullable', 'array', 'max:10'],
           'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt'],
       ]);

       $reply = $ticket->replies()->create([
            'author_role'  => 'ops',
            'author_email' => $validated['author_email'] ?? null,
            'message'      => $validated['message'],
        ]);

        // Store optional attachments for this reply
        $storedAttachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                // Store under: storage/app/public/tickets/{ticketId}/replies/{replyId}/...
                $path = $file->store("tickets/{$ticket->id}/replies/{$reply->id}", 'public');
                $storedAttachments[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
            }

            if (!empty($storedAttachments)) {
                Log::info('Ticket reply attachments stored', [
                    'ticket_id' => $ticket->id,
                    'reply_id' => $reply->id,
                    'attachments' => $storedAttachments,
                ]);
            }
        }

        Mail::to($ticket->requester_email)->send(new TicketReplied($ticket, $reply));

       if ($ticket->status === 'Open') {
           $ticket->update(['status' => 'In Progress']);
       }

       return back()->with('success', 'Reply posted.');
    }

    public function opsIndex()
    {
        $tickets = Ticket::orderByDesc('created_at')->paginate(20);
        return view('ops.tickets.index', compact('tickets'));
    }

    public function opsShow(Ticket $ticket)
    {
        $ticket->load('replies');
        return view('ops.tickets.show', compact('ticket'));
    }

    public function opsUpdateStatus(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'max:30'],
        ]);

        $ticket->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Status updated.');
    }

}
