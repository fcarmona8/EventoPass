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
                <p>{{ $event->description }}</p>
                <p>Category: {{ $event->category->name }}</p>
                <p>Venue: {{ $event->venue->name }}</p>
                <p>Date: {{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') }}</p>
                <p>Lowest Ticket Price: {{ $event->lowestTicketPrice() }}</p>
            </div>
        </a>
        @endforeach
    </div>
    {{ $events->links('vendor.pagination.bootstrap-4') }}
@endsection
