<?php

namespace App\Mail;

use App\Models\Waitlist;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WaitlistNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $waitlist;

    public function __construct(Waitlist $waitlist)
    {
        $this->waitlist = $waitlist;
    }

    public function build()
    {
        return $this->markdown('emails.waitlist-notification')
                    ->subject('Time Slot Available - Aruma Beauty');
    }
}
