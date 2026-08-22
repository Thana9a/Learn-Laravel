<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Register the private channels for broadcasting.
| The reports.{userId} channel is scoped per user for security.
|
*/

Broadcast::channel('reports.{userId}', function ($user, $userId) {
    // Only authorize if the authenticated user owns this channel
    return (int) $user->id === (int) $userId;
});
