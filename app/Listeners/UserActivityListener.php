<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class UserActivityListener
{
    /**
     * Handle user login events.
     */
    public function handleLogin(Login $event): void
    {
        $user = $event->user;
        $user->updateLastLogin(
            request()->ip(),
            request()->userAgent()
        );
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event): void
    {
        $user = $event->user;
        if ($user) {
            $user->markAsOffline();
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
        ];
    }
}
