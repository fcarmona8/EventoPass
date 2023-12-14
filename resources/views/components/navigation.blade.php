@php
    $routes = [
        'home' => 'Home',
        'tickets.promoterhome' => 'Home Promotor',
        'tickets.showevent' => 'Mostrar Esdeveniment',
        'tickets.buytickets' => 'Comprar Entrades',
        'tickets.aboutus' => 'Sobre Nosaltres',
        'tickets.legalnotice' => 'Avisos Legals',
    ];
    $activeRoute = Route::currentRouteName();
@endphp

<nav>
    <ul>
        @foreach ($routes as $route => $label)
            @if ($route != $activeRoute && $route != 'tickets.showevent')
                <li><a href="{{ route($route) }}">{{ $label }}</a></li>
            @endif
        @endforeach
    </ul>
</nav>
