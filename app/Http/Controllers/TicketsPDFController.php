<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session as LaravelSession;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketsPDFController extends Controller
{
    public function generatePdfNo()
    {
         $data = [
            'title' => 'a',
            'content' => 'a'
        ];


        $pdf = PDF::loadView('tickets.ticketsPDF.ticketsPDF', $data);

        //$pdfPath = 'pdfs/a.pdf';
        //Storage::put($pdfPath, $pdf->output());
        //return 'PDF guardado en la ruta: ' . $pdfPath;

        return $pdf->download('a.pdf');

        /*$title='aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $content = 'ªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªªª';*/

       //return view('tickets.ticketsPDF.ticketsPDF'); 
    }

    public function datosPDF(Request $request){


        if($request->get('nominals') == true){
            $this->generatePdfNominal($request);
        }else{
            $this->generatePdf($request);
        }
    }

    public function generatePdf(){
        $qrCodeImage = QrCode::size(300)->generate('https://copernic.cat/');
        $base64QrCode = base64_encode($qrCodeImage);

        $data = [
            'qrCode' => $base64QrCode
        ];

        $pdf = PDF::loadView('tickets.ticketsPDF.ticketsPdf', $data);

        $session =  LaravelSession::get('a');
        $pdfName = $session['buyerDNI'] . $session['sessionId'];

        $storagePath = 'public/pdfs/'.$pdfName .'.pdf';

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