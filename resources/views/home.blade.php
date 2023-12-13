@extends('layouts.app')

@section('content')
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
            </div>
        @endforeach
    </div>
    {{ $events->links() }}
@endsection
