@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $event->name }}</h1>

        <!-- Carrusel de fotografías -->
        <div id="eventCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                @if ($event->main_image)
                    <div class="carousel-item active">
                        <img src="{{ asset('storage/' . $event->main_image) }}" class="d-block w-100"
                            alt="{{ $event->name }}">
                    </div>
                @endif

                @foreach ($event->images as $index => $image)
                    <div class="carousel-item {{ $index == 0 && !$event->main_image ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $image->image_url) }}" class="d-block w-100"
                            alt="{{ $image->alt_text ?? 'Evento' }}">
                    </div>
                @endforeach
            </div>
            @if ($event->images->count() > 5 || $event->main_image)
                <a class="carousel-control-prev" href="#eventCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Anterior</span>
                </a>
                <a class="carousel-control-next" href="#eventCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Siguiente</span>
                </a>
            @endif
        </div>

        <!-- Descripción del evento -->
        <p>{{ $event->description }}</p>

        <!-- Datos del local -->
        <div class="venue-details">
            <h3>Detalles del Local</h3>
            <p>Nombre: {{ $event->venue->venue_name }}</p>
            <p>Ubicación: {{ $event->venue->city }}, {{ $event->venue->province }}</p>
            <p>Fecha del Evento: {{ \Carbon\Carbon::parse($event->event_date)->format('Y-M-D , H:i') }}</p>
        </div>

        <div class="card-body">
            <div id='calendar'></div>
        </div>

        <div id="sessionDetails" style="display: none;">
            <h3>Sesiones para la Fecha Seleccionada:</h3>
            <ul id="sessionList"></ul>
        </div>

        <div id="totalPriceContainer" style="display: none;">
            <h3>Precio Total: <span id="totalPrice">0</span> $</h3>
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

            ticketTypes.forEach(ticketType => {
                const quantity = selectedTickets[ticketType.id] || 0;
                total += quantity * ticketType.price;
            });

            totalPriceElement.textContent = total.toFixed(2);
        }

        function clearSessionsDisplay() {
            const sessionList = document.getElementById('sessionList');
            sessionList.innerHTML = '';
            document.getElementById('sessionDetails').style.display = 'none';
            document.getElementById('totalPrice').textContent = '0.00';
            document.getElementById('totalPriceContainer').style.display = 'none';
        }
    </script>
@endpush
