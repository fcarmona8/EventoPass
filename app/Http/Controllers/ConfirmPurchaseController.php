<?php

namespace App\Http\Controllers;

require_once base_path('app/redsysHMAC256_API_PHP_7.0.0/apiRedsys.php');

use Exception;
use App\Models\Event;
use App\Models\Ticket;
use GuzzleHttp\Client;
use App\Models\Purchase;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ConfirmPurchaseController extends Controller
{
    public function showConfirmPurchase(Request $request)
    {
        $eventId = $request->input('eventId');
        $totalPrice = $request->input('totalPrice');
        $ticketData = json_decode($request->input('ticketData'), true);

        $event = Event::with('venue')->find($eventId);
        $ticketTypes = TicketType::findMany(array_keys($ticketData));

        Log::info('TicketData: ', $ticketData);
        Log::info('TicketTypes: ', $ticketTypes->toArray());

        $areTicketsNominal = $event->nominal;
        Log::info('Valor de $areTicketsNominal: ' . $areTicketsNominal);
        

        return view('tickets.purchaseconfirm', compact('eventId', 'event', 'totalPrice', 'ticketTypes', 'ticketData', 'areTicketsNominal'));
    }

    public function createPayment(Request $request)
    {
        // Aquí debes implementar la lógica para recoger los datos de la compra
        $totalPrice = $request->input('totalPrice'); 
        $eventId = $request->input('eventId');
        
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
        
        Session::put('a', $request->all());

        // Pasar los datos a la vista
        return view('payment.paymentform', compact('params', 'signature'));
    }
}