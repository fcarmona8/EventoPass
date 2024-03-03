@extends('layouts.app')

@section('content')
    <h2 class="titol-crear-event">Els meus esdeveniments</h2>
    <div class="homepromotor">
        @foreach ($events as $event)
            <div class="card cardHomePromotor" id="event-card-{{ $event->id }}">
                @if ($event->main_image_id)
                    <picture class="contenedorImagen">
                        <source media="(max-width: 799px)"
                            srcset="{{ config('services.api.url') }}{{ $event->optimizedImageSmallUrl() }}">
                        <source media="(min-width: 800px) and (max-width: 1023px)"
                            srcset="{{ config('services.api.url') }}{{ $event->optimizedImageMediumUrl() }}">
                        <img src="{{ config('services.api.url') }}{{ $event->optimizedImageLargeUrl() }}"
                            alt="{{ $event->name }}" loading="lazy"
                            onerror="this.onerror=null; this.src='https://picsum.photos/200'">
                    </picture>
                @else
                    <img src="https://picsum.photos/2000" alt="{{ $event->name }}" loading="lazy">
                @endif
                <div class="card-content">
                    <h3 id="event-name-{{ $event->id }}">{{ Str::limit($event->name, $limit = 55, $end = '...') }}</h3>
                    <p id="event-description-{{ $event->id }}" class="description">{{ $event->description }}</p>
                    <p>Proxima data: <span
                            id="event-date-{{ $event->id }}">{{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y, H:i') }}</span>
                    </p>
                    <p>Proxima ubicació: <span id="event-location-{{ $event->id }}">{{ $event->venue->city }},
                            {{ $event->venue->venue_name }}</span></p>
                    <div class="divBotones">
                        <span class="card-editEvent" eventId="{{ $event->id }}" eventName="{{ $event->name }}"
                            eventDesc="{{ $event->description }}" eventAddress="{{ $event->venue->id }}"
                            eventVid="{{ $event->video_link }}" eventHidden="{{ $event->hidden }}">Editar event</span>
                        <a class="card-link" href="{{ route('promotorsessionslist', ['id' => $event->id]) }}">
                            <span class="card-price card-info">Sessions</span>
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

    <div id="toastBox"></div>

    {{ $events->links('vendor.pagination.bootstrap-4') }}
@endsection

@push('scripts')
    <script>
        // Función para abrir el modal al hacer clic en "Editar evento"
        document.querySelectorAll('.card-editEvent').forEach(function(element) {
            element.addEventListener('click', function() {
                const eventName = this.getAttribute('eventName');
                const eventDesc = this.getAttribute('eventDesc');
                const eventAddress = this.getAttribute('eventAddress');
                const eventVid = this.getAttribute('eventVid');
                const eventId = this.getAttribute('eventId');
                const eventHidden = this.getAttribute('eventHidden');
                openEditEventModal(eventName, eventDesc, eventAddress, eventVid, eventId, eventHidden);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = "{{ session('success') }}";

            if (successMessage) {
                showToast('Event creat correctament');
            }
        });

        // Función para abrir el modal y prellenar el nombre del evento si es necesario
        function openEditEventModal(eventName, eventDesc, eventAddress, eventVid, eventId, eventHidden) {

            const eventNameInput = document.getElementById('eventName');
            const eventDescInput = document.getElementById('eventDesc');
            const eventAddressInput = document.getElementById('eventAddress');
            const eventVidInput = document.getElementById('eventVid');
            const eventIdInput = document.getElementById('eventId');
            const eventHiddenInput = document.getElementById('eventHidden');

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

        function resaltarCampoVacio(campo) {
            campo.style.border = "1px solid red";
        }

        function quitarResaltadoCampos() {
            const camposObligatorios = ['eventName', 'eventDesc', 'eventAddress', 'eventVid'];
            camposObligatorios.forEach(campoId => {
                const campo = document.getElementById(campoId);
                campo.style.border = "";
            });
        }


        function saveEvent() {

            const camposObligatorios = ['eventName', 'eventDesc', 'eventAddress'];

            const campoVacioEncontrado = false;
            camposObligatorios.forEach(campoId => {
                const campo = document.getElementById(campoId);
                if (campo.value === "") {
                    resaltarCampoVacio(campo);
                    campoVacioEncontrado = true;
                } else {
                    campo.style.border = "1px solid black";
                }
            });

            if (!campoVacioEncontrado) {
                quitarResaltadoCampos();

                const formData = new FormData(document.getElementById("formularioEditEvent"));
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
            const alertContainer = document.getElementById('success-message');
            alertContainer.innerHTML = '<p class="message">' + message +
                '</p><span class="close-btn message" onclick="closeAlert()">Tancar</span>';
            alertContainer.style.display = 'flex';
        }

        function closeAlert() {
            const alertContainer = document.getElementById('success-message');
            alertContainer.style.opacity = '0';
            alertContainer.style.display = 'none';
            window.location.reload();
        }
    </script>
@endpush
