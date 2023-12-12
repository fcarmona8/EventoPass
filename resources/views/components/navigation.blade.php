@php
    $routes = [
        'home' => 'Home',
        'tickets.promoterhome' => 'Home Promotor',
        'tickets.aboutus' => 'Sobre Nosaltres',
        'tickets.legalnotice' => 'Avisos Legals',
        'tickets.showevent' => 'Mostrar Esdeveniment',
        'tickets.buytickets' => 'Comprar Entrades',
    ];
    $activeRoute = Route::currentRouteName();
@endphp

<nav>
    <ul>
        @foreach ($routes as $route => $label)
            @if ($route != $activeRoute)
                <li><a href="{{ route($route) }}">{{ $label }}</a></li>
            @endif
        @endforeach
    </ul>
</nav>
