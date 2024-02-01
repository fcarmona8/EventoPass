<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Session;
use App\Models\Purchase;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Ssheduardo\Redsys\Facades\Redsys;

class ConfirmPurchaseController extends Controller
{
    public function showConfirmPurchase(Request $request)
    {
        $eventId = $request->input('eventId');
        $totalPrice = $request->input('totalPrice');
        $ticketData = json_decode($request->input('ticketData'), true);
        
        $event = Event::with('venue')->find($eventId);
        $ticketTypes = TicketType::findMany(array_keys($ticketData));

        \Log::info('TicketData: ', $ticketData);
        \Log::info('TicketTypes: ', $ticketTypes->toArray());

        $areTicketsNominal = $event->nominal;
        \Log::info('Valor de $areTicketsNominal: ' . $areTicketsNominal);

        // Lógica para generar el formulario de Redsys
        try {
            $key = config('redsys.key');
            $code = config('redsys.merchantcode');
            $order = time();

            Redsys::setAmount($totalPrice);
            Redsys::setOrder($order);
            Redsys::setMerchantcode($code);
            Redsys::setCurrency('978');
            Redsys::setTransactiontype('0');
            Redsys::setTerminal('1');
            Redsys::setMethod('T');
            Redsys::setNotification(config('redsys.url_notification'));
            Redsys::setUrlOk(config('redsys.url_ok'));
            Redsys::setUrlKo(config('redsys.url_ko'));
            Redsys::setVersion('HMAC_SHA256_V1');
            Redsys::setTradeName('EventoPass S.L');
            Redsys::setProductDescription('Compra entradas');
            Redsys::setEnviroment('test'); 

            $signature = Redsys::generateMerchantSignature($key);
            Redsys::setMerchantSignature($signature);

            $form = Redsys::createForm();
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        // Asegúrate de pasar 'form' a tu vista
        return view('tickets.purchaseconfirm', compact('eventId', 'event', 'totalPrice', 'ticketTypes', 'ticketData', 'areTicketsNominal', 'form'));
    }
    
}