<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Debt;

class DebtAction
{
    private $debt;
    private $action;

    use InteractsWithSockets, SerializesModels;

    public function getDebt()
    {
        return $this->debt;
    }
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Create a new event instance.
     * DebtAction constructor.
     * @param Debt $debt
     * @param $action
     */
    public function __construct(Debt $debt, $action)
    {
        $this->debt = $debt;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
