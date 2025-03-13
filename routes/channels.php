<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// User-specific notification channel
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user-notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// User-specific chat channel
Broadcast::channel('chat.user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Admin-specific chat channel
Broadcast::channel('chat.admin.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->isAdmin();
});

// General admin channel for chat notifications
Broadcast::channel('chat.admin', function ($user) {
    return $user->isAdmin();
});