<?php

namespace App\Http\Controllers;

use App\Models\Purchase;

require_once base_path('app/redsysHMAC256_API_PHP_7.0.0/apiRedsys.php');

use RedsysAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\TicketsPDFController;
use Illuminate\Support\Facades\Session as sessionLaravel;

class PaymentController extends Controller
{
    /**
     * Inicia el proceso de pago mediante la pasarela de pago de Redsys.
     * Valida los datos de la tarjeta de crédito proporcionados, prepara y envía una solicitud a Redsys
     * con los parámetros de pago modificados y gestiona la respuesta de Redsys.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function initiatePayment(Request $request)
    {

        $validated = $request->validate([
            'creditCard' => 'required|numeric',
            'expirationDate' => 'required',
            'CVV' => 'required|numeric'
        ]);

        $expirationParts = explode('/', $validated['expirationDate']);
        $expirationDateAAMM = $expirationParts[1] . $expirationParts[0];

        $redsysAPI = new RedsysAPI;

        $decodedParams = json_decode(base64_decode($request->input('Ds_MerchantParameters')), true);

        $decodedParams['DS_MERCHANT_PAN'] = $validated['creditCard'];
        $redsysAPI->setParameter("DS_MERCHANT_EXPIRYDATE", $expirationDateAAMM);
        $decodedParams['DS_MERCHANT_CVV2'] = $validated['CVV'];

        foreach ($decodedParams as $key => $value) {
            $redsysAPI->setParameter($key, $value);
        }
        $modifiedParams = $redsysAPI->createMerchantParameters();

        $signature = $redsysAPI->createMerchantSignature(env('REDSYS_SECRET_KEY'));

        $requestData = [
            'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
            'Ds_MerchantParameters' => $modifiedParams,
            'Ds_Signature' => $signature,
        ];

        $redsysUrl = env('REDSYS_URL', 'https://sis-t.redsys.es:25443/sis/rest/trataPeticionREST');

        $response = Http::withoutVerifying()->post($redsysUrl, $requestData);

        if ($response->successful()) {
            $responseData = $response->json();

            $decodedResponseParams = json_decode(base64_decode($responseData['Ds_MerchantParameters']), true);

            if (isset($decodedResponseParams['Ds_Response']) && (int) $decodedResponseParams['Ds_Response'] <= 99 || $decodedResponseParams['Ds_Response'] === "0173") {

                $session = sessionLaravel::get('datosCompra');
                $ticketsPDFController = new TicketsPDFController();

                $pdfName = $session['buyerDNI'] . $session['sessionId'];
                $session['namePDF'] = $pdfName.'.pdf';
                sessionLaravel::put('datosCompra', $session);

                $session = sessionLaravel::get('datosCompra');
                $compra = new Purchase;
                $compra->generarCompra($session['sessionId'], $session['totalPrice'], $session['buyerName'], $session['buyerEmail'], $session['buyerDNI'], $session['buyerPhone'], $session['nEntrades'], $session['namePDF']);

                if ($session['nominals?']) {
                    $ticketsPDFController->generatePdfNominal();
                } else {
                    $ticketsPDFController->generatePdf();
                }

                MailController::enviarEntrades($session['buyerEmail'], $session['buyerDNI'] . $session['sessionId'], $session['eventName'], $session['eventId']);

                return view('payment.response');
            } else {
                return view('payment.errorResponse', ['error' => 'Transacción rechazada o fallida.']);
            }
        } else {
            return view('payment.errorResponse', ['error' => 'Error al conectar con el sistema de pago.']);
        }
    }
}
