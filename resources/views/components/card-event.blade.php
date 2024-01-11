<a class="card-link" href="{{ route('tickets.showevent', ['id' => $event->id]) }}">  
    <div class="card">
        @if ($event->main_image)
            <!-- <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->name }}"> -->
            <img src="https://picsum.photos/2000" alt="{{ $event->name }}">
        @endif
        <div class="card-content">
            <h3>{{ Str::limit($event->name, $limit = 70, $end = '...') }}</h3>
            <p class="description">{{ $event->description }}</p>
            <p>Data: {{ \Carbon\Carbon::parse($event->event_date)->format('Y-M-D , H:i') }}</p>
            <p>Ubicació: {{ $event->venue->name }}, {{ $event->venue->location }}</p>
            <span class="card-price">Des de {{ $event->lowestTicketPrice() }} €</span>
        </div>
    </div>
</a>
