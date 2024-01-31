@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Respuesta de Pago</h2>

        @if (isset($response['status']) && $response['status'] === 'success')
            <div class="alert alert-success">
                El pago se ha realizado con éxito.
            </div>
        @else
            <div class="alert alert-danger">
                Se ha producido un error en el pago. Por favor, inténtalo de nuevo más tarde.
            </div>
        @endif

        <a href="{{ route('home') }}" class="btn btn-primary">Volver a la página de inicio</a>
    </div>
@endsection
