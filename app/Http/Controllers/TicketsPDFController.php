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
        $qrCodeImage = QrCode::size(300)->generate('https://copernic.cat/');
        $base64QrCode = base64_encode($qrCodeImage);

        $data = [
            'qrCode' => $base64QrCode
        ];

        $pdf = PDF::loadView('tickets.ticketsPDF.ticketsPdf', $data);

        $session =  LaravelSession::get('datosCompra');
        $pdfName = $session['buyerDNI'] . $session['sessionId'];

        $storagePath = 'public/pdfs/'.$pdfName .'.pdf';

        $session['namePDF'] = $pdfName.'.pdf';

        LaravelSession::put('datosCompra', $session);

        Storage::put($storagePath, $pdf->output());
        
        return 'PDF guardado en la ruta: ' . $storagePath;
    }

    public function generatePdfNominal()
    {
        // Generar el código QR y obtener la representación en base64
        $qrCodeImage = QrCode::size(300)->generate('https://copernic.cat/');
        $base64QrCode = base64_encode($qrCodeImage);

        $data = [
            'qrCode' => $base64QrCode
        ];

        

        $pdf = PDF::loadView('tickets.ticketsPDF.ticketsPdfNominals', $data);

        $storagePath = 'public/pdfs/z.pdf';

        Storage::put($storagePath, $pdf->output());

        return 'PDF guardado en la ruta: ' . $storagePath;
    }

    public function descargarPDF($nombrePdf){

        $filePath = public_path("storage/pdfs/{$nombrePdf}"); 

        return response()->download($filePath);

    }
    
}