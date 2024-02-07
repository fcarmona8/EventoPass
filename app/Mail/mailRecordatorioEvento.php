<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class mailRecordatorioEvento extends Mailable
{
    use Queueable, SerializesModels;

    public $fecha;

    /**
     * Create a new message instance.
     *
     * @param  string  $fecha
     * @return void
     */
    public function __construct($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail Recordatorio Evento')
                    ->view('view.name')
                    ->with(['fecha' => $this->fecha]);
    }
}
