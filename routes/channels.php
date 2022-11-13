<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    Log::log("LOGGGGGGGGGGGGGGGGGGGGGGGGG:::::::::::::1");
    return (int) $user->id === (int) $id;
});

Broadcast::channel('notification.{userId}', function ($user, $userId) {
    Log::log("LOGGGGGGGGGGGGGGGGGGGGGGGGG:::::::::::::2");
    return (int) $user->id === (int) $userId;
});
