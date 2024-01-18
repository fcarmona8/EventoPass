<nav>
    <ul>
        <li><a href="{{ route('home') }}">Home</a></li>

        @auth
            @if (Auth::user()->role->name == 'administrador')
                <li><a href="{{ route('ruta.admin') }}">Taulell d'administració</a></li>
            @endif

            @if (Auth::user()->role->name == 'promotor')
                <li><a href="{{ route('promotorhome') }}">Home Promotor</a></li>
                <li><a href="{{ route('promotor.createEvent') }}">Crear Esdeveniment</a></li>
                <a href="{{ route('promotorsessionslist') }}">Ver todas las sesiones</a>
            @else
                <li><a href="{{ route('login') }}">Accés Promotors</a></li>
            @endif

            <li><a href="{{ route('user.profile') }}">Perfil d'Usuari</a></li>
        @else
            <li><a href="{{ route('login') }}">Accés Promotors</a></li>
        @endauth

        <li><a href="{{ route('tickets.aboutus') }}">Sobre Nosaltres</a></li>
        <li><a href="{{ route('tickets.legalnotice') }}">Avisos Legals</a></li>
    </ul>
</nav>
