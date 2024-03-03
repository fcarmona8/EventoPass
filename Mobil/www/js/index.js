document.addEventListener('deviceready', onDeviceReady, false);

function onDeviceReady() {
    console.log("Cordova está listo");
}

function login() {
    const sessionCodeInput = document.getElementById("sessionCodeInput");
    const formData = new FormData();
    formData.append('session_code', sessionCodeInput.value);

    fetch('https://192.168.43.191:443/api/V1/login', {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Respuesta de red no fue ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log("Login exitoso");
            localStorage.setItem('session_code', sessionCodeInput.value);
            document.getElementById("loginForm").style.display = "none";
            document.getElementById("logoutButton").style.display = "block";
            document.getElementById("scanQRCode").style.display = "block";
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.error('Error durante el login:', error);
        document.getElementById("result").innerText = "Error de conexión durante el login. " + error.message;
    });
}

function logout() {
    console.log("Logout realizado");
    
    document.getElementById("sessionCodeInput").value = "";

    document.getElementById("loginForm").style.display = "";
    document.getElementById("logoutButton").style.display = "none";
    document.getElementById("scanQRCode").style.display = "none";
    document.getElementById("validateTicket").style.display = "none";
    
    document.getElementById("result").innerText = "";
}

function scanQRCode() {
    cordova.plugins.barcodeScanner.scan(
        function (result) {
            if (!result.cancelled) {
                console.log("Contenido del QR: ", result.text);

                const parts = result.text.split('$');
                if(parts.length === 2) {
                    const sessionId = parts[0];
                    const hash = parts[1];
                    console.log("Session ID: ", sessionId);
                    console.log("Hash: ", hash);
                    validateTicket(sessionId, hash);
                } else {
                    console.error("El formato del QR no es el esperado.");
                }
            }
        },
        function (error) {
            alert("Error al escanear: " + error);
        },
        {
            preferFrontCamera: false,
            showFlipCameraButton: true,
            showTorchButton: true,
            torchOn: false,
            saveHistory: false,
            prompt: "Coloca el código QR dentro del área de escaneo",
            resultDisplayDuration: 500,
            formats: "QR_CODE",
            orientation: "portrait",
            disableAnimations: true,
            disableSuccessBeep: false
        }
    );
}

function validateTicket(sessionId, hash) {
    const sessionCode = localStorage.getItem('session_code');
    fetch(`https://192.168.43.191:443/api/V1/tickets/validate/${sessionId}/${hash}`, {
        method: 'GET',
        headers: {
            'Session-Code': sessionCode,
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Respuesta de red no fue ok');
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new TypeError("Oops, no recibimos JSON!");
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            let message = "Ticket validado correctamente: " + data.message;
            document.getElementById("result").innerText = message;
        } else {
            document.getElementById("result").innerText = "Error al validar el ticket: " + data.message;
        }
    })
    .catch(error => {
        console.error('Error al validar el ticket:', error);
        document.getElementById("result").innerText = "Error de conexión al validar el ticket. Detalles: " + error.message;
    });
}
