<?php

namespace App\Listeners;

use App\Events\DebtAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\DebtActionNotification;

class DebtActionNotify
{
    /**
     * Action the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DebtAction  $event
     * @return void
     */
    public function handle(DebtAction $event)
    {
        $debt = $event->getDebt();
        $action = $event->getAction();
        $debt->user->notify(new DebtActionNotification(
            $debt,
            $action
        ));
    }
}
