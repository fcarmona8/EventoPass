<!doctype html>
<html lang="ca">

<head>
    @include('partials.meta', $metaData ?? [])
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!--<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/resultats.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/event-create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/promotorhome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/crear-sesion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sesion-list.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('css/comentarios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/compra-tickets.css') }}">-->

    <title>@yield('title')</title>
</head>

<body class="w3-content">

    <header>
        @auth
            <div id="mobile-menu-div"><button class="fa fa-user menu-mobile" onclick="toggleSidebar()"></button></div>
            <div class="sidebar" id="sidebar">
                <div class="user-info">
                    <div class="usuari">
                        <p class="user-name">Usuari: {{ Auth::user()->name }}</p>
                    </div>

                    <div class="user-menu">
                        @if (Auth::user()->role->name == 'administrador')
                            <li><a href="{{ route('ruta.admin') }}">Taulell d'administració</a></li>
                        @endif

                        @if (Auth::user()->role->name == 'promotor')
                            <li><a href="{{ route('promotorhome') }}">Home Promotor</a></li>
                            <li><a href="{{ route('promotor.createEvent') }}">Crear Esdeveniment</a></li>
                            <li><a href="{{ route('promotorsessionslist') }}">Veure totes les sessions</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Accés Promotors</a></li>
                        @endif
                        <li><a href="{{ route('user.profile') }}">Perfil d'Usuari</a></li>
                        <div class="logout-div">
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="logout-button">
                                    <span class="logout-button-text">Tancar Sessió</span>
                                    <i class="fa fa-sign-out-alt"
                                        style="font-size: 18px; color:#E00F00; margin-left:10px"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        @endauth

        <h1 id="title">EventoPass</h1>
        <a href="{{ route('home') }}">
            <img id="logo" src="{{ asset('logo/logo.png') }}" alt="Logo de la Aplicación" loading="lazy">
        </a>
    </header>

    <main class="w3-container w3-padding">
        @yield('content')
        <div class="social-share-buttons"> Compartir:
            <!-- Botón compartir en Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}" target="_blank"
                class="social-button facebook" title="Compartir en Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>

            <!-- Botón compartir en Twitter -->
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}&text=Texto+personalizado+aquí"
                target="_blank" class="social-button twitter" title="Compartir en Twitter">
                <i class="fab fa-twitter"></i>
            </a>

            <!-- Botón compartir en LinkedIn -->
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(Request::fullUrl()) }}"
                target="_blank" class="social-button linkedin" title="Compartir en LinkedIn">
                <i class="fab fa-linkedin-in"></i>
            </a>
        </div>

    </main>

    <footer>
        <x-navigation :activeRoute="Route::currentRouteName()" />
    </footer>

    @stack('scripts')

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.user-info');
            sidebar.classList.toggle('active');
            content.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }

        }

        function showToast(msg) {
            let toast = document.createElement("div");

            if (msg.includes('error') || msg.includes('Error')) {
                msg = `<svg class="errorIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
                        ${msg}`
                toast.classList.add("error");
            } else {
                msg = `<svg class="successIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
                ${msg}`
            }

            toast.classList.add("toast");
            toast.innerHTML = msg;
            toastBox.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>
</body>

</html>
