<!-- resources/views/tickets/createEvent.blade.php -->
@extends('layouts.app')

@section('content')
    <h2 class="titol-crear-event">Crear Esdeveniment</h2>

    <form class="event-create" method="post" action="{{ route('promotor.storeEvent') }}" enctype="multipart/form-data">
        @csrf

        <h3 class="h3-event">Informacio Principal</h3>

        <div class="div-event">
            <!-- Títol de l’esdeveniment -->
            <div class="div-informacion-principal">
                <div class="div-formulario-evento">
                    <input type="text" class="input-event input-infomacion-principal" name="title"
                        placeholder="Títol de l’esdeveniment" required>
                </div>

                <div class="div-formulario-evento">
                    <label class="label-adreca label-categoria">
                        Selecciona una categoria
                        <select name="selector-options-categoria" class="select-categoria-desktop">
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>

            <!-- Descripció de l’esdeveniment -->
            <div class="div-textarea">
                <textarea class="textarea-event" name="description" id="description" rows="4" placeholder="Descripció" required></textarea>
            </div>

            <div class="div-informacion-principal">
                <div class="div-data-image div-data">
                    <!-- Data i hora de la celebració -->
                    <label for="event_datetime" class="label-event">Data i Hora</label>
                    <input type="datetime-local" class="input-event date-input" name="event_datetime" id="event_datetime"
                        required>
                </div>
                <!-- Imatge principal de l’esdeveniment -->
                <div class="div-data-image">
                    <label for="event_image" class="label-event">Imatge Principal de l’esdeveniment:</label>
                    <input type="file" class="input-event input-image-event" name="event_image" id="event_image"
                        accept="image/*" required>
                </div>

            </div>
        </div>

        <h3 class="h3-event">Adreça</h3>
        <div class="div-event div-adreca">
            <label class="label-adreca" style="display: {{ $existingAddresses->count() > 0 ? 'block' : 'none' }}" id="label-adreca">
                Selecciona una adreça
                <select name="selector-options" class="select-categoria-desktop"  id="adreces-select">
                    @foreach ($existingAddresses as $direccion)
                        <option value="{{ $direccion->id }}">
                            {{ $direccion->venue_name }}, {{ $direccion->city }}, {{ $direccion->province }},
                            {{ $direccion->postal_code }}, Capacitat del local: {{ $direccion->capacity }}
                        </option>
                    @endforeach
                </select>
            </label>
            <!-- Botón para abrir el modal de Nova Adreça -->
            <button type="button" class="btn-form" id="abrir-modal-direccion">
                Afegir nova adreça
            </button>
        </div>


        <h3 class="h3-event">Dades adicionals</h3>

        <div class="div-event">

            <div class="div-informacion-principal">
                <!-- Aforament màxim -->
                <input type="number" class="input-event" name="max_capacity" id="max_capacity"
                    placeholder="Aforament Màxim" oninput="actualizarMaxEntradas()">

                <!-- Enllaç a vídeo promocional -->
                <input type="url" class="input-event" name="promo_video_link" id="promo_video_link"
                    placeholder="Enllaç a Vídeo Promocional">
            </div>


            <div class="div-additional-images">
                <label for="additional_images" class="label-event">Imatges Adicionals:</label>
                <input type="file" class="input-event" name="additional_images[]" id="additional_images" accept="image/*"
                    multiple>
            </div>
        </div>

        <!-- Tipus d'entrades -->
        <h3 class="h3-event">Informació Entrades</h3>
        <div class="div-event">
            <div class="div-event ticket-type" id="entradas-container">
                <div class="div-informacion-principal ticket-input">
                    <input type="text" class="input-event" name="entry_type_name[]" required
                        placeholder="Nom del tipus d'entrada">

                    <input type="number" class="input-event" name="entry_type_price[]" placeholder="Preu" step="0.01"
                        required>

                    <input type="number" class="input-event" name="entry_type_quantity[]" id="entry_type_quantity" placeholder="Quantitat" required
                        min="0" oninput="actualizarMaxEntradas()">
                    <button type="button" class="eliminar-linea" style="display: none;"
                        onclick="eliminarEntrada(this)">Eliminar</button>

                    <hr class="separador-entradas">
                </div>
            </div>

            <button type="button" id="agregar-entrada" class="agregar-entrada" onclick="agregarEntrada()"><span
                    class="icono-plus">+</span><u>Afegir Entrada</u></button>
        </div>
        <div class="div-event div-tancament">
            <div class="div-tancament2">
                <label class="label-adreca label-categoria">
                    Tancament de la Venta Online:
                    <select name="selector-options-venue" class="select-categoria-desktop">
                        <option value="1">Hora de l'esdeveniment</option>
                        <option value="2">1 hora abans</option>
                        <option value="3">2 hores abans</option>
                    </select>
                </label>
            </div>
        </div>

        <div class="div-event extraInfoContainer">
            <div class="primaryDetail">
                <label for="event_hidden" class="switch">Esdeveniment Ocult:
                    <input type="hidden" name="event_hidden" value="0">
                    <input type="checkbox" class="input-event" name="event_hidden" id="event_hidden" value="1">
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="primaryDetail secondaryDetail">
                <label for="nominal_entries" class="switch">Entrades Nominals:
                    <input type="checkbox" class="input-event" name="nominal_entries" id="nominal_entries">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn-form btn-guardar">Crear Esdeveniment</button>
    </form>

    <!-- Modal para Nova Adreça -->
    <div id="nueva-direccion-modal" class="modal">
        <div class="modal-content div-adreca">
            <span class="close" onclick="cerrarModalDireccion()">&times;</span>
            <form class="nova-adreca" id="formularioVenue" action="" method="POST">
                @csrf
                <h2>Nova Adreça</h2>
                <!-- Formulario para crear nova adreça -->
                <input class="input-event input-adreca" type="text" name="nova_provincia" id="nova_provincia"
                    placeholder="Provincia" required>

                <input class="input-event input-adreca" type="text" name="nova_ciutat" id="nova_ciutat"
                    placeholder="Ciutat" required>

                <input class="input-event input-adreca" type="number" name="codi_postal" id="codi_postal"
                    placeholder="Codi Postal"required>

                <input class="input-event input-adreca" type="text" name="nom_local" id="nom_local"
                    placeholder="Nom del local" required>

                <input class="input-event input-adreca" type="number" name="capacitat_local" id="capacitat_local"
                    placeholder="Capacitat del local" required>

                <button type="button" class="btn btn-primary" id="guardar-adreca"
                    onclick="guardarNovaAdreca()">Guardar</button>
                <button type="button" class="btn btn-secondary" id="cerrar-modal-direccion">Tancar</button>
            </form>
        </div>
    </div>

    <div id="overlay" class="overlay" onclick="cerrarModalDireccion()"></div>

    <script>
        document.querySelectorAll('.label-adreca').forEach(setupSelector);

        document.getElementById('abrir-modal-direccion').addEventListener('click', function() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('nueva-direccion-modal').style.display = 'block';
        });

        document.getElementById('cerrar-modal-direccion').addEventListener('click', function() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('nueva-direccion-modal').style.display = 'none';
        });

        function guardarNovaAdreca() {

            let camposRequeridos = ['nova_provincia', 'nova_ciutat', 'codi_postal', 'nom_local', 'capacitat_local'];
            const contenedorAdreca = document.getElementById('label-adreca');

            // Función para resaltar campo vacío
            function resaltarCampoVacio(campo) {
                campo.style.border = "1px solid red";
            }

            // Función para quitar resaltado de campos
            function quitarResaltadoCampos() {
                camposRequeridos.forEach(campoId => {
                    let campo = document.getElementById(campoId);
                    campo.style.border = "";
                });
            }

            // Validación de campos requeridos
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

            if (!campoVacioEncontrado) {
                quitarResaltadoCampos();

                const formData = new FormData(document.getElementById("formularioVenue"));
                fetch("{{ route('promotor.createVenue') }}", {
                        method: "POST",
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.addresses) {
                            const select = document.querySelector('select[name="selector-options"]');
                            select.innerHTML = "";

                            data.addresses.forEach(direccion => {
                                const option = document.createElement("option");
                                option.value = direccion.id;
                                option.text =
                                    `${direccion.venue_name}, ${direccion.city}, ${direccion.province}, ${direccion.postal_code}, Capacitat del local: ${ $direccion->capacity }`;
                                select.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });

                    contenedorAdreca.style.display = 'block';

                cerrarModalDireccion();

            };
        }

        function cerrarModalDireccion() {
            document.querySelectorAll('.input-adreca').forEach(function(input) {
                input.value = "";
            });
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('nueva-direccion-modal').style.display = 'none';
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

        function validarNumero(input) {
            const valor = parseFloat(input.value);

            if (valor < 0) {
                input.value = 0;
            }

        }

        function actualizarMaxEntradas() {
            const aforoMaximo = parseInt(document.getElementById("max_capacity").value);
            const entradasInputs = Array.from(document.querySelectorAll("#entry_type_quantity"));

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
            });
        }

        function agregarEntrada() {
            const primerSeparador = document.querySelector('hr');
            const contenedor = document.getElementById('entradas-container');
            const primerTicketInput = contenedor.querySelector('.ticket-input');
            const nuevoTicketInput = primerTicketInput.cloneNode(true);

            nuevoTicketInput.querySelectorAll('input').forEach(function(input) {
                input.value = '';
            });

            const separador = nuevoTicketInput.querySelector('hr')
            const botonEliminar = nuevoTicketInput.querySelector('button');

            primerSeparador.style.display = 'block';

            separador.style.display = 'block';

            botonEliminar.style.display = 'block';

            if (window.innerWidth > 768) {
                primerSeparador.style.display = 'none'
                separador.style.display = 'none';
            }

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
    </script>
@endsection
