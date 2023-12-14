@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


<form action="{{ route('home') }}" method="GET">
    <div class="contenedorFiltro">
        <label for="filtro">Buscar por:</label>
        <select id="filtro" name="filtro" class="filtro">
            <option value="ciudad">Ciudad</option>
            <option value="recinto">Recinto</option>
            <option value="evento">Evento</option>
        </select>
        <input type="text" name="search">
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
        <p>Mostrando {{ $events->firstItem() }} - {{ $events->lastItem() }} de {{ $events->total() }} eventos</p>
    </div>

    <div class="grid-container">
        @foreach ($events as $event)
            <div class="event-card">
                <h2>{{ $event->name }}</h2>
                @if ($event->main_image)
                    <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->name }}">
                @endif
                <p>{{ $event->description }}</p>
                <p>Category: {{ $event->category->name }}</p>
                <p>Venue: {{ $event->venue->name }}</p>
                <p>Date: {{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') }}</p>
                <p>Lowest Ticket Price: {{ $event->lowestTicketPrice() }}</p>
            </div>
        @endforeach
    </div>
    {{ $events->links('vendor.pagination.bootstrap-4') }}
@endsection
