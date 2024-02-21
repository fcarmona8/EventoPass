<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session as LaravelSession;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class TicketsPDFController extends Controller
{

    public function generatePdf(){

        $pdf = PDF::loadView('tickets.ticketsPDF.ticketsPDF');

        $session =  LaravelSession::get('datosCompra');

        $storagePath = 'public/pdfs/'.$session['namePDF'];

        Storage::put($storagePath, $pdf->output());
        
        return 'PDF guardado en la ruta: ' . $storagePath;
    }

    public function generatePdfNominal()
    {
        $pdf = PDF::loadView('tickets.ticketsPDF.ticketsPdfNominals');

        $session =  LaravelSession::get('datosCompra');
        $pdfName = $session['buyerDNI'] . $session['sessionId'];

        $storagePath = 'public/pdfs/'.$pdfName;

        $session['namePDF'] = $pdfName.'.pdf';

        LaravelSession::put('datosCompra', $session);

        Storage::put($storagePath, $pdf->output());

        return 'PDF guardado en la ruta: ' . $storagePath;
    }

    public function descargarPDF($nombrePdf){

        $filePath = public_path("storage/pdfs/{$nombrePdf}"); 

        return response()->download($filePath);

    }
    
}