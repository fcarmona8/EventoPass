@extends('layouts.app')

@section('content')
    <style>
        #btn_submit {
            display: none;
        }
    </style>

    <div class="container">
        <h2>Confirmación de Compra</h2>

        {{-- Temporizador --}}
        <div id="timer" class="timer">
            10:00
        </div>

        {{-- Detalles del Evento --}}
        <div class="event-details">
            <h3>{{ $event->name }}</h3>
            <p>Fecha: {{ head(explode(' ', $sessio->date_time)) }}</p>
            <p>Hora: {{ implode(' ', array_slice(explode(' ', $sessio->date_time), 1)) }}</p>
            <p>Precio Total: €{{ $totalPrice }}</p>            
        </div>

        {{-- Formulario de Datos Personales --}}
        <form id="purchase-form" action="{{ route('tickets.createPayment') }}" method="POST">
            @csrf
            <input type="hidden" name="eventId" value="{{ $event->id }}">
            <input type="hidden" name="totalPrice" value="{{ $totalPrice }}">

            @if ($areTicketsNominal)
                {{-- Campos para cada asistente cuando es nominal --}}
                @foreach ($ticketData as $ticketTypeId => $quantity)
                    @for ($i = 1; $i <= $quantity; $i++)
                        <div class="attendee-details">
                            <h4>Detalles del Asistente {{ $i }}</h4>
                            <p>Tipo de entrada: {{ $ticketTypes[$ticketTypeId]->name }}</p>
                            <p>Precio individual: €{{ $ticketTypes[$ticketTypeId]->price }}</p>
                            <input type="text" name="attendee[{{ $ticketTypeId }}][{{ $i }}][name]"
                                placeholder="Nombre Asistente {{ $i }}" required>
                            <input type="text" name="attendee[{{ $ticketTypeId }}][{{ $i }}][dni]"
                                placeholder="DNI Asistente {{ $i }}" required>
                            <input type="text" name="attendee[{{ $ticketTypeId }}][{{ $i }}][phone]"
                                placeholder="Teléfono Asistente {{ $i }}" required>
                        </div>
                    @endfor
                @endforeach
            @else
                {{-- Cuando no es nominal, mostrar cantidad total de entradas y tipo --}}
                <div class="non-nominal-details">
                    <h4>Detalles de la Compra (No Nominal)</h4>
                    <p>Número total de entradas: {{ array_sum($ticketData) }}</p>
                    <input type="hidden" name="nEntrades" value= {{ array_sum($ticketData) }}>
                    @php
                    $pos = 1;
                    @endphp
                    @foreach ($ticketData as $ticketTypeId => $quantity)
                        @php
                            $ticketType = $ticketTypes->firstWhere('id', $ticketTypeId);
                        @endphp
                        @if ($ticketType)
                            <p>{{ $ticketType->name }}: {{ $quantity }}</p>
                            <input type="hidden" name="ticketName{{$pos}}" value={{$ticketType->name}}>
                            <input type="hidden" name="ticketNameId{{$pos}}" value={{$ticketType->id}}>
                            <input type="hidden" name="ticketNameNum{{$pos}}" value = {{$quantity}}>
                            <input type="hidden" name="ticketNameEur{{$pos}}" value = {{$ticketType->price}}>
                            <input type="hidden" name="horaSession" value={{ implode(' ', array_slice(explode(' ', $sessio->date_time), 1)) }}>
                            <input type="hidden" name="fechaSession" value={{ head(explode(' ', $sessio->date_time)) }}>
                            <input type="hidden" name="eventName" value = {{$event->name}}>
                            <input type="hidden" name="sessionId" value = {{$sessio->id}}>

                            @php
                            $pos++;
                            @endphp
                        @else
                            <p>Tipo de entrada desconocido: ID {{ $ticketTypeId }}</p>
                        @endif
                    @endforeach
                </div>

                <div class="buyer-details">
                    <h4>Datos del Comprador</h4>
                    <input type="text" name="buyerName" placeholder="Nombre del Comprador" required>
                    <input type="text" name="buyerDNI" placeholder="DNI del Comprador" required>
                    <input type="text" name="buyerPhone" placeholder="Teléfono del Comprador" required>
                    <input type="email" name="buyerEmail" placeholder="Correo Electrónico del Comprador" required>
                </div>
            @endif

            <input type="hidden" name="ticketData" id="ticketData" value=''>

            <button type="submit" id="continue-button" class="btn btn-primary">Continuar</button>

        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let timer = 10 * 60;
        const timerElement = document.getElementById('timer');
        const countdownElement = document.getElementById('countdown');
        const minutesElement = document.getElementById('minutes');
        const secondsElement = document.getElementById('seconds');

        const interval = setInterval(() => {
            const minutes = Math.floor(timer / 60);
            const seconds = timer % 60;
            timerElement.textContent =
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            const countdownElement = document.getElementById('countdown');

            if (countdownElement) {
                countdownElement.style.visibility = 'visible';
            }
            const minutesElement = document.getElementById('minutes');

            if (minutesElement) {
                minutesElement.textContent = minutes.toString().padStart(2, '0');
            }
            const secondsElement = document.getElementById('seconds');

            if (secondsElement) {
                secondsElement.textContent = seconds.toString().padStart(2, '0');
            }
            timer--;

            if (timer < 0) {
                clearInterval(interval);
                window.location.href = `/tickets/showevent/${eventId}`;
            }
        }, 1000);
    </script>
@endpush
