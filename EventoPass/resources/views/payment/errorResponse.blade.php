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

.btn-error{
    background-color: red;
}
    </style>
@section('content')
    <div class="containerShowEvent">
        <h2>Respuesta de Pago</h2>

            <div class="alert alert-success confirmacion">
                <p> El pago ha fallado. </p>
                {{ session('error') }}

                <a href="{{ route('home') }}" class="btn btn-error btnConfirmacion">Volver a la página de inicio</a>
            </div>
    </div>
@endsection
