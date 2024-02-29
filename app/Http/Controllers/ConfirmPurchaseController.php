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
    /**
     * Muestra la página de confirmación de compra con los detalles del evento, sesión, tipos de tickets y precios.
     * Valida la existencia de la sesión y el evento, y prepara los datos para la vista.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
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

    /**
     * Inicia el proceso de pago, ya sea redirigiendo a la pasarela de pagos o completando la compra sin pago.
     * Determina si se debe saltar la pasarela de pagos basado en la configuración o requisitos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createPayment(Request $request)
    {
        $skipPaymentGateway = env('SKIP_PAYMENT_GATEWAY', false);

        $totalPrice = $request->input('totalPrice');
        $eventId = $request->input('eventId');
        $eventubi = $this->stringUbicacion($eventId);
        $request->merge(['eventubi' => $eventubi]);
        sessionLaravel::put('datosCompra', $request->all());


        if ($skipPaymentGateway || $totalPrice == 0) {
            return $this->completePurchaseWithoutPayment($request);
        } else {
            $amount = (int) ($totalPrice * 100);

            $order = time();
            $merchantCode = '999008881';
            $currency = '978';
            $transactionType = '0';
            $terminal = '1';
            $merchantURL = '';
            $authCode = '123456';

            $redsys = new \RedsysAPI;

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

            $params = $redsys->createMerchantParameters();
            $signature = $redsys->createMerchantSignature('sq7HjrUOBfKmC576ILgskD5srU870gJ7');

            return view('payment.paymentform', compact('params', 'signature'));
        }
    }

    /**
     * Completa la compra sin pasar por la pasarela de pagos.
     * Utilizado cuando el total de la compra es 0 o la pasarela de pagos está deshabilitada.
     * Genera los PDF de los tickets y guarda la compra en la base de datos.
     *
     * @param  mixed $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function completePurchaseWithoutPayment($request)
    {
        $session = sessionLaravel::get('datosCompra');
        $ticketsPDFController = new TicketsPDFController();

        if ($session['nominals?']) {
            $ticketsPDFController->generatePdfNominal();
        } else {
            $ticketsPDFController->generatePdf();
        }


        $session = sessionLaravel::get('datosCompra');
        $compra = new Purchase;
        $compra->generarCompra($session['sessionId'], $session['totalPrice'], $session['buyerName'], $session['buyerEmail'], $session['buyerDNI'], $session['buyerPhone'], $session['nEntrades'], $session['namePDF']);

        MailController::enviarEntrades($session['buyerEmail'], $session['buyerDNI'] . $session['sessionId'], $session['eventName'], $session['eventId']);

        return redirect()->route('payment.response');
    }

    /**
     * Genera una cadena de texto con la ubicación del evento formateada.
     * Utiliza el ID del evento para buscar la ubicación y formatearla.
     *
     * @param  int $eventId
     * @return string
     */
    public function stringUbicacion($eventId)
    {
        $eventVenueid = Event::find($eventId);

        $eventubi = Venue::searchByVenueId($eventVenueid->venue_id)->value('venue_name');
        $eventubi = $eventubi . ', ' . Venue::searchByVenueId($eventVenueid->venue_id)->value('city');
        $eventubi = $eventubi . ', ' . Venue::searchByVenueId($eventVenueid->venue_id)->value('province');
        $eventubi = $eventubi . ', ' . Venue::searchByVenueId($eventVenueid->venue_id)->value('postal_code');

        return $eventubi;
    }

}