@extends('layouts.app')

@section('content')
    <div class="homepromotor">
        @foreach ($events as $event)
            <div class="card cardHomePromotor" id="event-card-{{ $event->id }}">
                @if ($event->main_image)
                    <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->name }}"
                        id="event-image-{{ $event->id }}">
                @endif
                <div class="card-content">
                    <h3 id="event-name-{{ $event->id }}">{{ Str::limit($event->name, $limit = 55, $end = '...') }}</h3>
                    <p id="event-description-{{ $event->id }}" class="description">{{ $event->description }}</p>
                    <p>Proxima data: <span
                            id="event-date-{{ $event->id }}">{{ \Carbon\Carbon::parse($event->event_date)->format('Y-M-D , H:i') }}</span>
                    </p>
                    <p>Proxima ubicació: <span id="event-location-{{ $event->id }}">{{ $event->venue->city }},
                            {{ $event->venue->venue_name }}</span></p>
                    <div class="divBotones">
                        <span class="card-editEvent" eventId="{{ $event->id }}" eventName="{{ $event->name }}"
                            eventDesc="{{ $event->description }}" eventAddress="{{ $event->venue->id }}"
                            eventVid="{{ $event->video_link }}" eventHidden="{{ $event->hidden }}">Editar event</span>
                        <a class="card-link" href="{{ route('promotorsessionslist', ['id' => $event->id]) }}">
                            <span class="card-price card-info">Sessións</span>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal para editar evento -->
    <div class="modal" tabindex="-1" role="dialog" id="editEventModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Editar event</h2>
                    <button type="button" class="close cerrar" onclick="closeModal()" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formularioEditEvent">
                    @csrf
                    <div class="modal-body">
                        <input type="text" id="eventId" name="eventId" hidden>

                        <label class="labelHomePromotor" for="eventName">Nom del event:</label>
                        <input class="inputEditEvent" type="text" id="eventName" name="eventName" class="form-control"
                            placeholder="Ingresa el nom del event">

                        <label class="labelHomePromotor" for="eventDesc">Descripció del event:</label>
                        <textarea id="eventDesc" class="form-control" name="eventDesc" placeholder="Ingresa la descripció del event"></textarea>

                        <label class="labelHomePromotor" for="eventAddress"> Adreça:</label>
                        <select name="eventAddress" class="select-categoria-desktop" name="eventAddress" id="eventAddress">
                            @foreach ($existingAddresses as $direccion)
                                <option value="{{ $direccion->id }}">
                                    {{ $direccion->venue_name }}, {{ $direccion->city }}, {{ $direccion->province }},
                                    {{ $direccion->postal_code }}, {{ $direccion->capacity }}
                                </option>
                            @endforeach
                        </select>

                        <label class="labelHomePromotor">Foto del event:</label>
                        <input class="inputEditEvent" type="file" id="eventPhoto" name="eventPhoto" class="form-control"
                            placeholder="Ingresa la foto del event">

                        <label class="labelHomePromotor" for="eventVid">Video del event:</label>
                        <input class="inputEditEvent" type="text" id="eventVid" name="eventVid" class="form-control"
                            placeholder="Ingresa el video del event">

                            <label for="eventHidden" class="switch">Ocult:
                                <input type="hidden" name="eventHidden" value="0">
                                <input type="checkbox" class="input-event" name="eventHidden" id="eventHidden" value="1">
                                <span class="slider round"></span>
                            </label>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="saveEvent()">Guardar</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="success-message" class="custom-alert" style="display:none;">
        <p>{{ Session::get('success_message') }}</p>
        <span class="close-btn" onclick="closeAlert()">Cerrar</span>
    </div>

    {{ $events->links('vendor.pagination.bootstrap-4') }}
@endsection

@push('scripts')
    <script>
        // Función para abrir el modal al hacer clic en "Editar evento"
        document.querySelectorAll('.card-editEvent').forEach(function(element) {
            element.addEventListener('click', function() {
                var eventName = this.getAttribute('eventName');
                var eventDesc = this.getAttribute('eventDesc');
                var eventAddress = this.getAttribute('eventAddress');
                var eventVid = this.getAttribute('eventVid');
                var eventId = this.getAttribute('eventId');
                var eventHidden = this.getAttribute('eventHidden');
                openEditEventModal(eventName, eventDesc, eventAddress, eventVid, eventId, eventHidden);
            });
        });

        // Función para abrir el modal y prellenar el nombre del evento si es necesario
        function openEditEventModal(eventName, eventDesc, eventAddress, eventVid, eventId, eventHidden) {
            
            var eventNameInput = document.getElementById('eventName');
            var eventDescInput = document.getElementById('eventDesc');
            var eventAddressInput = document.getElementById('eventAddress');
            var eventVidInput = document.getElementById('eventVid');
            var eventIdInput = document.getElementById('eventId');
            var eventHiddenInput = document.getElementById('eventHidden');

            eventNameInput.value = eventName;
            eventDescInput.value = eventDesc;
            eventAddressInput.value = eventAddress;
            eventVidInput.value = eventVid;
            eventIdInput.value = eventId;
            eventHiddenInput.checked = eventHidden == 1;

            document.getElementById('editEventModal').style.display = 'block';
        }

        // Función para cerrar el modal
        function closeModal() {
            document.getElementById('editEventModal').style.display = 'none';
        }

        function saveEvent() {

            var camposObligatorios = ['eventName', 'eventDesc', 'eventAddress', 'eventVid'];

            // Función para resaltar campo vacío
            function resaltarCampoVacio(campo) {
                campo.style.border = "1px solid red";
            }

            // Función para quitar resaltado de campos
            function quitarResaltadoCampos() {
                camposObligatorios.forEach(campoId => {
                    var campo = document.getElementById(campoId);
                    campo.style.border = "";
                });
            }

            // Validación de campos requeridos
            var campoVacioEncontrado = false;
            camposObligatorios.forEach(campoId => {
                var campo = document.getElementById(campoId);
                if (campo.value === "") {
                    resaltarCampoVacio(campo);
                    campoVacioEncontrado = true;
                } else {
                    campo.style.border = "1px solid black";
                }
            });

            if (!campoVacioEncontrado) {
                quitarResaltadoCampos();

                var formData = new FormData(document.getElementById("formularioEditEvent"));
                // Asegúrate de que estás obteniendo el ID del evento correctamente
                formData.append('eventId', document.getElementById('eventId').value);

                fetch("{{ route('promotor.editEvent') }}", {
                        method: "POST",
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.event) {
                            closeModal();
                            showSuccessAlert('{{ Session::get('success_message') }}');
                        } else {}
                    })
                    .catch(error => {
                        console.error('Error:', error.message);
                    });
            }
        }

        function showSuccessAlert(message) {
            var alertContainer = document.getElementById('success-message');
            alertContainer.innerHTML = '<p class="message">' + message + '</p><span class="close-btn message" onclick="closeAlert()">Tancar</span>';
            alertContainer.style.display = 'flex';
        }

        function closeAlert() {
            var alertContainer = document.getElementById('success-message');
            alertContainer.style.opacity = '0';
            alertContainer.style.display = 'none';
            window.location.reload();
        }
    </script>
@endpush
