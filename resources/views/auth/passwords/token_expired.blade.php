<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enlace Caducado</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            padding: 50px;
        }

        .container {
            background-color: #fff;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }

        h1 {
            color: #d93025;
        }

        p {
            color: #555;
        }

        a {
            text-decoration: none;
            color: #1a73e8;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Enlace de Restablecimiento Caducado</h1>
        <p>Lo sentimos, tu enlace de restablecimiento de contraseña ha caducado. Por favor, solicita un nuevo enlace.
        </p>
        <a href="{{ route('password.request') }}">Solicitar nuevo enlace</a>
    </div>
</body>

</html>
