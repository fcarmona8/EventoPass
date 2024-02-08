@extends('layouts.app')
<style>
.confirmacion{
    display: flex;
    align-items: center;
    flex-direction: column;
}

.btnConfirmacion{
    padding: 1%;
    margin-top: 3%; 
}
    </style>
@section('content')
    <div class="container">
        <h2>Respuesta de Pago</h2>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}

                <a href="{{ route('home') }}" class="btn btn-primary btnConfirmacion">Volver a la página de inicio</a>
            </div>
        @else
            <div class="alert alert-success confirmacion">
               <p> El pago se ha realizado con éxito. </p>

               <p> Las entradas se enviaran por correo. </p>

               <a href="{{ route('home') }}" class="btn btn-primary btnConfirmacion">Volver a la página de inicio</a>
            </div>
        @endif

        
    </div>
@endsection
