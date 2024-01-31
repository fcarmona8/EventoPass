@extends('layouts.app')

@section('content')
    <div class="container">

        <!-- Carrusel de fotografías -->
        <div class="slider-frame">
            <ul>
                @if ($event->main_image)
                    <li>
                        @if ($event->main_image)
                            <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->name }}"
                                onerror="this.onerror=null; this.src='https://picsum.photos/200'">
                        @else
                            <img src="https://picsum.photos/2000" alt="{{ $event->name }}">
                        @endif
                    </li>
                @endif

                @foreach ($event->images as $index => $image)
                    <li>
                        @if ($event->main_image)
                            <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->name }}"
                                onerror="this.onerror=null; this.src='https://picsum.photos/200'">
                        @else
                            <img src="https://picsum.photos/2000" alt="{{ $event->name }}">
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <h1>{{ $event->name }}</h1>
        <!-- Descripción del evento -->
        <div class="divDescEvent">
            <p class="pDescEvent">{{ $event->description }}</p>
        </div>

        <!-- Datos del local -->
        <div class="venue-details">
            <h2 class="h2-showevent">Detalles del Local</h2>
            <li class="eventDetailList"><span>Nom del local: {{ $event->venue->venue_name }}</span></li>
            <li class="eventDetailList"><span>Ubicació: {{ $event->venue->city }}, {{ $event->venue->province }}</span>
            </li>
            <li class="eventDetailList"><span>Primera sessió:
                    {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y, H:i') }}</span></li>
            <!-- Mapa de Google Maps -->
            <div id="map" style="height: 400px;"></div>
        </div>
        <div id="sessionDetails" style="display: none;">
            <h3 class="h3Color">Sesiones para la Fecha Seleccionada:</h3>
            <ul id="sessionList"></ul>
        </div>

        <div id="totalPriceContainer" style="display: none;">
            <h3 class="h3Color">Precio Total: <span id="totalPrice">0</span> €</h3>
        </div>
        <div class="dvBotonCompra">
            <form action="{{ route('tickets.purchaseconfirm') }}" method="POST">
                @csrf
                <input type="hidden" name="eventId" value="{{ $event->id }}">
                <input type="hidden" name="totalPrice" id="totalPriceInput" value="0">
                <input type="hidden" name="ticketData" id="ticketDataInput" value="{}">
                <button type="submit" id="buyButton" class="btn btn-primary btnCompra"
                    style="display: none;">Comprar</button>
            </form>
        </div>
        <div class="card-body">
            <div id='calendar'></div>
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

                calendarEl.addEventListener('click', function(e) {
                    const dayCell = e.target.closest('.fc-daygrid-day');
                    if (dayCell) {
                        const dateStr = dayCell.getAttribute('data-date');
                        if (dateStr) {
                            const event = calendar.getEvents().find(ev => ev.startStr === dateStr);
                            if (event) {
                                displaySessions(event.extendedProps.sessions);
                            } else {
                                clearSessionsDisplay();
                            }
                        }
                    }
                });
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
                        `${ticketType.name}: ${ticketType.price} € (${ticketType.available_tickets} disponibles)`;

                    const inputQuantity = document.createElement('input');
                    inputQuantity.type = 'number';
                    inputQuantity.min = 0;
                    inputQuantity.max = ticketType.available_tickets;
                    inputQuantity.value = 0;
                    inputQuantity.classList.add('inputQuantity');
                    inputQuantity.addEventListener('input', function() {
                        selectedTickets[ticketType.id] = parseInt(inputQuantity.value);
                        recalculateTotalPrice(ticketTypes);
                    });

                    ticketItem.appendChild(inputQuantity);
                    sessionList.appendChild(ticketItem);
                });

                const buyButton = document.getElementById('buyButton');
                buyButton.style.display = 'block';

                recalculateTotalPrice(ticketTypes);
            }

            function recalculateTotalPrice(ticketTypes) {
                let totalPrice = 0;
                for (const ticketType of ticketTypes) {
                    if (selectedTickets[ticketType.id] > 0) {
                        totalPrice += selectedTickets[ticketType.id] * ticketType.price;
                    }
                }

                const totalPriceContainer = document.getElementById('totalPriceContainer');
                const totalPriceElement = document.getElementById('totalPrice');
                totalPriceElement.textContent = totalPrice.toFixed(2);
                totalPriceContainer.style.display = 'block';

                const totalPriceInput = document.getElementById('totalPriceInput');
                totalPriceInput.value = totalPrice.toFixed(2);

                const ticketDataInput = document.getElementById('ticketDataInput');
                ticketDataInput.value = JSON.stringify(selectedTickets);
            }

            function clearSessionsDisplay() {
                const sessionList = document.getElementById('sessionList');
                sessionList.innerHTML = '';
                document.getElementById('sessionDetails').style.display = 'none';
            }
        </script>
    @endpush
