<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ticketsPDF.css') }}">
</head>
<body>
    <div class="pdf">
        <div class="header">
            <img src="{{ asset('logo/logo.png') }}" alt="Logo" class="logoPDF">
            <h1 class="pdfh1">{{ $title }}</h1>
        </div>
    </div>
</body>
</html>
