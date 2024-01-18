<!doctype html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/resultats.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/event-create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/promotorhome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/crear-sesion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <title>@yield('title')</title>
</head>

<body class="w3-content">

    <header>
        @auth
            <div id="mobile-menu-div"><button class="fa fa-user menu-mobile" onclick="toggleSidebar()"></button></div>
            <div class="container">
                <div class="sidebar" id="sidebar">
                    <div class="user-info">
                        <div class="usuari">
                            <p class="user-name">Usuari: {{ Auth::user()->name }}</p>

                            <!-- Formulario de Logout -->
                        </div>

                        <div class="user-menu">
                            @if (Auth::user()->role->name == 'administrador')
                                <li><a href="{{ route('ruta.admin') }}">Taulell d'administració</a></li>
                            @endif

                            @if (Auth::user()->role->name == 'promotor')
                                <li><a href="{{ route('promotorhome') }}">Home Promotor</a></li>
                                <li><a href="{{ route('promotor.createEvent') }}">Crear Esdeveniment</a></li>
                            @else
                                <li><a href="{{ route('login') }}">Accés Promotors</a></li>
                            @endif
                            <li><a href="{{ route('user.profile') }}">Perfil d'Usuari</a></li>
                            <div class="logout-div">
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="logout-button">
                                        <span class="logout-button-text">Tancar Sessió</span>
                                        <i class="fa fa-sign-out-alt" style="font-size: 18px; color:#E00F00; margin-left:10px"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        @endauth

        <h1 id="title">EventoPass</h1>
        <img id="logo" src="{{ asset('logo/logo.png') }}" alt="Logo de la Aplicación">
    </header>

    <main class="w3-container w3-padding">
        @yield('content')
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
            
        }
    </script>
</body>

</html>
