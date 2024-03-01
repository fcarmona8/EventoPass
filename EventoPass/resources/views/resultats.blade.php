@extends('layouts.app')
@section('title', 'Resultats')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <x-cercador :selectedFiltro="$selectedFiltro" :searchTerm="$searchTerm" :categories="$categories" :selectedCategoria="$selectedCategoria" />

    <div class="pagination-info">
        <p>Mostrant {{ $events->firstItem() }} - {{ $events->lastItem() }} de {{ $events->total() }} events</p>
    </div>

    <div class="grid-container">
        @if ($events->isEmpty())
            <span class="CategoriaVaciaTexto"> No hi ha esdeveniments en aquesta categoria</span>
        @else
            @foreach ($events as $event)
                <x-card-event :event=$event />
            @endforeach
        @endif
    </div>
    {{ $events->links('vendor.pagination.bootstrap-4') }}
@endsection
