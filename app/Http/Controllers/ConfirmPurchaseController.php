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

    public function savePurchaseData(Request $request)
    {
        $ticketData = json_decode($request->input('ticketData'), true);

        if (is_null($ticketData) || !is_array($ticketData) || empty($ticketData)) {
            \Log::error('El ticketData recibido no es un JSON válido o está vacío.', ['ticketData' => $request->input('ticketData')]);
            return response()->json(['success' => false, 'message' => 'El ticketData recibido no es un JSON válido.'], 400);
        }

        $validatedData = $request->validate([
            'eventId' => 'required|integer',
            'totalPrice' => 'required|numeric',
            'buyerName' => 'required|string',
            'buyerDNI' => 'required|string',
            'buyerPhone' => 'required|string',
            'buyerEmail' => 'required|email',
        ]);

        $eventId = $validatedData['eventId'];
        $totalPrice = $validatedData['totalPrice'];

        DB::beginTransaction();
        try {
            $purchase = Purchase::create([
                'session_id' => $eventId,
                'name' => $validatedData['buyerName'],
                'dni' => $validatedData['buyerDNI'],
                'phone' => $validatedData['buyerPhone'],
                'email' => $validatedData['buyerEmail'],
                'total_price' => $totalPrice
            ]);

            foreach ($ticketData as $ticketTypeId => $quantity) {
                $ticketType = TicketType::find($ticketTypeId);
                if ($ticketType && $quantity > 0) {
                    if ($ticketType->available_tickets >= $quantity) {
                        $ticketType->available_tickets -= $quantity;
                        $ticketType->save();

                        for ($i = 0; $i < $quantity; $i++) {
                            Ticket::create([
                                'purchase_id' => $purchase->id,
                                'type_id' => $ticketTypeId,
                                'session_id' => $eventId
                            ]);
                        }
                    } else {
                        throw new Exception("No hay suficientes tickets disponibles para el tipo de ticket ID: {$ticketTypeId}.");
                    }
                } else {
                    throw new Exception("Tipo de ticket ID: {$ticketTypeId} no encontrado o cantidad inválida.");
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Datos de compra guardados exitosamente.']);
        } catch (Exception $e) {
            DB::rollback();
            \Log::error('Error al procesar la compra.', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function ok(Request $request)
    {
        // Asignar el estado 'success' directamente
        $response['status'] = 'success';

        // Redirigir a la vista 'payment.response', enviando $message, $decode, y $response
        return view('payment.response', compact('response'));
    }

}