<?php

namespace App\Http\Controllers;

require_once base_path('app/redsysHMAC256_API_PHP_7.0.0/apiRedsys.php');

use Exception;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Ticket;
use GuzzleHttp\Client;
use App\Models\Session;
use App\Models\Purchase;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session as sessionLaravel;

class ConfirmPurchaseController extends Controller
{
    public function showConfirmPurchase(Request $request)
    {
        $eventId = $request->input('eventId');
        $sessionId = $request->input('sessionId');
        $totalPrice = $request->input('totalPrice');
        $ticketData = json_decode($request->input('ticketData'), true);

        $sessio = Session::where('id', $sessionId)->first();

        $event = Event::with('venue')->find($eventId);
        $ticketTypes = TicketType::findMany(array_keys($ticketData));
        $session = Session::find($sessionId);

        Log::info('Session: ', ['sessionId' => $sessionId]);

        if (!$session) {
            return redirect()->back()->with('error', 'La sesión seleccionada no existe.');
        }

        Log::info('TicketData: ', $ticketData);
        Log::info('TicketTypes: ', $ticketTypes->toArray());

        $areTicketsNominal = $event->nominal;
        Log::info('Valor de $areTicketsNominal: ', ['areTicketsNominal' => $areTicketsNominal]);

        return view('tickets.purchaseconfirm', compact('eventId', 'event', 'totalPrice', 'ticketTypes', 'ticketData', 'areTicketsNominal', 'sessio'));
    }

    public function createPayment(Request $request)
    {
        // Esta variable controla si se debe saltar la pasarela de pagos, ya sea por configuración o por lógica de negocio.
        $skipPaymentGateway = env('SKIP_PAYMENT_GATEWAY', false);
        
        $totalPrice = $request->input('totalPrice');
        
        if ($skipPaymentGateway || $totalPrice == 0) {
            return $this->completePurchaseWithoutPayment($request);
        } else {
            $eventId = $request->input('eventId');

            $eventVenueid = Event::find($eventId);

            $eventubi = Venue::searchByVenueId($eventVenueid->venue_id)->value('venue_name');
            $eventubi = $eventubi . ', ' . Venue::searchByVenueId($eventVenueid->venue_id)->value('city');
            $eventubi = $eventubi . ', ' . Venue::searchByVenueId($eventVenueid->venue_id)->value('province');
            $eventubi = $eventubi . ', ' . Venue::searchByVenueId($eventVenueid->venue_id)->value('postal_code');

            
            // Convertir el precio a la forma que Redsys espera (sin decimales, como entero)
            $amount = (int)($totalPrice * 100);
            
            // Datos de la transacción
            $order = time();
            $merchantCode = '999008881';
            $currency = '978';
            $transactionType = '0'; 
            $terminal = '1';
            $merchantURL = '';
            $authCode = '123456';
            
            // Cargar la clase RedsysAPI
            $redsys = new \RedsysAPI;
            
            // Establecer parámetros
            $redsys->setParameter("DS_MERCHANT_AMOUNT", $amount);
            $redsys->setParameter("DS_MERCHANT_ORDER", $order);
            $redsys->setParameter("DS_MERCHANT_MERCHANTCODE", $merchantCode);
            $redsys->setParameter("DS_MERCHANT_CURRENCY", $currency);
            $redsys->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $transactionType);
            $redsys->setParameter("DS_MERCHANT_TERMINAL", $terminal);
            $redsys->setParameter("DS_MERCHANT_MERCHANTURL", $merchantURL);
            $redsys->setParameter("DS_MERCHANT_DIRECTPAYMENT", "true");
            $redsys->setParameter("DS_REDSYS_ENVIROMENT", "true");
            $redsys->setParameter("DS_MERCHANT_AUTHORISATIONCODE", $authCode);
            
            // Generar parámetros y firma
            $params = $redsys->createMerchantParameters();
            $signature = $redsys->createMerchantSignature('sq7HjrUOBfKmC576ILgskD5srU870gJ7');
            
            $request->merge(['eventubi' => $eventubi]);
            sessionLaravel::put('a', $request->all());

            // Pasar los datos a la vista
            return view('payment.paymentform', compact('params', 'signature'));
        }
    }

    protected function completePurchaseWithoutPayment($request)
    {
        // Generación del PDF de los tickets
        $ticketsPDFController = new TicketsPDFController();
        $ticketsPDFController->generatePdf();

        // Recuperación de la sesión y datos de la compra
        $session = sessionLaravel::get('a');

        // Registro de la compra en la base de datos
        $compra = new Purchase;
        $compra->generarCompra(
            $session['sessionId'],
            $session['totalPrice'],
            $session['buyerName'],
            $session['buyerEmail'],
            $session['buyerDNI'],
            $session['buyerPhone'],
            $session['nEntrades']
        );

        // Operación autorizada, redirigir al usuario a la página de éxito
        return redirect()->route('payment.response');
    }

}