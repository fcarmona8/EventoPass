<!DOCTYPE html>
<html>

<head>
    <style>
        body,
html {
    width: 297mm;
    height: 200mm;
    margin: 0;
}

.pdf {
    width: 100%;
    height: 100%;
    position: relative;
}

.header {
    margin: 2% 0%;
    background-color: #11436b;
    width: 90%;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    color: rgb(230, 223, 223);
}

.pdfh1 {
    padding: 5%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
    word-wrap: break-word;
    max-width: 800px;
}

.content {
    margin-left: auto;
    margin-right: auto;
    background-color: #d8d8d8;
    width: 90%;
    display: table;
}

.infoContainer {
    display: table-row;
}

.divInfoEntrada,
.divInfoSessio,
.divInfoPersonal {
    display: table-cell;
    box-sizing: border-box;
    padding: 0% 2%;
    width: 33.33%;
    text-align: center;
}

.linea,
.linea2 {
    display: table-cell;
    position: relative;
    border-left: 7px solid white;
    height: 10%;
}

.dadesEntrades {
    margin-top: 0.5%;
    width: 90%;
    background-color: #d8d8d8;
    margin-left: auto;
    margin-right: auto;
}

.logoPDF {
    width: 150px;
    height: 150px;
}

.qr {
        position: absolute;
        top: 60%;
        left: 35%;
    }

    </style>
</head>

<body>
    @php
        $session = Session::get('datosCompra');
        $entrada = 0;
    @endphp
    @for ($i = 1; $i <= $session['nEntrades']; $i++)
    @php $entrada++; @endphp
    <div class="pdf">
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 150px;">
                        <img src="{{ public_path('logo/logo.png') }}" class="logoPDF" loading="lazy">
                    </td>
                    <td>
                        <h1 class="pdfh1">{{$session['eventName']}}</h1>
                    </td>
                </tr>
            </table>
        </div>
        <div class="content">
            <div class="infoContainer">
                <div class="divInfoEntrada">
                    <h3> Informació entrada </h3>
                       <p> Tipus: {{ $session['ticketName'.$entrada] }} </p>                     
                    <p> Preu: {{ $session['ticketNameEur'.$entrada] }} €</p>
                </div>
                <div class="linea"></div>
                <div class="divInfoSessio">
                    <h3> Informació sessió </h3>
                    <p> Data: {{ $session['fechaSession'] }} </p>
                    <p> Hora: {{ $session['horaSession'] }} </p>
                    <p> Direcció: {{ $session['eventubi'] }} </p>
                </div>
                <div class="linea2"></div>
                <div class="divInfoPersonal">
                    <h3> Informació personal </h3>
                    <p> Nom: {{ $session['name'.$entrada] }} </p>
                    <p> DNI: {{ $session['dni'.$entrada] }} </p>
                    <p> Telèfon: {{ $session['phone'.$entrada] }} </p>
                </div>
            </div>
        </div>
        <div class="dadesEntrades">
            <p style="padding: 1% 2%">
                Identificador entrada: 
                @php 
                    $hash = $session['unicIdNameTicket'.$entrada];
                    echo $hash;
                @endphp 
            </p>
        </div>
         <div class="qr">
            @php
                $qrCodeImage = QrCode::size(300)->generate('https://copernic.cat/'.$hash);
                $base64QrCode = base64_encode($qrCodeImage);
            @endphp
            <img src="data:image/png;base64, {{ $base64QrCode }}" alt="Código QR" loading="lazy">
        </div>
    </div>
    @endfor
</body>

</html>
