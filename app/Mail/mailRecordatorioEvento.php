<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class mailRecordatorioEvento extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * Create a new message instance.
     *
     * @param  string  $namePDF
     * @return void
     */
    public function __construct(private $namePDF)
    {
        $this->namePDF = $namePDF;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $entrades = env('url') . "/entrades/" . $this->namePDF;
        return $this->subject('Recordatorio Evento')
                    ->view('emails.mailRecordatorioEvento')
                    ->with(['entrades' => $entrades]);

    }
}
