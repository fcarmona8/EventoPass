<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <div class="container ResPasCard">
            <div class="ResPasCardHeader">Restablecer Contraseña</div>
            <div class="ResPasCardBody">
                <!-- Mostrar errores -->
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    {{ csrf_field() }}

                    <!-- Campo de Email -->
                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" name="email" id="email" required class="form-control ResPasCardInput">
                    </div>

                    <!-- Botón para enviar solicitud -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary ">Enviar enlace de
                            restablecimiento</button>
                    </div>
                </form>
        </div>
    </div>
    </div>
</body>

</html>
