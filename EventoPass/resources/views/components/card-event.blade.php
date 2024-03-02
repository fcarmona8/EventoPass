<a class="card-link" href="{{ route('tickets.showevent', ['id' => $event->id]) }}">
    <div class="card">
        <picture class="contenedorImagen">
            <source media="(max-width: 799px)"
                srcset="{{ config('services.api.url') }}{{ $event->optimizedImageSmallUrl() }}">
            <source media="(min-width: 800px) and (max-width: 1023px)"
                srcset="{{ config('services.api.url') }}{{ $event->optimizedImageMediumUrl() }}">
            <img src="{{ config('services.api.url') }}{{ $event->optimizedImageLargeUrl() }}" alt="{{ $event->name }}"
                loading="lazy" onerror="this.onerror=null; this.src='https://picsum.photos/200'">
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
