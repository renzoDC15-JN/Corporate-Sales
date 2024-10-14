<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProspectRegistered extends Notification
{
    use Queueable;
    protected $prospect;
    /**
     * Create a new notification instance.
     */
    public function __construct($prospect)
    {
        $this->prospect = $prospect;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Registration Successful')
            ->greeting('Mabuhay! ' . $this->prospect->first_name . '!')
            ->line('Natanggap na namin ang iyong impormasyon.')
            ->line('Name: ' . $this->prospect->first_name . ' ' . $this->prospect->last_name)
            ->line('Company: ' . $this->prospect->company)
            ->line('Position: ' . $this->prospect->position_title)
            ->line('Maraming salamat sa iyong pagtugon.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'prospect_id' => $this->prospect->prospect_id,
        ];
    }
}
