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
            <button id="buyButton" class="btn btn-primary btnCompra" style="display: none;">Comprar</button>
        </div>
        <div class="card-body">
            <div id='calendar'></div>
        </div>

        <div class="contenedorComentarios">
            <h2 class="h2-showevent">Comentaris</h2>
            @foreach ($comentarios as $comentario)
                <div class="contenedorComentario">
                    <h3 class="tituloComentario">{{ strtoupper($comentario->titulo) }}</h3>
                    <hr class="separadorComentario">
                    <div class="contenedorPuntuacionUsuario">
                        <h4 class="usuarioComentario">Usuari: {{ $comentario->nombre }}</h4>
                        <div class="puntuacionUsuario">
                            <span>Valoració: </span>
                            <span class="stars">
                                @for ($i = 0; $i < $comentario->puntuacion; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                        viewBox="0 0 15 16" fill="none">
                                        <path
                                            d="M13.8465 7.57717C14.1221 7.28942 14.2194 6.86712 14.1005 6.47455C13.9814 6.08197 13.671 5.80174 13.2898 5.74227L9.89994 5.21453C9.75557 5.19201 9.63082 5.09499 9.56634 4.95472L8.05084 1.66397C7.88068 1.29423 7.5353 1.06445 7.1504 1.06445C6.76578 1.06445 6.4204 1.29423 6.25024 1.66397L4.73446 4.95502C4.66998 5.09529 4.54495 5.19231 4.40058 5.21483L1.01075 5.74257C0.629774 5.80174 0.319162 6.08227 0.200019 6.47485C0.0811561 6.86742 0.178433 7.28972 0.454003 7.57747L2.90667 10.1389C3.01123 10.2483 3.05917 10.4057 3.0345 10.5594L2.45589 14.1764C2.40458 14.4948 2.48252 14.8044 2.67483 15.0486C2.97367 15.4292 3.49537 15.5451 3.91251 15.3102L6.94407 13.6024C7.07078 13.5312 7.23029 13.5318 7.35673 13.6024L10.3886 15.3102C10.536 15.3934 10.6933 15.4355 10.8556 15.4355C11.1519 15.4355 11.4328 15.2943 11.626 15.0486C11.8186 14.8044 11.8962 14.4942 11.8449 14.1764L11.266 10.5594C11.2413 10.4054 11.2893 10.2483 11.3939 10.1389L13.8465 7.57717Z"
                                            fill="#FFB800" />
                                    </svg>
                                @endfor
                            </span>
                        </div>
                    </div>

                    <hr class="separadorComentario">
                    <p class="textoComentario">{{ $comentario->comentario }}</p>
                </div>
            @endforeach
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
