@extends('layouts.app')

@section('content')
    <div class="homepromotor">
        @foreach ($events as $event)
            <div class="card">
                @if ($event->main_image)
                    <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->name }}">
                    {{-- <img src="https://picsum.photos/2000" alt="{{ $event->name }}"> --}}
                @endif
                <div class="card-content">
                    <h3>{{ Str::limit($event->name, $limit = 55, $end = '...') }}</h3>
                    <p class="description">{{ $event->description }}</p>
                    <p>Proxima data: {{ \Carbon\Carbon::parse($event->event_date)->format('Y-M-D , H:i') }}</p>
                    <p>Proxima ubicació: {{ $event->venue->city }}, {{ $event->venue->venue_name }}</p>
                    <div class="divBotones">
                        <span class="card-editEvent">Editar event</span>
                        <a class="card-link" href="{{ route('promotorsessionslist', ['id' => $event->id]) }}">
                            <span class="card-price">Mes informació</span>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{ $events->links('vendor.pagination.bootstrap-4') }}
@endsection
