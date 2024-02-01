@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Confirmación de Compra</h2>

        {{-- Temporizador --}}
        <div id="timer" class="timer">
            10:00
        </div>

        {{-- Detalles del Evento --}}
        <div class="event-details">
            <h3>{{ $event->name }}</h3>
            <p>Fecha: {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}</p>
            <p>Hora: {{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }}</p>
            <p>Precio Total: €{{ $totalPrice }}</p>
        </div>

        {{-- Formulario de Datos Personales --}}
        <form id="purchase-form" action="{{ route('tickets.processPurchase') }}" method="POST">
            @csrf
            <input type="hidden" name="eventId" value="{{ $event->id }}">
            <input type="hidden" name="totalPrice" value="{{ $totalPrice }}">

            @if ($areTicketsNominal)
                {{-- Campos para cada asistente cuando es nominal --}}
                @foreach ($ticketData as $ticketTypeId => $quantity)
                    @for ($i = 0; $i < $quantity; $i++)
                        <div class="attendee-details">
                            <h4>Detalles del Asistente {{ $i + 1 }}</h4>
                            <p>Tipo de entrada: {{ $ticketTypes[$ticketTypeId]->name }}</p>
                            <p>Precio individual: €{{ $ticketTypes[$ticketTypeId]->price }}</p>
                            <input type="text" name="attendee[{{ $ticketTypeId }}][{{ $i }}][name]"
                                placeholder="Nombre Asistente {{ $i + 1 }}" required>
                            <input type="text" name="attendee[{{ $ticketTypeId }}][{{ $i }}][dni]"
                                placeholder="DNI Asistente {{ $i + 1 }}" required>
                            <input type="text" name="attendee[{{ $ticketTypeId }}][{{ $i }}][phone]"
                                placeholder="Teléfono Asistente {{ $i + 1 }}" required>
                        </div>
                    @endfor
                @endforeach
            @else
                {{-- Cuando no es nominal, mostrar cantidad total de entradas y tipo --}}
                <div class="non-nominal-details">
                    <h4>Detalles de la Compra (No Nominal)</h4>
                    <p>Número total de entradas: {{ array_sum($ticketData) }}</p>
                    @foreach ($ticketData as $ticketTypeId => $quantity)
                        @php
                            $ticketType = $ticketTypes->firstWhere('id', $ticketTypeId);
                        @endphp
                        @if ($ticketType)
                            <p>{{ $ticketType->name }}: {{ $quantity }}</p>
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

            <button type="button" id="continue-button" class="btn btn-primary">Continuar</button>
        </form>

        {{-- Mensaje de Respuesta del Formulario --}}
        <div id="form-response"></div>

        {{-- Inserta el formulario de Redsys --}}
        <div class="redsys-form">
            {!! $form !!}
        </div>
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
