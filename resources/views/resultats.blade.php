@extends('layouts.app')
@section('title', 'Resultats')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
   
    <x-cercador :selectedFiltro="$selectedFiltro" :searchTerm="$searchTerm" :categories="$categories" :selectedCategoria="$selectedCategoria"/>
    
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
@endsection
