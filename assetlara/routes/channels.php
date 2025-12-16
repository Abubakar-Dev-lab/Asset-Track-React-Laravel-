<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
// 🟢 OUR CUSTOM ADMIN DASHBOARD CHANNEL
// Only users who are logged in AND have the 'admin' role can listen here.
Broadcast::channel('dashboard', function ($user) {
    // CRITICAL: We enforce the role check at the WebSocket level
    return $user && $user->role === 'admin';
});
