<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class TicketsPDFController extends Controller
{
    public function generatePdf()
    {
         $data = [
            'title' => 'a',
            'content' => 'a'
        ];


        $pdf = PDF::loadView('tickets.ticketsPDF.ticketsPDF', $data);

        return $pdf->download('a.pdf');

        /*$title='aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $content = 'ªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªª';

       return view('tickets.ticketsPDF.ticketsPDF', compact('title', 'content')); */
    }
}