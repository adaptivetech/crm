<?php

namespace App\Listeners;

use App\Events\DebtAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Activity;
use Lang;
use App\Models\Debt;

class DebtActionLog
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
        switch ($event->getAction()) {
            case 'created':
                $text = __(':title was created by :creator and assigned to :assignee', [
                    'title' => $event->getDebt()->title,
                    'creator' => $event->getDebt()->creator->name,
                    'assignee' => $event->getDebt()->user->name
                ]);
                break;
            case 'updated_status':
                $text = __('Debt was completed by :username', [
                    'username' => Auth()->user()->name,
                ]);
                break;
            case 'updated_deadline':
                $text = __(':username updated the deadline for this debt', [
                    'username' => Auth()->user()->name,
                ]);
                break;
            case 'updated_assign':
                $text = __(':username assigned debt to :assignee', [
                    'username' => Auth()->user()->name,
                    'assignee' => $event->getDebt()->user->name
                ]);
                break;
            case 'updated_agreement':
                $text = __(':username created an agreement for :title', [
                    'title' => $event->getDebt()->title,
                    'username' => $event->getDebt()->user->name
                ]);
                break;
            default:
                break;
        }

        $activityinput = array_merge(
            [
                'text' => $text,
                'user_id' => Auth()->id(),
                'source_type' => Debt::class,
                'source_id' =>  $event->getDebt()->id,
                'action' => $event->getAction()
            ]
        );
        
        Activity::create($activityinput);
    }
}
