<?php
namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        return $this
            ->subject('You have been invited!')
            ->view('emails.invitation') // Create a view for the email
            ->with([
                'email' => $this->invitation->email,
                'invited_by' => $this->invitation->invited_by,
            ]);
    }
}
