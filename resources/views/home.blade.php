@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


<button id="mostrarFiltros"> <i class="fas fa-filter iconoFiltrar"></i>Filtrar</button>

<div class="barraLateral" id="barraLateral">
    <button id="cerrarFiltros" ><i class="fas fa-times iconoCerrar"></i></button>
    <form action="{{ route('home') }}" method="GET" class="filterForm">
        <div class="contenedorFiltro">
            <label for="filtro">Buscar per:</label>
            <div class="contenedor-buscador">
                <select id="filtro" name="filtro" class="filtro">
                <option value="ciudad" {{ $selectedFiltro == 'ciudad' ? 'selected' : '' }}>Ciutat</option>
                <option value="recinto" {{ $selectedFiltro == 'recinto' ? 'selected' : '' }}>Recinte</option>
                <option value="evento" {{ $selectedFiltro == 'evento' ? 'selected' : '' }}>Nom</option>
            </select>
            <input type="text" name="search" value="{{ $searchTerm }}" placeholder="Escriu per cercar...">
            </div>
            
            
        </div>
        <div id="contenedor-filtro-categoria" class="contenedorFiltro">
            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria" class="filtro">
                @foreach ($categories as $id => $name)
                    <option value="{{ $id }}" {{ $selectedCategoria == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="contenedor-boton-buscar">
            <button type="submit" class="fas fa-search iconoLupa"> <span>Buscar</span> </button>
        </div>
        
    </form>
</div>



    <div class="pagination-info">
        <p>Mostrant {{ $events->firstItem() }} - {{ $events->lastItem() }} de {{ $events->total() }} events</p>
    </div>

    <div class="grid-container">
        @foreach ($events as $event)
            <a class="card-link" href="{{ route('tickets.showevent', ['id' => $event->id]) }}">
                <div class="card">

                    @if ($event->main_image)
                        <!-- <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->name }}"> -->
                        <img src="https://picsum.photos/2000" alt="{{ $event->name }}">
                    @endif
                    <div class="card-content">
                        <h3>{{ $event->name }}</h3>
                        <p class="description">{{ $event->description }}</p>
                        <p>Data: {{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') }}</p>
                        <p>Ubicació: {{ $event->venue->name }}, {{ $event->venue->location }}</p>
                        <span class="card-price">Des de {{ $event->lowestTicketPrice() }} €</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    {{ $events->links('vendor.pagination.bootstrap-4') }}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const mostrarFiltrosBtn = document.getElementById('mostrarFiltros');
    const cerrarFiltrosBtn = document.getElementById('cerrarFiltros');
    const barraLateral = document.getElementById('barraLateral');

    mostrarFiltrosBtn.addEventListener('click', function() {
        // Mostrar la barra lateral
        barraLateral.style.right = '0';
    });

    cerrarFiltrosBtn.addEventListener('click', function() {
        // Ocultar la barra lateral
        barraLateral.style.right = '-350px';
    });
});
    </script>
@endsection
