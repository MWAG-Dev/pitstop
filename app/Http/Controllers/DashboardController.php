<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Guest view: no counts
        if (!$user) {
            return view('welcome', [
                'user' => null,
                'myTicketsCount' => null,
                'myOpenCount' => null,
                'opsSummary' => null,
            ]);
        }

        // Fetch recent tickets using requester_email (consistent with counts)
        $myTickets = Ticket::where('requester_email', $user->email)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

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
            'myOpenCount'    => $myOpenCount,
            'opsSummary'     => $opsSummary,
            'myTickets'      => $myTickets,
        ]);
    }
}