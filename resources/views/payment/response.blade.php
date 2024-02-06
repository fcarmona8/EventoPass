@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Respuesta de Pago</h2>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @else
            <div class="alert alert-success">
                El pago se ha realizado con éxito.
            </div>
        @endif

        <a href="{{ route('home') }}" class="btn btn-primary">Volver a la página de inicio</a>
    </div>
@endsection
