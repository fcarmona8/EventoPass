<!-- resources/views/tickets/createEvent.blade.php -->
@extends('layouts.app')

@section('content')
    <h2 class="titol-crear-event">Crear Esdeveniment</h2>

    <form class="event-create" method="post" action="{{ route('event.store') }}" enctype="multipart/form-data">
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
                    <input type="text" class="input-event input-infomacion-principal" name="category" id="category"
                        placeholder="Categoria" required>
                </div>
                <!-- Categoria -->


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
            <label class="label-adreca">
                Selecciona una adreça
                <select name="selector-options">
                    <option value="1">Adreca 1</option>
                    <option value="2">Adreca 2</option>
                    <option value="3">Adreca 3</option>
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



            <!-- Imatges addicionals -->
            <label for="additional_images" class="label-event">Imatges Adicionals:</label>
            <input type="file" class="input-event" name="additional_images[]" id="additional_images" accept="image/*"
                multiple>
        </div>

        <!-- Tipus d'entrades -->
        <h3 class="h3-event">Informació Entrades</h3>
        <div class="div-event">
            <div class="div-event ticket-type" id="entradas-container">
                <div class="div-informacion-principal ticket-input">
                    <input type="text" class="input-event" name="entry_type_name" id="entry_type_name" required
                        placeholder="Nom del tipus d'entrada">

                    <input type="number" class="input-event" name="entry_type_price" id="entry_type_price"
                        placeholder="Preu" step="0.01" required>

                    <input type="number" class="input-event" name="entry_type_quantity" id="entry_type_quantity"
                        placeholder="Quantitat" required min="0" oninput="actualizarMaxEntradas()">
                    <button type="button" class="eliminar-linea" style="display: none;"
                        onclick="eliminarEntrada(this)">Eliminar</button>

                    <hr class="separador-entradas">
                </div>
            </div>

            <button type="button" id="agregar-entrada" class="agregar-entrada" onclick="agregarEntrada()"><span
                    class="icono-plus">+</span><u>Afegir Entrada</u></button>

            <!-- Afegir més tipus d'entrades -->

            <!-- Tancament de la venta online -->
            <label for="online_sales_closure">Tancament de la Venta Online:</label>
            <select name="online_sales_closure" id="online_sales_closure">
                <option value="event_time">A l'hora de l'esdeveniment</option>
                <option value="1_hour_before">1 hora abans</option>


                <!-- Otras opciones -->
            </select>
            Esdeveniment ocult
            <label for="event_hidden">Esdeveniment Ocult:</label>
            <input type="checkbox" class="input-event" name="event_hidden" id="event_hidden">
            Entrades nominals
            <label for="nominal_entries">Entrades Nominals:</label>
            <input type="checkbox" class="input-event" name="nominal_entries" id="nominal_entries">
        </div>

        <button type="submit" class="btn-form btn-guardar">Crear Esdeveniment</button>
    </form>

    <!-- Modal para Nova Adreça -->
    <div id="nueva-direccion-modal" class="modal">
        <div class="modal-content div-adreca">
            <span class="close" onclick="cerrarModalDireccion()">&times;</span>
            <form class="nova-adreca" action="">
                @csrf
                <h2>Nova Adreça</h2>
                <!-- Formulario para crear nova adreça -->
                <input class="input-event" type="text" name="nova_provincia" id="nova_provincia"
                    placeholder="Provincia">

                <input class="input-event" type="text" name="nova_ciutat" id="nova_ciutat" placeholder="Ciutat">

                <input class="input-event" type="number" name="codi_postal" id="codi_postal"
                    placeholder="Codi Postal">

                <input class="input-event" type="text" name="nom_local" id="nom_local" placeholder="Nom del local">

                <input class="input-event" type="text" name="capacitat_local" id="capacitat_local"
                    placeholder="Capacitat del local">

                <button type="button" class="btn btn-primary" id="guardar-adreca"
                    onclick="guardarNovaAdreca()">Guardar</button>
                <button type="button" class="btn btn-secondary" id="cerrar-modal-direccion">Tancar</button>
            </form>
        </div>
    </div>

    <div id="overlay" class="overlay" onclick="cerrarModalDireccion()"></div>

    <script>
        // Evento al hacer clic en el botón para abrir el modal de Nova Adreça

        document.querySelectorAll('.label-adreca').forEach(setupSelector);

        document.getElementById('abrir-modal-direccion').addEventListener('click', function() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('nueva-direccion-modal').style.display = 'block';
        });

        document.getElementById('cerrar-modal-direccion').addEventListener('click', function() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('nueva-direccion-modal').style.display = 'none';
        });

        function cerrarModalDireccion() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('nueva-direccion-modal').style.display = 'none';
        }

        function setupSelector(selector) {
            selector.addEventListener('change', e => {
                console.log('changed', e.target.value)
            })

            selector.addEventListener('mousedown', e => {

                e.preventDefault();

                const select = selector.children[0];
                const dropDown = document.createElement('ul');
                dropDown.className = "selector-options";

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

        function agregarEntrada() {
            var primerSeparador = document.querySelector('hr')
            var contenedor = document.getElementById('entradas-container');
            var nuevoEntrada = document.querySelector('.ticket-input').cloneNode(true);

            // Limpiar los valores de los campos clonados
            nuevoEntrada.querySelectorAll('input').forEach(function(input) {
                input.value = '';
            });
            var separador = nuevoEntrada.querySelector('hr')
            var botonEliminar = nuevoEntrada.querySelector('button');

            primerSeparador.style.display = 'block'

            separador.style.display = 'block'

            botonEliminar.style.display = 'block';

            if (window.innerWidth > 768) {
                primerSeparador.style.display = 'none'
                separador.style.display = 'none';
            }

            // Agregar el nuevo div al contenedor
            contenedor.appendChild(nuevoEntrada);


            actualizarMaxEntradas();


        }

        function eliminarEntrada(elemento) {
            var contenedor = document.getElementById('entradas-container');
            var divAEliminar = elemento.parentNode;

            // Asegurarse de que no se elimine el primer conjunto
            if (divAEliminar.previousElementSibling !== null) {
                contenedor.removeChild(divAEliminar);
            }
        }

        function actualizarMaxEntradas() {
            const aforoMaximo = parseInt(document.getElementById("max_capacity").value);
            const entradasInputs = Array.from(document.querySelectorAll("#entry_type_quantity"));

            // Calcular la suma de las entradas actuales
            const sumaEntradas = entradasInputs.reduce((sum, input) => sum + (parseInt(input.value) || 0), 0);

            // Actualizar el atributo max de cada campo de entradas
            entradasInputs.forEach(input => {
                if (input !== document.activeElement) {
                    input.max = aforoMaximo - sumaEntradas + (parseInt(input.value) || 0);
                }
            });

            if (document.activeElement.value > parseInt(document.activeElement.max)) {
                document.activeElement.value = parseInt(document.activeElement.max)
            }
        }
    </script>
@endsection
