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
    <div class="containerShowEvent">
        <h2>Respuesta de Pago</h2>
            <div class="alert alert-success confirmacion">
               <p> El pago se ha realizado con éxito. </p>

               <p> Las entradas se enviaran por correo. </p>

               <a href="{{ route('home') }}" class="btn btn-primary btnConfirmacion">Volver a la página de inicio</a>
            </div>

        
    </div>
@endsection
