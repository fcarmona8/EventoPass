<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @include('partials.meta', $metaData ?? [])
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>

        <!-- Mostrar mensajes de error -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="error">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario de Login -->
        <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

            <!-- Campo de Email -->
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="form-control LoginCardInput">
            </div>

            <!-- Campo de Contraseña -->
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required class="form-control LoginCardInput">
            </div>

            <!-- Botón de Submit -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
        </form>

        <a href="{{ route('password.request') }}">He oblidat la contrasenya</a>
    </div>
</body>

</html>
