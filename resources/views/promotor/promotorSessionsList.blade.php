@extends('layouts.app')

@section('content')
    @if ($isSpecificEvent)
        <button type="button" class="btn btn-primary btnSessions" id="abrir-modal-sesion">Crear Nueva Sesión</button>
        <div class="listSessions" id="listSessions">
            @foreach ($sessions as $session)
                <div class="card cardHomePromotor">
                    @if ($session->event->main_image_id)
                        <img src="{{ asset('storage/' . $session->event->main_image) }}" alt="{{ $session->event->name }}"
                            onerror="this.onerror=null; this.src='https://picsum.photos/200'" loading="lazy">
                    @else
                        <img src="https://picsum.photos/2000" alt="{{ $session->event->name }}" loading="lazy">
                    @endif
                    <div class="sessionCont">
                        <p>Data: {{ \Carbon\Carbon::parse($session->date_time)->format('Y-m-d, H:i') }}</p>
                        <p>Ventas: {{ $session->sold_tickets }} / {{ $session->max_capacity }}</p>
                        <div class="divBoton">
                            <span class="card-price card-info card-sessions">Detalls</span>
                            <span class="card-price card-info card-sessions">Editar</span>
                            <span class="card-price card-info card-sessions">Entrades</span>
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
                                <img src="{{ asset('storage/' . $session->event->main_image) }}"
                                    alt="{{ $session->event->name }}"
                                    onerror="this.onerror=null; this.src='https://picsum.photos/200'" loading="lazy">
                            @else
                                <img src="https://picsum.photos/2000" alt="{{ $session->event->name }}" loading="lazy">
                            @endif
                            <div class="sessionCont">
                                <p>Data: {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y, H:i') }}</p>
                                <p>Ventas: {{ $session->sold_tickets }} / {{ $session->max_capacity }}</p>
                                <div class="divBoton">
                                    <span class="card-price card-info card-sessions">Detalls</span>
                                    <span class="card-price card-info card-sessions">Editar</span>
                                    <span class="card-price card-info card-sessions">Entrades</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    <div id="nueva-sesion-modal" class="modal">
        <div class="modal-content div-adreca" id="div-crear-sesion">
            <form class="nova-adreca" id="formularioSession"
                action="{{ route('promotorsessionslist.storeSession', ['id' => $event_id]) }}" method="POST">
                @csrf
                <h2>Nova Sessió</h2>
                <!-- Formulario para crear nova adreça -->

                @if ($primeraSesion)
                    <input type="datetime-local" class="input-event input-adreca" name="data_sesion" id="nova_data"
                        value="{{ $primeraSesion->date_time }}" required>

                    <input class="input-event input-adreca" type="number" name="max_capacity" id="max_capacity_session"
                        placeholder="Aforament màxim" oninput="vaciarEntradas()" value="{{ $primeraSesion->max_capacity }}"
                        required>
                @endif

                <hr class="separador-entradas-sesion">

                <div class="div-event" id="entradas-sesion">

                    <div class="div-event ticket-type" id="entradas-container">
                        @if ($ticketsPrimeraSesion)
                            @foreach ($ticketsPrimeraSesion as $index => $ticket)
                                <div class="div-informacion-principal ticket-input" id="ticket-input">
                                    <input type="text" class="input-event" name="entry_type_name[]"
                                        id="nombre-entradas-sesion" required value="{{ $ticket->name }}"
                                        placeholder="Nom del tipus d'entrada">

                                    <input type="number" class="input-event" name="entry_type_price[]" placeholder="Preu"
                                        id="precio_entradas" step="0.01" value="{{ $ticket->price }}" required>

                                    <input type="number" class="input-event" name="entry_type_quantity[]"
                                        id="entry_type_quantity_sesion" placeholder="Quantitat"
                                        value="{{ $ticket->available_tickets }}" required min="0"
                                        oninput="actualizarMaxEntradas()">

                                    <button type="button" class="eliminar-linea" id="eliminar-entrada-session"
                                        style="display: {{ $index === 0 ? 'none' : 'block' }}"
                                        onclick="eliminarEntrada(this)">Eliminar entrada</button>
                                    <hr class="separador-entradas-sesion">
                                </div>
                            @endforeach
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
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.label-adreca').forEach(setupSelector);

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
            const entradasInputs = Array.from(document.querySelectorAll("#entry_type_quantity_sesion"));

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
            const entradasInputs = Array.from(document.querySelectorAll("#entry_type_quantity_sesion"));

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

        function quitarResaltadoCampos() {
            let camposRequeridos = ['nova_data', 'max_capacity_session', 'precio_entradas', 'nombre-entradas-sesion',
                'entry_type_quantity_sesion'
            ];

            camposRequeridos.forEach(campoId => {
                let campo = document.getElementById(campoId);
                campo.style.border = "";
            });
        }

        function resaltarCampoVacio(campo) {
                campo.style.border = "1px solid red";
            }

        function resaltarCampos() {
            let camposRequeridos = ['nova_data', 'max_capacity_session', 'precio_entradas', 'nombre-entradas-sesion',
                'entry_type_quantity_sesion'
            ];            

            let campoVacioEncontrado = false;
            camposRequeridos.forEach(campoId => {
                let campo = document.getElementById(campoId);
                if (campo.value === "") {
                    resaltarCampoVacio(campo);
                    campoVacioEncontrado = true;
                    
                } else {
                    campo.style.border = "1px solid black";
                }
            });

            console.log(campoVacioEncontrado);
            return campoVacioEncontrado;
            
        }

        document.getElementById("formularioSession").addEventListener("submit", function(e) {

            e.preventDefault();

            let camposVacios = resaltarCampos();

            if (!camposVacios) {
                quitarResaltadoCampos();
                this.submit();
            };

        });
    </script>
@endpush
