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
                    <label for="title" class="label-event">Títol de l’esdeveniment</label>
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
                <label for="description" class="label-event">Descripció</label>
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
                    accept="image/jpeg, image/png, image/jpg" required>
                </div>

            </div>
        </div>

        <h3 class="h3-event">Adreça</h3>
        <div class="div-event div-adreca">
            <label class="label-adreca" style="display: {{ $existingAddresses->count() > 0 ? 'block' : 'none' }}"
                id="label-adreca">
                Selecciona una adreça
                <select name="selector-options" class="select-categoria-desktop" id="adreces-select">
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
                <div class="divMaxCapacityVideo">
                    <label for="max_capacity" class="label-event">Aforament màxim</label>
                    <input type="number" class="input-event" name="max_capacity" id="max_capacity"
                        placeholder="Aforament Màxim" oninput="vaciarEntradas()">
                </div>
                <!-- Enllaç a vídeo promocional -->
                <div class="divMaxCapacityVideo">
                    <label for="promo_video_link" class="label-event">Enllaç al video promocional</label>
                    <input type="url" class="input-event" name="promo_video_link" id="promo_video_link"
                        placeholder="Enllaç a Vídeo Promocional">
                </div>
            </div>


            <div class="div-additional-images">
                <label for="additional_images" class="label-event">Imatges Adicionals:</label>
                <input type="file" class="input-event" name="additional_images[]" id="additional_images"
                    accept="image/jpeg, image/png, image/jpg" multiple>
                <ul id="preview"></ul>
            </div>
        </div>

        <!-- Tipus d'entrades -->
        <h3 class="h3-event">Informació Entrades</h3>
        <div class="div-event">
            <div class="div-event ticket-type" id="entradas-container">
                <div class="div-informacion-principal ticket-input">
                    <div class="titulosEntradas">
                        <label class="label-event">Tipus entrada</label>
                        <input type="text" class="input-event" name="entry_type_name[]" required
                            placeholder="Nom del tipus d'entrada">
                    </div>
                    <div class="titulosEntradas">
                        <label class="label-event">Preu entrada</label>
                        <input type="number" class="input-event" name="entry_type_price[]" placeholder="Preu"
                            step="0.01" required>
                    </div>
                    <div class="titulosEntradas">
                        <label class="label-event">Quantitat</label>
                        <input type="number" class="input-event" name="entry_type_quantity[]" id="entry_type_quantity"
                            placeholder="Quantitat" required min="0" oninput="actualizarMaxEntradas()">
                    </div>

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
                    <input type="hidden" name="nominal_entries" value="0">
                    <input type="checkbox" class="input-event" name="nominal_entries" id="nominal_entries"
                        value="1">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn-form btn-guardar">Crear Esdeveniment</button>
    </form>

    <!-- Modal para Nova Adreça -->
    <div id="nueva-direccion-modal" class="modal">
        <div class="modal-content div-adreca">
            <form class="nova-adreca" id="formularioVenue" action="" method="POST">
                @csrf
                <h2>Nova Adreça</h2>
                <!-- Formulario para crear nova adreça -->
                <label class="labelDireccion" for="nova_provincia">Provincia:</label>
                <input class="input-event input-adreca" type="text" name="nova_provincia" id="nova_provincia"
                    placeholder="Provincia" required>

                <label class="labelDireccion" for="nova_ciutat">Ciutat:</label>
                <input class="input-event input-adreca" type="text" name="nova_ciutat" id="nova_ciutat"
                    placeholder="Ciutat" required>

                <label class="labelDireccion" for="codi_postal">Codi Postal:</label>
                <input class="input-event input-adreca" type="number" name="codi_postal" id="codi_postal"
                    placeholder="Codi Postal"required>

                <label class="labelDireccion" for="nom_local">Nom del local:</label>
                <input class="input-event input-adreca" type="text" name="nom_local" id="nom_local"
                    placeholder="Nom del local" required>

                <label class="labelDireccion" class="labelDireccion"l for="capacitat_local">Capacitat del local:</label>
                <input class="input-event input-adreca" type="number" name="capacitat_local" id="capacitat_local"
                    placeholder="Capacitat del local" required>

                <button type="button" class="btn btn-primary" id="guardar-adreca"
                    onclick="guardarNovaAdreca()">Guardar</button>
                <button type="button" class="btn btn-secondary" id="cerrar-modal-direccion">Tancar</button>
            </form>
        </div>
    </div>

    @if ($errors->has('error'))
        <div id="error-message" data-error="{{ $errors->first('error') }}" style="display: none;"></div>
    @endif

    <div id="toastBox"></div>

    <div id="overlay" class="overlay" onclick="cerrarModalDireccion()"></div>
@endsection

@push('scripts')
    <script>
        document.getElementById('abrir-modal-direccion').addEventListener('click', function() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('nueva-direccion-modal').style.display = 'block';
        });

        document.getElementById('cerrar-modal-direccion').addEventListener('click', function() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('nueva-direccion-modal').style.display = 'none';
        });

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

        function vaciarEntradas() {
            const entradasInputs = Array.from(document.querySelectorAll("#entry_type_quantity"));

            entradasInputs.forEach(input => {
                input.value = 0;
            });

            actualizarMaxEntradas();
        }

        function setupSelector(selector) {

            selector.addEventListener('mousedown', e => {

                e.preventDefault();

                const select = selector.children[0];
                const dropDown = document.createElement('ul');
                dropDown.className = "selector-options select-categoria-desktop ";

                if (selector.querySelector('#adreces-select')) {
                    dropDown.classList.add("select-direcciones");
                }

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

        document.querySelectorAll('.label-adreca').forEach(setupSelector);

        function agregarEntrada() {
            const primerSeparador = document.querySelector('hr');
            const contenedor = document.getElementById('entradas-container');
            const primerTicketInput = contenedor.querySelector('.ticket-input');
            const nuevoTicketInput = primerTicketInput.cloneNode(true);

            nuevoTicketInput.querySelectorAll('input').forEach(function(input) {
                input.value = '';
            });
            primerSeparador.classList.add('primer-separador')
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

        function cerrarModalDireccion() {
            document.querySelectorAll('.input-adreca').forEach(function(input) {
                input.value = "";
            });
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('nueva-direccion-modal').style.display = 'none';
        }

        function quitarResaltadoCampos() {
            let camposRequeridos = ['nova_provincia', 'nova_ciutat', 'codi_postal', 'nom_local', 'capacitat_local'];
            camposRequeridos.forEach(campoId => {
                let campo = document.getElementById(campoId);
                campo.style.border = "";
            });
        }

        function guardarNovaAdreca() {

            let camposRequeridos = ['nova_provincia', 'nova_ciutat', 'codi_postal', 'nom_local', 'capacitat_local'];
            const contenedorAdreca = document.getElementById('label-adreca');

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
                            const select = document.getElementById('adreces-select');
                            select.innerHTML = "";

                            data.addresses.forEach(direccion => {
                                const option = document.createElement("option");
                                option.value = direccion.id;
                                option.text =
                                    `${direccion.venue_name}, ${direccion.city}, ${direccion.province}, ${direccion.postal_code}, Capacitat del local: ${direccion.capacity }`;
                                select.appendChild(option);
                            });
                        }

                        showToast('Direcció creada correctament');

                    })
                    .catch(error => {
                        console.error(error);
                        showToast('Error al crear la direcció');
                    });

                contenedorAdreca.style.display = 'block';

                cerrarModalDireccion();

            };



        }

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('error-message');
            const fechaInput = document.getElementById('event_datetime');
            const fechaActual = new Date().toISOString().slice(0, 16);

            if (successMessage) {
                showToast("Error al crear l'esdeveniment");
            }

            fechaInput.setAttribute('min', fechaActual);
        });
    </script>
@endpush
