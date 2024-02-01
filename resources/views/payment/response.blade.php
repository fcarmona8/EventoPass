@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Respuesta de Pago</h2>
        <div class="alert alert-success">
            El pago se ha realizado con éxito.
        </div>

        <a href="{{ route('home') }}" class="btn btn-primary">Volver a la página de inicio</a>
    </div>
@endsection
