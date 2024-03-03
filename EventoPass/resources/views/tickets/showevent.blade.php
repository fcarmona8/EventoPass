@extends('layouts.app')

@section('content')
    <div class="containerShowEvent">
        <div class="slideshow-container">

            <!-- Full-width images with number and caption text -->
            @if ($event->main_image_id)
                <div class="mySlides fade">
                    <picture>
                        <source media="(max-width: 799px)"
                            srcset=" {{ config('services.api.url') }}{{ $event->optimizedImageSmallUrl() }}">
                        <source media="(min-width: 800px) and (max-width: 1023px)"
                            srcset=" {{ config('services.api.url') }}{{ $event->optimizedImageMediumUrl() }}">
                        <img src="{{ config('services.api.url') }}{{ $event->optimizedImageLargeUrl() }}"
                            alt="{{ $event->name }}" loading="lazy"
                            onerror="this.onerror=null; this.src='https://picsum.photos/200'">
                    </picture>
                </div>
            @endif

            @foreach ($event->images as $index => $image)
                <div class="mySlides fade">
                    <picture>
                        <source media="(max-width: 799px)"
                            srcset="{{ config('services.api.url') }}/api/V1/optimized-images/{{ $image->image_id }}/small">
                        <source media="(min-width: 800px) and (max-width: 1023px)"
                            srcset="{{ config('services.api.url') }}/api/V1/optimized-images/{{ $image->image_id }}/medium">
                        <img src="{{ config('services.api.url') }}/api/V1/optimized-images/{{ $image->image_id }}/large"
                            alt="{{ $event->name }}" loading="lazy"
                            onerror="this.onerror=null; this.src='https://picsum.photos/200'">
                    </picture>
                </div>
            @endforeach

            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>
        <br>

        <!-- The dots/circles -->
        <div class="puntosImagenes">
            @if ($event->main_image_id)
                <span class="dot" onclick="currentSlide(1)"></span>
            @endif

            @foreach ($event->images as $index => $image)
                <span class="dot" onclick="currentSlide({{ $index + 2 }})"></span>
            @endforeach
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
                <input type="hidden" name="sessionId" id="sessionIdInput" value="">
                <button type="submit" id="buyButton" class="btn btnCompra" style="display: none;">Comprar</button>
            </form>
        </div>
        <div class="card-body">
            <div id='calendar'></div>
        </div>

        <div class="contenedorComentarios">
            <h2 class="h2-showevent">Comentaris</h2>
            @if ($comentarios->count() === 0)
                <div class="noComentarios">
                    <span>No hi ha comentaris</span>
                </div>
            @else
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
                        <span class="fechaComentario">Data:
                            {{ \Carbon\Carbon::parse($comentario->created_at)->format('d-m-y') }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const iconosRedesSociales = document.querySelectorAll('.fab');
            const bodyElement = document.body;


            if (window.innerWidth < 768) {
                bodyElement.style.backgroundColor = '#fff';
                iconosRedesSociales.forEach(icono => {
                    icono.style.color = '#000';
                });
            }

            if (calendarEl) {
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
            }

            @if ($coordinates)
                var map = L.map('map').setView([{{ $coordinates->lat }}, {{ $coordinates->lon }}], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                var marker = L.marker([{{ $coordinates->lat }}, {{ $coordinates->lon }}]).addTo(map);
            @endif
        });

        let slideIndex = 1;
        showSlides(slideIndex);

        // Next/previous controls
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        // Thumbnail image controls
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");
            if (n > slides.length) {
                slideIndex = 1
            }
            if (n < 1) {
                slideIndex = slides.length
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" actived", "");
            }
            slides[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].className += " actived";
        }

        let inputEntradas;

        const selectedTickets = {};

        function displaySessions(sessions) {
            const sessionList = document.getElementById('sessionList');
            sessionList.innerHTML = '';

            sessions.forEach(session => {
                const sessionItem = document.createElement('li');
                sessionItem.textContent = `Sesión a las ${session.formattedDateTime}`;
                sessionItem.style.cursor = 'pointer';
                sessionItem.onclick = () => displayTicketTypes(session.ticketTypes);
                document.getElementById('sessionIdInput').value = session.id;
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
                    if (inputQuantity.value > parseInt(inputQuantity.max)) {
                        inputQuantity.value = parseInt(inputQuantity.max)
                    }
                    selectedTickets[ticketType.id] = parseInt(inputQuantity.value);
                    recalculateTotalPrice(ticketTypes);
                });

                ticketItem.appendChild(inputQuantity);
                sessionList.appendChild(ticketItem);
                inputEntradas = document.querySelectorAll('.inputQuantity');
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
