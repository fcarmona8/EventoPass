@extends('layouts.app')

@section('title', 'Home')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <x-cercador :selectedFiltro="$selectedFiltro" :searchTerm="$searchTerm" :categories="$categories" :selectedCategoria="$selectedCategoria" />

    <div class="categories">
        @foreach ($categoriesPerPage as $categoria)
            <div class="cardCategoria">
                <h1>{{ $categoria->name }}</h1>
                @foreach($events as $event)
                    @if($event->category_id  == $categoria->id)
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
                        @endif
                @endforeach
                <form id="searchForm" action="{{ route('resultats') }}" method="GET">
                    <button >Ver mas</button>
                    <input type="text" name="categoria" value={{$categoria->id}} style="display: none">
                    </select>
                    </form>
                
            </div>
        @endforeach
    </div>

    {{ $categoriesPerPage->links('vendor.pagination.bootstrap-4') }}

@endsection
