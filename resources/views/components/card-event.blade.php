<a class="card-link" href="{{ route('tickets.showevent', ['id' => $event->id]) }}">
    <div class="card">
        <picture>
            <source media="(max-width: 799px)" srcset="http://localhost:8080{{ $event->optimizedImageSmallUrl() }}">
            <source media="(min-width: 800px)" srcset="http://localhost:8080{{ $event->optimizedImageMediumUrl() }}">
            <img src="http://localhost:8080{{ $event->optimizedImageLargeUrl() }}" alt="{{ $event->name }}"
                loading="lazy">
        </picture>
        <div class="card-content">
            <h3>{{ Str::limit($event->name, $limit = 55, $end = '...') }}</h3>
            <p class="description">{{ $event->description }}</p>
            <p>Data: {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y, H:i') }}</p>
            <p>Ubicació: {{ $event->venue->city }}, {{ $event->venue->venue_name }}</p>
            <span class="card-price">Des de {{ $event->lowestTicketPrice() }} €</span>
        </div>
    </div>
</a>
