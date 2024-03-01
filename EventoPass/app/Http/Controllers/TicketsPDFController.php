<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session as LaravelSession;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class TicketsPDFController extends Controller
{

    /**
     * Genera un archivo PDF de tickets estándar y lo almacena.
     * Utiliza una vista específica para el diseño de los tickets y los datos de la sesión actual.
     *
     * @return string
     */
    public function generatePdf(){

        $pdf = PDF::loadView('tickets.ticketsPDF.ticketsPDF');

        $session =  LaravelSession::get('datosCompra');

        $storagePath = 'public/pdfs/'.$session['namePDF'];

        Storage::put($storagePath, $pdf->output());
        
        return 'PDF guardado en la ruta: ' . $storagePath;
    }

    /**
     * Genera un archivo PDF de tickets nominales con nombres especificados para cada entrada,
     * y lo almacena en el sistema de archivos. Similar a `generatePdf` pero utiliza una vista distinta
     * para los tickets nominales.
     *
     * @return string
     */
    public function generatePdfNominal()
    {
        $pdf = PDF::loadView('tickets.ticketsPDF.ticketsPdfNominals');

        $session =  LaravelSession::get('datosCompra');
        $pdfName = $session['buyerDNI'] . $session['sessionId'];

        $storagePath = 'public/pdfs/'.$pdfName.'.pdf';

        $session['namePDF'] = $pdfName.'.pdf';

        LaravelSession::put('datosCompra', $session);

        Storage::put($storagePath, $pdf->output());

        return 'PDF guardado en la ruta: ' . $storagePath;
    }

    /**
     * Permite la descarga de un archivo PDF de tickets previamente generado.
     * Busca el archivo y lo devuelve como una respuesta de descarga.
     *
     * @param  string $nombrePdf
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function descargarPDF($nombrePdf){

        $filePath = public_path("storage/pdfs/{$nombrePdf}"); 

        return response()->download($filePath);

    }
    
}