<?php

namespace App\Observers;

use App\Jobs\SendMessengerNotificationJob;
use App\Models\User;

class UserObserver
{
    public function updated(User $user): void
    {
        $changes = $user->getChanges();
        $message = null;

        if (array_key_exists('email', $changes)) {
            $message = "Ваш email был изменен на {$user->email}";
        } elseif (array_key_exists('password', $changes)) {
            $message = "Ваш пароль был изменен";
        }

        if ($message) {
            $this->sendNotifications($user, $message, 'user_updated');
        }
    }

    protected function sendNotifications(User $user, string $message, string $eventType): void
    {
        foreach ($user->userMessengers()->where('is_confirmed', true)->where('allow_notifications', true)->get() as $userMessenger) {
            $userMessenger->load('messenger');
            SendMessengerNotificationJob::dispatch($userMessenger, $message, $eventType);
        }
    }
}
