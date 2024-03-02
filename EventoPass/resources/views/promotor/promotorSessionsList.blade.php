@extends('layouts.app')

@section('content')
    @if ($isSpecificEvent)
        <button type="button" class="btn btn-primary btnSessions" id="abrir-modal-sesion">Crear Nueva Sesión</button>
        <div class="listSessions" id="listSessions">
            @foreach ($sessions as $session)
                <div class="card cardHomePromotor">
                    @if ($session->event && $session->event->main_image_id)
                        <picture class="contenedorImagen">
                            <source media="(max-width: 799px)"
                                srcset="{{ config('services.api.url') }}{{ $session->event->optimizedImageSmallUrl() }}">
                            <source media="(min-width: 800px) and (max-width: 1023px)"
                                srcset="{{ config('services.api.url') }}{{ $session->event->optimizedImageMediumUrl() }}">
                            <img class="imagenSessionList"
                                src="{{ config('services.api.url') }}{{ $session->event->optimizedImageLargeUrl() }}"
                                alt="{{ $session->event->name }}" loading="lazy"
                                onerror="this.onerror=null; this.src='https://picsum.photos/200'">
                        </picture>
                    @else
                        <img src="https://picsum.photos/2000" alt="{{ $session->event->name }}" loading="lazy">
                    @endif
                    <div class="sessionCont">
                        <p>Data: {{ \Carbon\Carbon::parse($session->date_time)->format('Y-m-d, H:i') }}</p>
                        <p>Ventas: {{ $session->sold_tickets }} / {{ $session->max_capacity }}</p>
                        <p class="codigo_acceso">codi d'accés: {{ $session->session_code }}</p>
                        <div class="statusSessionDiv">
                            <p class="statusSessionText">Estat de la sessió: </p>
                            <span
                                class="statusSession @if ($session->date_time < now()) closed                                
                            @else
                                {{ $session->closed ? 'closed' : '' }} @endif">
                                @if ($session->date_time < now())
                                    Sessió Finalitzada
                                @else
                                    {{ $session->closed ? 'Tancada' : 'Oberta' }}
                                @endif
                            </span>
                        </div>
                        @if ($session->date_time < now())
                        @else
                            <button type="button" class="toggle-session-btn statusSessionBtn"
                                data-session-id="{{ $session->id }}"
                                data-session-closed="{{ $session->closed ? 'true' : 'false' }}" id="openModalButton">
                                {{ $session->closed ? 'Obrir la sessió' : 'Tancar la sessió' }}
                            </button>
                        @endif
                        <div class="divBoton">
                            <a class="card-price card-info card-sessions"
                                href="{{ route('tickets.showevent', ['id' => $session->event->id]) }}">Detalls</a>
                            <span class="card-price card-info card-sessions">Editar</span>
                            <a class="card-price card-info card-sessions btnCSV"
                                href="{{ route('promotorsessionslist.downloadCSV', ['id' => $session->id]) }}">Descargar
                                CSV</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="listEvents">
            @foreach ($events as $event)
                <h2>{{ $event->name }}</h2>
                <div class="listSessions">
                    @foreach ($event->sessions as $session)
                        <div class="card cardHomePromotor">
                            @if ($event->main_image_id)
                                <picture class="contenedorImagen imagenSesiones">
                                    <source media="(max-width: 799px)"
                                        srcset="{{ config('services.api.url') }}{{ $event->optimizedImageSmallUrl() }}">
                                    <source media="(min-width: 800px) and (max-width: 1023px)"
                                        srcset="{{ config('services.api.url') }}{{ $event->optimizedImageMediumUrl() }}">
                                    <img class="imagenSessionList"
                                        src="{{ config('services.api.url') }}{{ $event->optimizedImageLargeUrl() }}"
                                        alt="{{ $event->name }}" loading="lazy"
                                        onerror="this.onerror=null; this.src='https://picsum.photos/200'">
                                </picture>
                            @else
                                <img src="https://picsum.photos/2000" alt="{{ $session->event->name }}" loading="lazy">
                            @endif
                            <div class="sessionCont">
                                <p>Data: {{ \Carbon\Carbon::parse($session->date_time)->format('d/m/Y, H:i') }}</p>
                                <p>Ventas: {{ $session->sold_tickets }} / {{ $session->max_capacity }}</p>
                                <div class="statusSessionDiv">
                                    <p class="statusSessionText">Estat de la sessió: </p>
                                    <span
                                        class="statusSession @if ($session->date_time < now()) closed                                
                                    @else
                                        {{ $session->closed ? 'closed' : '' }} @endif">
                                        @if ($session->date_time < now())
                                            Sessió Finalitzada
                                        @else
                                            {{ $session->closed ? 'Tancada' : 'Oberta' }}
                                        @endif
                                    </span>
                                </div>
                                @if ($session->date_time < now())
                                @else
                                    <button type="button" class="toggle-session-btn statusSessionBtn"
                                        data-session-id="{{ $session->id }}"
                                        data-session-closed="{{ $session->closed ? 'true' : 'false' }}"
                                        id="openModalButton">
                                        {{ $session->closed ? 'Obrir la sessió' : 'Tancar la sessió' }}
                                    </button>
                                @endif
                                <div class="divBoton">
                                    <a class="card-price card-info card-sessions detailsBtn"
                                        href="{{ route('tickets.showevent', ['id' => $event->id]) }}">Detalls</a>
                                    <span class="card-price card-info card-sessions editBtnSession">Editar</span>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    <div id="confirmModal">
        <div class="modal-contenedor">
            <h2 class="modal-title">Segur que vols tancar la sessió?</h2>
            <p class="modal-content">Tancar la sessión desactivarà la venta online d'entrades,
                es pot tornar a obrir la sessió en qualsevol moment
            </p>
            <div class="botonesModal">
                <button id="cancelButton">Cancelar</button>
                <button id="confirmButton">Confirmar</button>
            </div>
        </div>
    </div>

    <div id="nueva-sesion-modal" class="modal">
        <div class="modal-content div-adreca" id="div-crear-sesion">
            <form class="nova-adreca" id="formularioSession"
                action="{{ route('promotorsessionslist.storeSession', ['id' => $event_id]) }}" method="POST">
                @csrf
                <h2>Nova Sessió</h2>
                <!-- Formulario para crear nova adreça -->

                @if ($primeraSesion)
                    <label class="labelSesion" for="data_sesion">Data i hora</label>
                    <input type="datetime-local" class="input-event input-adreca" name="data_sesion" id="nova_data"
                        value="{{ $primeraSesion->date_time }}">

                    <label class="labelSesion" for="max_capacity">Aforament màxim</label>
                    <input class="input-event input-adreca" type="number" name="max_capacity" id="max_capacity_session"
                        placeholder="Aforament màxim" oninput="vaciarEntradas()"
                        value="{{ $primeraSesion->max_capacity }}">
                @else
                    <label class="labelSesion" for="data_sesion">Data i hora</label>
                    <input type="datetime-local" class="input-event input-adreca" name="data_sesion" id="nova_data">

                    <label class="labelSesion" for="max_capacity">Aforament màxim</label>
                    <input class="input-event input-adreca" type="number" name="max_capacity" id="max_capacity_session"
                        placeholder="Aforament màxim" oninput="vaciarEntradas()">
                @endif

                <hr class="separador-entradas-sesion">

                <div class="div-event" id="entradas-sesion">

                    <div class="div-event ticket-type" id="entradas-container">
                        @if ($ticketsPrimeraSesion)
                            @foreach ($ticketsPrimeraSesion as $index => $ticket)
                                <div class="div-informacion-principal ticket-input" id="ticket-input">
                                    <label class="labelSesion" for="entry_type_name[]" id="labelNombreEntradasSesion">Nom
                                        del tipus d'entrada</label>
                                    <input type="text" class="input-event inputNombreSesion" name="entry_type_name[]"
                                        id="nombre-entradas-sesion-{{ $index }}" value="{{ $ticket->name }}"
                                        placeholder="Nom del tipus d'entrada">

                                    <label class="labelSesion" for="entry_type_price[]"
                                        id="labelNombrePrecioSesion">Preu</label>
                                    <input type="number" class="input-event" name="entry_type_price[]"
                                        placeholder="Preu" id="precio_entradas-{{ $index }}" step="0.01"
                                        value="{{ $ticket->price }}">

                                    <label class="labelSesion" for="entry_type_quantity[]"
                                        id="labelNombreCantidadSesion">Quantitat</label>
                                    <input type="number" class="input-event" name="entry_type_quantity[]"
                                        id="entry_type_quantity_sesion-{{ $index }}" placeholder="Quantitat"
                                        value="{{ $ticket->available_tickets }}" min="0"
                                        oninput="actualizarMaxEntradas()">

                                    <button type="button" class="eliminar-linea" id="eliminar-entrada-session"
                                        style="display: {{ $index === 0 ? 'none' : 'block' }}"
                                        onclick="eliminarEntrada(this)">Eliminar entrada</button>
                                    <hr class="separador-entradas-sesion">
                                </div>
                            @endforeach
                        @else
                            <div class="div-informacion-principal ticket-input" id="ticket-input">
                                <label class="labelSesion" for="entry_type_name[]" id="labelNombreEntradasSesion">Nom
                                    del tipus d'entrada</label>
                                <input type="text" class="input-event inputNombreSesion" name="entry_type_name[]"
                                    id="nombre-entradas-sesion-0" placeholder="Nom del tipus d'entrada">
                                <label class="labelSesion" for="entry_type_price[]"
                                    id="labelNombrePrecioSesion">Preu</label>
                                <input type="number" class="input-event" name="entry_type_price[]" placeholder="Preu"
                                    id="precio_entradas-0" step="0.01">
                                <label class="labelSesion" for="entry_type_quantity[]"
                                    id="labelNombreCantidadSesion">Quantitat</label>
                                <input type="number" class="input-event" name="entry_type_quantity[]"
                                    id="entry_type_quantity_sesion-0" placeholder="Quantitat" min="0"
                                    oninput="actualizarMaxEntradas()">

                                <button type="button" class="eliminar-linea" id="eliminar-entrada-session"
                                    style="display: none;" onclick="eliminarEntrada(this)">Eliminar entrada
                                </button>
                                <hr class="separador-entradas-sesion">
                            </div>
                        @endif
                    </div>

                    <button type="button" id="agregar-entrada" class="agregar-entrada" onclick="agregarEntrada()"><span
                            class="icono-plus">+</span><u>Afegir Entrada</u></button>
                </div>
                <div class="div-event div-tancament" id="div-event-sesion">
                    <div class="div-tancament2" id="cierre-entradas-sesion">
                        <label class="label-adreca label-categoria">
                            Tancament de la Venta Online:
                            <select name="selector-options-sesion" class="select-categoria-desktop"
                                id="selector-options-sesion">
                                <option value="1">Hora de l'esdeveniment</option>
                                <option value="2">1 hora abans</option>
                                <option value="3">2 hores abans</option>
                            </select>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="guardar-adreca">Guardar</button>
                <button type="button" class="btn btn-secondary" id="cerrar-modal-direccion">Tancar</button>

            </form>
        </div>
    </div>

    <div id="overlay" class="overlay"></div>

    <!-- Botón para abrir el modal -->

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.label-adreca').forEach(setupSelector);
        const modal = document.getElementById('confirmModal');
        const btn = document.getElementById('openModalButton');
        const cancelButton = document.getElementById('cancelButton');
        const confirmButton = document.getElementById('confirmButton');
        const fechaInput = document.getElementById('nova_data');
        const fechaActual = new Date().toISOString().slice(0, 16);


        if (document.getElementById('abrir-modal-sesion')) {
            document.getElementById('abrir-modal-sesion').addEventListener('click', function() {
                document.getElementById('overlay').style.display = 'block';
                document.getElementById('nueva-sesion-modal').style.display = 'block';
            });
        }
        document.getElementById('cerrar-modal-direccion').addEventListener('click', function() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('nueva-sesion-modal').style.display = 'none';
        });

        document.getElementById('overlay').addEventListener('click', function() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('nueva-sesion-modal').style.display = 'none';
        });

        function validarNumero(input) {
            const valor = parseFloat(input.value);

            if (valor < 0) {
                input.value = 0;
            }

        }

        function actualizarMaxEntradas() {
            const aforoMaximo = parseInt(document.getElementById("max_capacity_session").value);
            const entradasInputs = Array.from(document.querySelectorAll('[id^="' + 'entry_type_quantity_sesion' + '"]'));

            if (document.activeElement.value > parseInt(document.activeElement.max)) {
                document.activeElement.value = parseInt(document.activeElement.max)
            }
            document.activeElement.value = Math.ceil(document.activeElement.value);

            const sumaEntradas = entradasInputs.reduce((sum, input) => sum + (parseInt(input.value) || 0), 0);

            entradasInputs.forEach(input => {
                validarNumero(input);
                if (input !== document.activeElement) {
                    input.max = aforoMaximo - sumaEntradas + (parseInt(input.value) || 0);
                };

                if (parseInt(input.value) < 0) {
                    input.value = 0;
                }
            });
        }

        function vaciarEntradas() {
            const entradasInputs = Array.from(document.querySelectorAll('[id^="' + 'entry_type_quantity_sesion' + '"]'));

            entradasInputs.forEach(input => {
                input.value = 0;
            });

            actualizarMaxEntradas();
        }

        function cerrarModalDireccion() {
            document.querySelectorAll('.input-adreca').forEach(function(input) {
                input.value = "";
            });
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('nueva-sesion-modal').style.display = 'none';
        }

        function agregarEntrada() {
            const primerSeparador = document.querySelector('hr');
            const contenedor = document.getElementById('entradas-container');
            const primerTicketInput = contenedor.querySelector('.ticket-input');
            const nuevoTicketInput = primerTicketInput.cloneNode(true);

            nuevoTicketInput.querySelectorAll('input').forEach(function(input) {
                input.value = '';
                input.id = input.id + '-' + (contenedor.children.length + 1)
            });

            const separador = nuevoTicketInput.querySelector('hr');
            separador.classList.add('nueva-linea');
            const botonEliminar = nuevoTicketInput.querySelector('button');

            primerSeparador.style.display = 'block';

            separador.style.display = 'block';

            botonEliminar.style.display = 'block';

            contenedor.appendChild(nuevoTicketInput);

            actualizarMaxEntradas();
        }

        function eliminarEntrada(elemento) {
            const contenedor = document.getElementById('entradas-container');
            const divAEliminar = elemento.parentNode;

            if (divAEliminar !== contenedor.firstChild) {
                contenedor.removeChild(divAEliminar);
            }

            actualizarMaxEntradas();
        }


        function setupSelector(selector) {

            selector.addEventListener('mousedown', e => {

                e.preventDefault();

                const select = selector.children[0];
                const dropDown = document.createElement('ul');
                dropDown.className = "selector-options select-categoria-desktop ";

                [...select.children].forEach(option => {
                    const dropDownOption = document.createElement('li');
                    dropDownOption.textContent = option.textContent;

                    dropDownOption.addEventListener('mousedown', (e) => {
                        e.stopPropagation();
                        select.value = option.value;
                        selector.value = option.value;
                        select.dispatchEvent(new Event('change'));
                        selector.dispatchEvent(new Event('change'));
                        dropDown.remove();
                    });

                    dropDown.appendChild(dropDownOption);
                });

                selector.appendChild(dropDown);

                // handle click out
                document.addEventListener('click', (e) => {
                    if (!selector.contains(e.target)) {
                        dropDown.remove();
                    }
                });

            });
        }

        function addErrorMessage(field, message) {
            const errorMessageId = field.id + '-error';
            let errorMessage = document.getElementById(errorMessageId);

            if (errorMessage) {
                errorMessage.textContent = message;
            } else {
                errorMessage = document.createElement('span');
                errorMessage.id = errorMessageId;
                errorMessage.className = 'error-message';
                errorMessage.textContent = message;
                field.parentNode.insertBefore(errorMessage, field.nextSibling);
            }
        };

        function quitarResaltadoCampos(camposRequeridos) {

            camposRequeridos.forEach(campoId => {
                let campo = document.getElementById(campoId);
                if (campo) {
                    campo.style.border = "";
                }
            });
        }

        function resaltarCampoVacio(campo) {
            if (campo) {
                campo.style.border = "1px solid red";
            }
        }

        function resaltarCampos() {

            const contenedorCampos = document.getElementById('entradas-sesion')

            let camposRequeridos = ['nova_data', 'max_capacity_session'];

            let camposNombre = document.querySelectorAll('[id^="' + 'nombre-entradas-sesion' + '"]');
            let camposPrecio = document.querySelectorAll('[id^="' + 'precio_entradas' + '"]');
            let camposCantidad = document.querySelectorAll('[id^="' + 'entry_type_quantity_sesion' + '"]');

            camposNombre = Array.from(camposNombre);
            camposPrecio = Array.from(camposPrecio);
            camposCantidad = Array.from(camposCantidad);

            let campos = camposNombre.concat(camposPrecio, camposCantidad);

            camposRequeridos.forEach(campoId => {
                let campo = document.getElementById(campoId);
                if (!campos.includes(campo)) {
                    campos.push(campo);
                }
            });

            let campoVacioEncontrado = false;
            campos.forEach(campo => {
                if (campo.value === "") {
                    resaltarCampoVacio(campo);
                    campoVacioEncontrado = true;

                } else {
                    campo.style.border = "1px solid black";
                }
            });

            if (campoVacioEncontrado) {
                addErrorMessage(contenedorCampos, '* Revisa los campos marcados')
            }

            return campoVacioEncontrado;

        }

        document.getElementById("formularioSession").addEventListener("submit", function(e) {

            e.preventDefault();

            let camposRequeridos = ['nova_data', 'max_capacity_session'];

            let camposNombre = document.querySelectorAll('[id^="' + 'nombre-entradas-sesion' + '"]');
            let camposPrecio = document.querySelectorAll('[id^="' + 'precio_entradas' + '"]');
            let camposCantidad = document.querySelectorAll('[id^="' + 'entry_type_quantity_sesion' + '"]');

            camposNombre = Array.from(camposNombre);
            camposPrecio = Array.from(camposPrecio);
            camposCantidad = Array.from(camposCantidad);

            let campos = camposNombre.concat(camposPrecio, camposCantidad, camposRequeridos)

            let camposVacios = resaltarCampos();

            if (!camposVacios) {
                quitarResaltadoCampos(campos);
                this.submit();
            };

        });

        function toggleSession() {
            const sessionId = this.getAttribute('data-session-id');
            const closed = this.getAttribute('data-session-closed') === 'true';

            fetch(`/promotor/promotorsessionlist/update-session/${sessionId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        closed: !closed
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    this.setAttribute('data-session-closed', !closed);
                    this.textContent = !closed ? 'Obrir la sessió' : 'Tancar la sessió';

                    const statusSpan = this.parentElement.querySelector('.statusSession');
                    const codigoAcceso = this.parentElement.parentElement.querySelector('.codigo_acceso');
                    codigoAcceso.textContent = !closed ? "codi d'accés: " + data.session_code : "codi d'accés:";

                    statusSpan.textContent = !closed ? 'Tancada' : 'Oberta';
                    if (!closed) {
                        statusSpan.classList.add('closed');
                    } else {
                        statusSpan.classList.remove('closed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error.message);
                    showToast("Error al canviar l'estat de la sessió");
                });
        }

        function showModal(button) {
            modal.style.display = "block";
            confirmButton.onclick = function() {
                modal.style.display = "none";
                toggleSession.call(button);
            }
        }

        function handleToggleSession(button) {
            if (button.getAttribute('data-session-closed') === 'true') {
                toggleSession.call(button);
            } else {
                showModal(button);
            }
        }

        document.querySelectorAll('.toggle-session-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                handleToggleSession(button);
            });
        });

        cancelButton.onclick = function() {
            modal.style.display = "none";
        };

        document.addEventListener('DOMContentLoaded', function() {

            fechaInput.setAttribute('min', fechaActual);
        });
    </script>
@endpush
