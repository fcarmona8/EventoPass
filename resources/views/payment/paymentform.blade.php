{{-- resources/views/tickets/paymentForm.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Pago Seguro con Redsys</h2>
        <form name="redsysForm" action="{{ route('initiatePayment') }}" method="POST">
            @csrf
            <input type="text" name="creditCard" placeholder="Número de Tarjeta" required>
            <input type="text" name="expirationDate" placeholder="Fecha de Vencimiento (MM/AA)" required>
            <input type="text" name="CVV" placeholder="CVV" required>
            <input type="hidden" name="Ds_SignatureVersion" value="HMAC_SHA256_V1">
            <input type="hidden" name="Ds_MerchantParameters" value="{{ $params }}">
            <input type="hidden" name="Ds_Signature" value="{{ $signature }}">
            <button type="submit">Proceder al pago</button>
        </form>
    </div>
@endsection
