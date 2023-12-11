<!doctype html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="{{ asset('css/w3.css') }}">

    <title>@yield('title')</title>
</head>

<body class="w3-content">
    <x-navigation :activeRoute="Route::currentRouteName()" />
    <div class="w3-container w3-padding">
        @yield('content')
    </div>

</body>

</html>
