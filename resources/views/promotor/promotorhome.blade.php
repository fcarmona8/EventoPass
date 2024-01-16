@extends('layouts.app')

@section('content')
    <div class="homepromotor">
        @foreach ($events as $event)
            <div class="card">
                @if ($event->main_image)
                    <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->name }}">
                @endif
                <div class="card-content">
                    <h3>{{ Str::limit($event->name, $limit = 55, $end = '...') }}</h3>
                    <p class="description">{{ $event->description }}</p>
                    <p>Proxima data: {{ \Carbon\Carbon::parse($event->event_date)->format('Y-M-D , H:i') }}</p>
                    <p>Proxima ubicació: {{ $event->venue->city }}, {{ $event->venue->venue_name }}</p>
                    <div class="divBotones">
                        <span class="card-editEvent" eventName="{{ $event->name }}" eventDesc="{{ $event->description }}" 
                            eventAddress="{{ $event->venue->id }}" eventPhoto="{{ $event->main_image }}" 
                            eventVid="{{ $event->video_link }}">Editar evento</span>
                        <a class="card-link" href="{{ route('promotorsessionslist', ['id' => $event->id]) }}">
                            <span class="card-price">Mes informació</span>
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
                    <h2 class="modal-title">Editar Evento</h2>
                    <button type="button" class="close cerrar" onclick="closeModal()" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="eventName">Nom del event:</label>
                    <input class="inputEditEvent" type="text" id="eventName" class="form-control" placeholder="Ingresa el nom del event">

                    <label for="eventDesc">Descripció del event:</label>
                    <textarea id="eventDesc" class="form-control" placeholder="Ingresa la descripció del event"></textarea>

                    <label for="eventAddress"> Adreça:</label>
                    <select name="eventAddress" class="select-categoria-desktop" id="eventAddress">
                        @foreach ($existingAddresses as $direccion)
                            <option value="{{ $direccion->id }}">
                                {{ $direccion->venue_name }}, {{ $direccion->city }}, {{ $direccion->province }},
                                {{ $direccion->postal_code }}, {{ $direccion->capacity }}
                            </option>
                        @endforeach
                    </select>

                    <label>Foto del event:</label>
                    <input class="inputEditEvent" type="file" id="eventPhoto" class="form-control" placeholder="Ingresa la foto del event">

                    <label for="eventVid">Video del event:</label>
                    <input class="inputEditEvent" type="text" id="eventVid" class="form-control" placeholder="Ingresa el video del event">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveEvent()">Guardar</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    {{ $events->links('vendor.pagination.bootstrap-4') }}

    <script>
        // Función para abrir el modal al hacer clic en "Editar evento"
        document.querySelectorAll('.card-editEvent').forEach(function (element) {
            element.addEventListener('click', function () {
                var eventName = this.getAttribute('eventName');
                var eventDesc = this.getAttribute('eventDesc');
                var eventAddress = this.getAttribute('eventAddress');
                var eventeventPhoto = this.getAttribute('eventeventPhoto');
                var eventVid = this.getAttribute('eventVid');
                openEditEventModal(eventName, eventDesc, eventAddress, eventPhoto, eventVid);
            });
        });

        // Función para abrir el modal y prellenar el nombre del evento si es necesario
        function openEditEventModal(eventName, eventDesc, eventAddress, eventPhoto, eventVid) {
            var eventNameInput = document.getElementById('eventName');
            var eventDescInput = document.getElementById('eventDesc');
            var eventAddressInput = document.getElementById('eventAddress');
            var eventPhotoInput = document.getElementById('eventPhoto');
            var eventVidInput = document.getElementById('eventVid');

            eventNameInput.value = eventName;
            eventDescInput.value = eventDesc;
            eventAddressInput.value = eventAddress;
            eventAddressInput.value = eventAddress;
            eventVidInput.value = eventVid;

            document.getElementById('editEventModal').style.display = 'block';
        }

        // Función para cerrar el modal
        function closeModal() {
            document.getElementById('editEventModal').style.display = 'none';
        }

        function saveEvent(){

            var camposObligatorios = ['nova_provincia', 'nova_ciutat', 'codi_postal', 'nom_local', 'capacitat_local'];
        }

    </script>

@endsection
