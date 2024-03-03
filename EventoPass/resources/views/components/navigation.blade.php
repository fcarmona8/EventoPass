<nav>
    <ul>
        <li><a href="{{ route('home') }}">Home</a></li>

        @guest
            <li><a href="{{ route('login') }}">Accés Promotors</a></li>
        @endguest

        <li><a href="{{ route('tickets.aboutus') }}">Sobre Nosaltres</a></li>
        <li><a href="{{ route('tickets.legalnotice') }}">Avisos Legals</a></li>
    </ul>
</nav>
