@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


<form action="{{ route('home') }}" method="GET">
    <div class="contenedorFiltro">
        <label for="filtro">Buscar por:</label>
        <select id="filtro" name="filtro" class="filtro">
            <option value="ciudad" {{ $selectedFiltro == 'ciudad' ? 'selected' : '' }}>Ciudad</option>
            <option value="recinto" {{ $selectedFiltro == 'recinto' ? 'selected' : '' }}>Recinto</option>
            <option value="evento" {{ $selectedFiltro == 'evento' ? 'selected' : '' }}>Evento</option>
        </select>
        <input type="text" name="search" value="{{ $searchTerm }}">
        <button type="submit" class="fas fa-search iconoLupa"></button>
    </div>
    <div class="contenedorFiltro">
        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria" class="filtro">
            <option value="todas">todas</option>
            <option value="musica">musica</option>
            <option value="magia">magia</option>
            <option value="baile">baile</option>
        </select>
    </div>
</form>
    <div class="pagination-info">
        <p>Mostrant {{ $events->firstItem() }} - {{ $events->lastItem() }} de {{ $events->total() }} events</p>
    </div>

    <div class="grid-container">
        @foreach ($events as $event)
        <a class="card-link" href="{{ route('tickets.showevent') }}">
            <div class="card">
                
                @if ($event->main_image)
                    <!-- <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->name }}"> -->
                    <img src="https://picsum.photos/2000" alt="{{ $event->name }}">
                @endif
                <div class="card-content">
                    <h3>{{ $event->name }}</h3>
                    <p class="description">{{ $event->description }}</p>
                    <p>Date: {{ \Carbon\Carbon::parse($event->event_date)->format('d-m-Y') }}</p> 
                    <p>Venue: {{ $event->venue->name }}</p>
                    <p>Lowest Ticket Price: {{ $event->lowestTicketPrice() }}</p>
                    
                </div>
                
            </div>
        </a>
        @endforeach
    </div>
    {{ $events->links('vendor.pagination.bootstrap-4') }}
@endsection

