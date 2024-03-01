<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correo Electrónico</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        p {
            margin-bottom: 15px;
        }

        a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <p style="font-size: 16px;">Hola,</p>
    
    <p style="font-size: 16px;">Aquí tienes tus entradas:</p>

    <p style="font-size: 16px;"><a href="{{$name}}" target="_blank">Descargar el entradas en PDF</a></p>

    <p style="font-size: 16px;"><a href="{{$event}}" target="_blank">Ver evento</p></a>

    <p style="font-size: 16px;">Gracias</p>
</body>
</html>
