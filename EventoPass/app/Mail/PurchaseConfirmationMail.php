<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $responseBody;

    public function __construct($responseBody)
    {
        $this->responseBody = $responseBody;
    }

    public function build()
    {
        return $this->view('emails.purchase_confirmation')
                    ->with([
                        'response' => $this->responseBody,
                    ]);
    }
}
