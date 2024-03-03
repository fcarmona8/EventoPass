<form action="{{ route('resultats') }}" method="GET">
    <div class="contenedorFiltro">
        <label for="filtro">Buscar per:</label>
        <select id="filtro" name="filtro" class="filtro">
            <option value="ciudad" {{ $selectedFiltro == 'ciudad' ? 'selected' : '' }}>Ciutat</option>
            <option value="recinto" {{ $selectedFiltro == 'recinto' ? 'selected' : '' }}>Recinte</option>
            <option value="evento" {{ $selectedFiltro == 'evento' ? 'selected' : '' }}>Nom</option>
        </select>
        <input class="inputSearch" type="text" name="search" value="{{ $searchTerm }}">
        <button type="submit" class="fas fa-search iconoLupa"></button>
    </div>
    <div class="contenedorFiltro">
        <label for="categoria">Categoria:</label>
        <select id="categoria" name="categoria" class="filtro">
            @foreach ($categories as $id => $name)
                <option value="{{ $id }}" {{ $selectedCategoria == $id ? 'selected' : '' }}>
                    {{ $name }}</option>
            @endforeach
        </select>
    </div>
</form>