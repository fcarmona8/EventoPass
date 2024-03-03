<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correo</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <div class="container containerNuevaContrasena">
        <div class="row justify-content-center ResPasCard">
            
                
                    <div class="card-header">Restablecer Contraseña</div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="error alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <form method="POST" action="{{ route('password.update') }}">
                            {{ csrf_field() }}

                            <input type="hidden" name="token" value="{{ $token }}">

                            <!-- Campo de Email -->
                            <div class="form-group">
                                <label for="email">Correo Electrónico:</label>
                                <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}"
                                    required autofocus class="form-control">
                            </div>

                            <!-- Campo de Nueva Contraseña -->
                            <div class="form-group">
                                <label for="password">Nueva Contraseña:</label>
                                <input type="password" name="password" id="password" required class="form-control">
                                <!-- Instrucciones para la contraseña -->
                                <small id="passwordHelpBlock" class="form-text text-muted">
                                    Tu contraseña debe tener al menos 8 caracteres e incluir una letra mayúscula, una
                                    minúscula y un símbolo.
                                </small>
                            </div>

                            <!-- Campo de Confirmación de Contraseña -->
                            <div class="form-group">
                                <label for="password-confirm">Confirmar Nueva Contraseña:</label>
                                <input type="password" name="password_confirmation" id="password-confirm" required
                                    class="form-control">
                            </div>

                            <!-- Botón para restablecer contraseña -->
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
                            </div>
                        </form>
                    </div>
                
            
        </div>
    </div>
    </div>
</body>

</html>
