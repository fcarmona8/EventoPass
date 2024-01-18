@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $event->name }}</h1>

        <!-- Carrusel de fotografías -->
        <div class="slider-frame">
            <ul>
                @if ($event->main_image)
                    <li>
                        <img src="{{ asset('storage/' . $event->main_image) }}" class="d-block w-100"
                            alt="{{ $event->name }}">
                    </li>
                @endif

                @foreach ($event->images as $index => $image)
                    <li>
                        <img src="{{ asset('storage/' . $image->image_url) }}" class="d-block w-100"
                            alt="{{ $image->alt_text ?? 'Evento' }}">
                    </li>
                @endforeach
            </ul>
        </div>


        <!-- Descripción del evento -->
        <h1>Descripció:</h1>
        <div class="divDescEvent">
        <p class="pDescEvent">{{ $event->description }}</p>
        </div>

        <!-- Datos del local -->
        <div class="venue-details">
            <h3>Detalles del Local</h3>
            <p>Nombre: {{ $event->venue->venue_name }}</p>
            <p>Ubicación: {{ $event->venue->city }}, {{ $event->venue->province }}</p>
            <p>Fecha del Evento: {{ \Carbon\Carbon::parse($event->event_date)->format('Y-M-D , H:i') }}</p>
            <!-- Mapa de Google Maps -->
            <div id="map" style="height: 400px;"></div>
        </div>

        <div id="sessionDetails" style="display: none;">
            <h3 class="h3Color">Sesiones para la Fecha Seleccionada:</h3>
            <ul id="sessionList"></ul>
        </div>

        <div id="totalPriceContainer" style="display: none;">
            <h3 class="h3Color">Precio Total: <span id="totalPrice">0</span> $</h3>
        </div>
        <div class="dvBotonCompra">
        <button id="buyButton" class="btn btn-primary btnCompra" style="display: none;">Comprar</button>
        </div>
        <div class="card-body">
            <div id='calendar'></div>
        </div>
    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    @foreach ($formattedSessions as $session)
                        {
                            title: '{{ $session['count'] . ' sesiones' }}',
                            start: '{{ $session['date'] }}',
                            extendedProps: {
                                sessions: @json($session['sessions'])
                            }
                        },
                    @endforeach
                ],
                dateClick: function(info) {
                    const event = calendar.getEvents().find(e => e.startStr === info.dateStr);
                    if (event) {
                        displaySessions(event.extendedProps.sessions);
                    } else {
                        clearSessionsDisplay();
                    }
                }
            });

            calendar.render();
        });

        const selectedTickets = {};

        function displaySessions(sessions) {
            const sessionList = document.getElementById('sessionList');
            sessionList.innerHTML = '';

            sessions.forEach(session => {
                const sessionItem = document.createElement('li');
                sessionItem.textContent = `Sesión a las ${session.formattedDateTime}`;
                sessionItem.style.cursor = 'pointer';
                sessionItem.onclick = () => displayTicketTypes(session.ticketTypes);
                sessionList.appendChild(sessionItem);
            });

            document.getElementById('sessionDetails').style.display = 'block';
        }

        function displayTicketTypes(ticketTypes) {
            const sessionList = document.getElementById('sessionList');
            sessionList.innerHTML = '';

            ticketTypes.forEach(ticketType => {
                const ticketItem = document.createElement('li');
                ticketItem.textContent =
                    `${ticketType.name}: $${ticketType.price} (${ticketType.available_tickets} disponibles)`;

                const inputQuantity = document.createElement('input');
                inputQuantity.type = 'number';
                inputQuantity.min = 0;
                inputQuantity.max = ticketType.available_tickets;
                inputQuantity.value = 0;
                inputQuantity.classList.add('inputQuantity');
                inputQuantity.addEventListener('change', function() {
                    selectedTickets[ticketType.id] = parseInt(inputQuantity.value);
                    recalculateTotalPrice(ticketTypes);
                });

                ticketItem.appendChild(inputQuantity);
                sessionList.appendChild(ticketItem);
            });

            document.getElementById('totalPriceContainer').style.display = 'block';
        }

        function recalculateTotalPrice(ticketTypes) {
            const totalPriceElement = document.getElementById('totalPrice');
            let total = 0;
            let anyTicketsSelected = false;

            ticketTypes.forEach(ticketType => {
                const quantity = selectedTickets[ticketType.id] || 0;
                total += quantity * ticketType.price;
                if (quantity > 0) {
                    anyTicketsSelected = true;
                }
            });

            totalPriceElement.textContent = total.toFixed(2);

            // Actualizar la visibilidad del botón de compra
            const buyButton = document.getElementById('buyButton');
            if (anyTicketsSelected) {
                buyButton.style.display = 'block';
            } else {
                buyButton.style.display = 'none';
            }
        }

        function clearSessionsDisplay() {
            const sessionList = document.getElementById('sessionList');
            sessionList.innerHTML = '';
            document.getElementById('sessionDetails').style.display = 'none';
            document.getElementById('totalPrice').textContent = '0.00';
            document.getElementById('totalPriceContainer').style.display = 'none';

            // Ocultar el botón de compra
            document.getElementById('buyButton').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const imagesCount = document.querySelectorAll('.slider-frame li').length;
            const sliderUl = document.querySelector('.slider-frame ul');

            if (imagesCount > 0) {
                // Ajustar el ancho del contenedor UL
                sliderUl.style.width = `${imagesCount * 100}%`;

                // Calcular los keyframes
                const percentagePerImage = 100 / imagesCount;
                let keyframes = '';

                for (let i = 0; i < imagesCount; i++) {
                    keyframes += `
                    ${i * percentagePerImage * 2}% {margin-left: ${-100 * i}%}
                    ${(i * percentagePerImage * 2) + percentagePerImage}% {margin-left: ${-100 * i}%}`;
                }

                const styleSheet = document.createElement('style');
                styleSheet.type = 'text/css';
                styleSheet.innerText = `@keyframes slide { ${keyframes} }`;
                document.head.appendChild(styleSheet);
            }
        });
    </script>
@endpush
