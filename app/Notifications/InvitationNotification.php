<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InvitationNotification extends Notification
{
    use Queueable;

    protected $invitation;

    public function __construct($invitation)
    {
        $this->invitation = $invitation;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Send via email and store in database
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You have been invited!')
            ->line('You have been invited to join our application.')
            ->action('Accept Invitation', url('/invite/' . $this->invitation->token))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'invitation_id' => $this->invitation->id,
            'email' => $this->invitation->email,
            'created_at' => now(),
        ];
    }
}
