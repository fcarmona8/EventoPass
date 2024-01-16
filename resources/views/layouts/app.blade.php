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
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <title>@yield('title')</title>
</head>

<body class="w3-content">

    <header>
        @auth
            <div class="user-info">
                <i class="fa fa-user"></i>
                <p>{{ Auth::user()->name }}</p>

                <!-- Formulario de Logout -->
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-button"
                        style="background: none; border: none; padding: 0; text-decoration: underline; cursor: pointer;">
                        <i class="fa fa-sign-out-alt" style="font-size: 18px;"></i>
                    </button>
                </form>
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
</body>

</html>
