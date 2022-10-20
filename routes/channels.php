<?php

Broadcast::channel('alert.notification.{receiver}', function ($user, $receiver) {
    return (int)$user->id === (int)$receiver;
});
