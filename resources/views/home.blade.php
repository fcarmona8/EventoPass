@extends('layouts.app')

@section('content')
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

