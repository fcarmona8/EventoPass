@extends('layouts.app')

@section('content')
    <div class="contenedorCrearComentario">
        <form class="formCrearComentario" action="{{ route('tickets.guardarComentario') }}" method="post"
            id="formCrearComentario">
            @csrf
            <h1 class="tituloEnviarComentario">Enviar un comentari</h1>
            <label for="nombre">Nom:</label>
            <input type="hidden" name="eventoId" value="{{ $eventoId }}">
            <input type="text" name="nombre" id="nombre" maxlength="25" /><br>

            <label for="caretes">Què t'ha semblat l'esdeveniment:</label>
            <div class="contenedorPuntuacionIconos">
                <label class="smileyRatingLabel" for="sad2">
                    <input type="radio" name="smileyRating" class="sad" id="sad2" value=1 />
                    <svg class="iconosComentarios" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%"
                        viewBox="0 0 24 24">
                        <path
                            d="M20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12M22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2A10,10 0 0,1 22,12M15.5,8C16.3,8 17,8.7 17,9.5C17,10.3 16.3,11 15.5,11C14.7,11 14,10.3 14,9.5C14,8.7 14.7,8 15.5,8M10,9.5C10,10.3 9.3,11 8.5,11C7.7,11 7,10.3 7,9.5C7,8.7 7.7,8 8.5,8C9.3,8 10,8.7 10,9.5M12,14C13.75,14 15.29,14.72 16.19,15.81L14.77,17.23C14.32,16.5 13.25,16 12,16C10.75,16 9.68,16.5 9.23,17.23L7.81,15.81C8.71,14.72 10.25,14 12,14Z" />
                    </svg>
                </label>

                <label class="smileyRatingLabel" for="happy2">
                    <input type="radio" name="smileyRating" class="happy" id="happy2" value=2 checked />
                    <svg class="iconosComentarios" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%"
                        viewBox="0 0 24 24">
                        <path
                            d="M20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12M22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2A10,10 0 0,1 22,12M10,9.5C10,10.3 9.3,11 8.5,11C7.7,11 7,10.3 7,9.5C7,8.7 7.7,8 8.5,8C9.3,8 10,8.7 10,9.5M17,9.5C17,10.3 16.3,11 15.5,11C14.7,11 14,10.3 14,9.5C14,8.7 14.7,8 15.5,8C16.3,8 17,8.7 17,9.5M12,17.23C10.25,17.23 8.71,16.5 7.81,15.42L9.23,14C9.68,14.72 10.75,15.23 12,15.23C13.25,15.23 14.32,14.72 14.77,14L16.19,15.42C15.29,16.5 13.75,17.23 12,17.23Z" />
                    </svg>
                </label>

                <label class="smileyRatingLabel" for="super-happy2">
                    <input type="radio" name="smileyRating" class="super-happy" id="super-happy2" value=3 />
                    <svg class="iconosComentarios" viewBox="0 0 24 24">
                        <path
                            d="M12,17.5C14.33,17.5 16.3,16.04 17.11,14H6.89C7.69,16.04 9.67,17.5 12,17.5M8.5,11A1.5,1.5 0 0,0 10,9.5A1.5,1.5 0 0,0 8.5,8A1.5,1.5 0 0,0 7,9.5A1.5,1.5 0 0,0 8.5,11M15.5,11A1.5,1.5 0 0,0 17,9.5A1.5,1.5 0 0,0 15.5,8A1.5,1.5 0 0,0 14,9.5A1.5,1.5 0 0,0 15.5,11M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z" />
                    </svg>
                </label>
            </div>


            <label for="puntuacion">Valora l'esdeveniment amb una puntuació de l'1 al 5:</label>
            <svg id="svg-sprite" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" style="display: none;">
                <symbol id="star-icon">
                    <title>star</title>
                    <path d="M20 7h-7L10 .5 7 7H0l5.46 5.47-1.64 7 6.18-3.7 6.18 3.73-1.63-7z" />
                </symbol>
            </svg>
            <section hidden>
                <div id="contenedorPuntuacionHidden">
                    <fieldset>
                        <div class="stars-wrapper">
                            <div class="star-container">
                                <input class="starInput" id="1-star-rating" type="radio" name="reviewRating"
                                    value="1">
                                <label for="1-star-rating">1 Star</label>
                                <div class="fake-star">
                                    <svg viewBox="0 0 20 20" width="30" height="30">
                                        <use href="#star-icon"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="star-container">
                                <input class="starInput" id="2-star-rating" type="radio" name="reviewRating"
                                    value="2">
                                <label for="2-star-rating">2 Stars</label>
                                <div class="fake-star">
                                    <svg viewBox="0 0 20 20" width="30" height="30">
                                        <use href="#star-icon"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="star-container">
                                <input class="starInput" id="3-star-rating" type="radio" name="reviewRating"
                                    value="3">
                                <label for="3-star-rating">3 Stars</label>
                                <div class="fake-star">
                                    <svg viewBox="0 0 20 20" width="30" height="30">
                                        <use href="#star-icon"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="star-container">
                                <input class="starInput" id="4-star-rating" type="radio" name="reviewRating"
                                    value="4">
                                <label for="4-star-rating">4 Stars</label>
                                <div class="fake-star">
                                    <svg viewBox="0 0 20 20" width="30" height="30">
                                        <use href="#star-icon"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="star-container">
                                <input class="starInput" id="5-star-rating" type="radio" name="reviewRating"
                                    value="5">
                                <label for="5-star-rating">5 Stars</label>
                                <div class="fake-star">
                                    <svg viewBox="0 0 20 20" width="30" height="30">
                                        <use href="#star-icon"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </section>

            <section>
                <div id="contenedorPuntuacion">
                    <div class="stars-wrapper">
                        <input class="starInput" id="one-star-rating-2" type="radio" name="reviewRating"
                            value="1" checked>
                        <label for="one-star-rating-2">1 Star</label>
                        <input class="starInput" id="two-star-rating-2" type="radio" name="reviewRating"
                            value="2">
                        <label for="two-star-rating-2">2 Stars</label>
                        <input class="starInput" id="three-star-rating-2" type="radio" name="reviewRating"
                            value="3">
                        <label for="three-star-rating-2">3 Stars</label>
                        <input class="starInput" id="four-star-rating-2" type="radio" name="reviewRating"
                            value="4">
                        <label for="four-star-rating-2">4 Stars</label>
                        <input class="starInput" id="five-star-rating-2" type="radio" name="reviewRating"
                            value="5">
                        <label for="five-star-rating-2">5 Stars</label>
                        <div class="stars-display">
                            <svg viewBox="0 0 20 20" width="30" height="30">
                                <use href="#star-icon"></use>
                            </svg>
                            <svg viewBox="0 0 20 20" width="30" height="30">
                                <use href="#star-icon"></use>
                            </svg>
                            <svg viewBox="0 0 20 20" width="30" height="30">
                                <use href="#star-icon"></use>
                            </svg>
                            <svg viewBox="0 0 20 20" width="30" height="30">
                                <use href="#star-icon"></use>
                            </svg>
                            <svg viewBox="0 0 20 20" width="30" height="30">
                                <use href="#star-icon"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </section>

            <label for="titulo">Títol del comentari:</label>
            <input type="text" name="titulo" id="titulo" maxlength="50" /><br>

            <label for="comentario">Comentari:</label>
            <textarea name="comentario" id="comentario"></textarea><br>

            <button class="guardar-comentario" id="guardar-comentario" type="submit">Enviar</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const nombreField = document.getElementById('nombre');
        const tituloField = document.getElementById('titulo');
        const comentarioField = document.getElementById('comentario');

        // Función para agregar un mensaje de error
        function addErrorMessage(field, message) {
            const errorMessageId = field.id + '-error';
            let errorMessage = document.getElementById(errorMessageId);

            if (errorMessage) {
                errorMessage.textContent = message;
            } else {
                errorMessage = document.createElement('div');
                errorMessage.id = errorMessageId;
                errorMessage.className = 'error-message';
                errorMessage.textContent = message;
                field.parentNode.insertBefore(errorMessage, field.nextSibling);
            }
        };

        // Función para quitar el mensaje de error
        function removeErrorMessage(field) {
            const errorMessageId = field.id + '-error';
            const errorMessage = document.getElementById(errorMessageId);

            if (errorMessage) {
                errorMessage.parentNode.removeChild(errorMessage);
            }
        };

        const formComentario = document.getElementById('formCrearComentario');

        if (formComentario) {
            formComentario.addEventListener('submit', function(event) {

                // Verificar si los campos están vacíos y mostrar mensajes de error
                if (!nombreField.value.trim()) {
                    event.preventDefault();
                    nombreField.style.border = '1px solid red';
                    addErrorMessage(nombreField, 'Campo requerido');
                } else {
                    nombreField.style.border = '';
                    removeErrorMessage(nombreField);
                }

                if (!tituloField.value.trim()) {
                    event.preventDefault();
                    tituloField.style.border = '1px solid red';
                    addErrorMessage(tituloField, 'Campo requerido');
                } else {
                    tituloField.style.border = '';
                    removeErrorMessage(tituloField);
                }

                if (!comentarioField.value.trim()) {
                    event.preventDefault();
                    comentarioField.style.border = '1px solid red';
                    addErrorMessage(comentarioField, 'Campo requerido');
                } else {
                    comentarioField.style.border = '';
                    removeErrorMessage(comentarioField);
                }
            });
        }
    </script>
@endpush
