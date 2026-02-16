<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketView;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Guest view: no counts
        if (! $user) {
            return view('welcome', [
                'user' => null,
                'myTicketsCount' => null,
                'myOpenCount' => null,
                'opsSummary' => null,
            ]);
        }

        // Fetch recent tickets using requester_email (consistent with counts)
        $myTickets = Ticket::where('requester_email', $user->email)
            ->whereNotIn('status', ['Closed', 'Resolved'])
            ->with(['replies' => fn ($query) => $query->latest('created_at')->limit(1)])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $unreadTicketIds = [];
        if ($myTickets->isNotEmpty()) {
            $views = TicketView::where('user_id', $user->id)
                ->whereIn('ticket_id', $myTickets->pluck('id'))
                ->get()
                ->keyBy('ticket_id');

            $unreadTicketIds = $myTickets->filter(function ($ticket) use ($views) {
                $latestReply = $ticket->replies->first();
                if (! $latestReply || $latestReply->author_role !== 'ops') {
                    return false;
                }

                $lastViewed = $views->get($ticket->id)?->last_viewed_at;

                return ! $lastViewed || $latestReply->created_at->gt($lastViewed);
            })->pluck('id')->all();
        }

        // Calculate counts
        $myTicketsCount = Ticket::where('requester_email', $user->email)->count();
        $myOpenCount = Ticket::where('requester_email', $user->email)
            ->where('status', 'Open')
            ->count();

        // Build ops summary for Ops users
        $opsSummary = null;
        if ($user->isOps()) {
            $opsSummary = [
                'total' => Ticket::count(),
                'open' => Ticket::where('status', 'Open')->count(),
                'in_progress' => Ticket::where('status', 'In Progress')->count(),
                'waiting' => Ticket::where('status', 'Waiting')->count(),
                'resolved' => Ticket::where('status', 'Resolved')->count(),
            ];
        }

        // Pass data to the dashboard view
        return view('dashboard', [
            'myTicketsCount' => $myTicketsCount,
            'myOpenCount' => $myOpenCount,
            'opsSummary' => $opsSummary,
            'myTickets' => $myTickets,
            'unreadTicketIds' => $unreadTicketIds,
        ]);
    }
}
