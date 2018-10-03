<?php

namespace Piclou\Piclommerce\Http\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendContactEmail extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * @var array
     */
    private $contact;

    /**
     * SendContactEmail constructor.
     * @param array $contact
     */
    public function __construct(array $contact)
    {
        //
        $this->contact = $contact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $contact = $this->contact;
        return $this->from(setting('generals.email'), setting('generals.websiteName'))
            ->replyTo($contact['email'], $contact['firstname']. " ". $contact['lastname'])
            ->subject(__("piclommerce::web.contact_object"))
            ->view('piclommerce::mail.contact', compact('contact'));
    }
}
