@extends('layouts.app')

@section('title', 'Home')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <x-cercador :selectedFiltro="$selectedFiltro" :searchTerm="$searchTerm" :categories="$categories" :selectedCategoria="$selectedCategoria" />

    <div class="categories">
        
        @foreach ($categoriesPerPage as $categoria)
            <div class="cardCategoria">
                <h1>{{ $categoria->name }}</h1>
                <div class="events">
                    @if ($events->where('category_id', $categoria->id)->isEmpty())
                        <span class="CategoriaVaciaTexto">No hi ha esdeveniments en aquesta categoria</span>
                    @else
                        @foreach ($events as $event)
                            @if ($event->category_id == $categoria->id)
                                <div class="event">
                                    <x-card-event :event=$event />
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
                <form class="verMas" id="searchForm" action="{{ route('resultats') }}" method="GET">
                    <button class="BtnVerMas">Ver mas</button>
                    <input type="text" name="categoria" value="{{ $categoria->id }}" style="display: none">
                    </select>
                </form>
            </div>
        @endforeach
    </div>

    {{ $categoriesPerPage->links('vendor.pagination.bootstrap-4') }}

@endsection
