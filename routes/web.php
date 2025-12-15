<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/', function () {
    return view('welcome');
});
Route::get('/ops/tickets', [TicketController::class, 'index'])->name('ops.tickets.index');
Route::get('/ops/tickets/{ticket}', [TicketController::class, 'show'])->name('ops.tickets.show');
Route::post('/ops/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('ops.tickets.status');
Route::post('/ops/tickets/{ticket}/reply', [TicketController::class, 'storeReply'])->name('ops.tickets.reply');
