{{-- resources/views/tickets/paymentForm.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="contenedorPago">
        <h2 class="titulo-pagina-pago">Pago Seguro con Redsys</h2>
        <form class="formularioPago" name="redsysForm" action="{{ route('initiatePayment') }}" method="POST">
            @csrf
            <input type="text" class="numeroTarjeta" name="creditCard" placeholder="Número de Tarjeta" required>
            <input type="text" name="expirationDate" placeholder="Fecha de Vencimiento (MM/AA)" id="fechaTarjeta"
                required>
            <input type="text" name="CVV" placeholder="CVV" maxlength="4" required>
            <input type="hidden" name="Ds_SignatureVersion" value="HMAC_SHA256_V1">
            <input type="hidden" name="Ds_MerchantParameters" value="{{ $params }}">
            <input type="hidden" name="Ds_Signature" value="{{ $signature }}">
            <button type="submit" class="botonPago">Proceder al pago</button>
        </form>
    </div>
@endsection


@push('scripts')
    <script>
        const fechaInput = document.getElementById('fechaTarjeta');


        fechaInput.addEventListener('input', function(e) {
            const input = e.target;
            let value = input.value.replace(/\D/g, '');
            let formattedValue = '';

            if (value.length > 2) {
                formattedValue += value.substr(0, 2) + '/';
                if (value.length > 4) {
                    formattedValue += value.substr(2, 2);
                } else {
                    formattedValue += value.substr(2);
                }
            } else {
                formattedValue = value;
            }

            input.value = formattedValue;
        });
    </script>
@endpush
