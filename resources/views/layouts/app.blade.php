<!doctype html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/event-create.css') }}">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <title>@yield('title')</title>
</head>

<body class="w3-content">

    <header>
        <h1 id="title">EventoPass</h1>
        <img id="logo" src="{{ asset('logo/logo.png') }}" alt="Logo de la Aplicación">
    </header>

    <main class="w3-container w3-padding">
        @yield('content')
    </main>

    <footer>
        <x-navigation :activeRoute="Route::currentRouteName()" />
    </footer>
</body>

</html>
