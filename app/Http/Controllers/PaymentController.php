<?php

namespace App\Http\Controllers;
use App\Models\Purchase;

require_once base_path('app/RedsysHMAC256_API_PHP_7.0.0/apiRedsys.php');

use RedsysAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\TicketsPDFController;
use Illuminate\Support\Facades\Session as sessionLaravel;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'creditCard' => 'required|numeric',
            'expirationDate' => 'required',
            'CVV' => 'required|numeric'
        ]);

        // Transformación de la fecha de caducidad de MM/AA a AAMM
        $expirationParts = explode('/', $validated['expirationDate']);
        $expirationDateAAMM = $expirationParts[1] . $expirationParts[0]; // Cambio de MM/AA a AAMM

        // Instancia de RedsysAPI para manejar los parámetros
        $redsysAPI = new RedsysAPI;

        // Decodificar los parámetros recibidos para obtener un array asociativo
        $decodedParams = json_decode(base64_decode($request->input('Ds_MerchantParameters')), true);

        // Añadir/Modificar los datos de la tarjeta en el array de parámetros
        $decodedParams['DS_MERCHANT_PAN'] = $validated['creditCard'];
        $redsysAPI->setParameter("DS_MERCHANT_EXPIRYDATE", $expirationDateAAMM);
        $decodedParams['DS_MERCHANT_CVV2'] = $validated['CVV'];

        // Volver a codificar los parámetros modificados
        foreach ($decodedParams as $key => $value) {
            $redsysAPI->setParameter($key, $value);
        }
        $modifiedParams = $redsysAPI->createMerchantParameters();

        // Generar una nueva firma con los parámetros modificados
        $signature = $redsysAPI->createMerchantSignature(env('REDSYS_SECRET_KEY'));

        // Preparar la nueva solicitud con los parámetros y firma modificados
        $requestData = [
            'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
            'Ds_MerchantParameters' => $modifiedParams,
            'Ds_Signature' => $signature,
        ];

        $redsysUrl = env('REDSYS_URL', 'https://sis-t.redsys.es:25443/sis/rest/trataPeticionREST');

        // Enviar la solicitud modificada a Redsys
        $response = Http::withoutVerifying()->post($redsysUrl, $requestData);

        // Verificar la respuesta de Redsys
        if ($response->successful()) {
            $responseData = $response->json();

            // Decodificar y verificar los parámetros de salida de Redsys
            $decodedResponseParams = json_decode(base64_decode($responseData['Ds_MerchantParameters']), true);
            

            if (isset($decodedResponseParams['Ds_Response']) && (int)$decodedResponseParams['Ds_Response'] <= 99) {

                $session = sessionLaravel::get('datosCompra');
                $ticketsPDFController = new TicketsPDFController();

                if($session['nominals?']){
                    $ticketsPDFController->generatePdfNominal();
                }else{
                    $ticketsPDFController->generatePdf();
                }
                
                
                $session = sessionLaravel::get('datosCompra');
                $compra = new Purchase;
                $compra->generarCompra($session['sessionId'],$session['totalPrice'],$session['buyerName'],$session['buyerEmail'],$session['buyerDNI'],$session['buyerPhone'],$session['nEntrades'],$session['namePDF']);
                MailController::enviarEntrades($session['buyerEmail'],$session['buyerDNI'].$session['sessionId'],$session['eventName'],$session['eventId']);

                // Operación autorizada
                return view('payment.response');
            } else {
                // Operación rechazada o fallida
                return view('payment.response', ['error' => 'Transacción rechazada o fallida.']);
            }
        } else {
            // Error al conectar con Redsys
            return view('payment.response', ['error' => 'Error al conectar con el sistema de pago.']);
        }
    }
}
