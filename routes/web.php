<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;

Route::get('/accept-invite', function() {
    return view('invitations.accept');
})->name('invites.accept.form');
