<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Auth;
use Lang;
use App\Models\Debt;

class DebtActionNotification extends Notification
{
    use Queueable;

    private $debt;
    private $action;

    /**
     * Create a new notification instance.
     * DebtActionNotification constructor.
     * @param $debt
     * @param $action
     */
    public function __construct($debt, $action)
    {
        $this->debt = $debt;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        /*return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', 'https://laravel.com')
                    ->line('Thank you for using our application!'); */
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        switch ($this->action) {
            case 'created':
                $text = __(':title was created by :creator and assigned to you', [
                'title' => $this->debt->title,
                'creator' => $this->debt->creator->name
                ]);
                break;
            case 'updated_status':
                $text = __(':title was completed by :username', [
                'title' => $this->debt->title,
                'username' =>  Auth()->user()->name
                ]);
                break;
            case 'updated_deadline':
                $text = __(':username updated the deadline for this :title', [
                'title' => $this->debt->title,
                'username' =>  Auth()->user()->name
                ]);
                break;
            case 'updated_assign':
                $text = __(':username assigned a debt to you', [
                'username' =>  Auth()->user()->name
                ]);
                break;
            case 'updated_agreement':
                $text = __('Adobe Sign agreement was created for :title', [
                'title' =>  $this->debt->title
                ]);
                break;
            default:
                break;
        }
        return [
            'assigned_user' => $notifiable->id, //Assigned user ID
            'created_user' => $this->debt->creator->id,
            'message' => $text,
            'type' => Debt::class,
            'type_id' =>  $this->debt->id,
            'url' => url('debts/' . $this->debt->id),
            'action' => $this->action
        ];
    }
}
