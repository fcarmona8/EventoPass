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

            <div class="alert alert-danger">
                <p> El pago se ha fallado. </p>
                {{ session('error') }}

                <a href="{{ route('home') }}" class="btn btn-error btnConfirmacion">Volver a la página de inicio</a>
            </div>

        
    </div>
@endsection
