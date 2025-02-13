<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma de Factura</title>
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Tu estilo personalizado */
        .neumorphism {
            background: #e0e5ec;
            border-radius: 15px;
            box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
        }

        .neumorphism-icon {
            box-shadow: 6px 6px 12px #bebebe, -6px -6px 12px #ffffff;
        }

        #signatureContainer {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 8px;
        }

        #signatureCanvas {
            width: 100%;
            height: 200px;
            border: 1px solid #000;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            #signatureContainer {
                max-width: 100%;
                padding: 5px;
            }

            #signatureCanvas {
                height: 150px;
            }
        }

        @media (orientation: landscape) {
            #signatureCanvas {
                height: 300px;
            }

            #signatureContainer {
                max-width: 80%;
            }
        }

        @media (orientation: portrait) {
            #signatureCanvas {
                height: 200px;
            }
        }
    </style>
</head>

<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">
    <!-- Formulario para el número de documento, transacción y firma -->
    <div class="w-full max-w-md p-4 bg-white rounded-lg shadow-md mb-6">
        <form action="save_signature.php" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700">Nombre</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div class="mb-4">
                <label for="documentNumber" class="block text-sm font-semibold text-gray-700">Número de Documento</label>
                <input type="text" id="documentNumber" name="documentNumber" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div class="mb-4">
                <label for="transactionNumber" class="block text-sm font-semibold text-gray-700">Número de Transacción</label>
                <input type="text" id="transactionNumber" name="transactionNumber" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>

            <!-- Canvas para firmar -->
            <div id="signatureContainer">
                <canvas id="signatureCanvas"></canvas>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-primary" id="saveSignature">Guardar Firma</button>
                <button type="button" class="btn btn-secondary" id="clearSignature">Borrar Firma</button>
            </div>
        </form>
    </div>

    <!-- Barra de navegación -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg">
        <div class="flex justify-around py-2">
            <a href="../index.php" class="text-blue-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs">Salir</span>
            </a>

            <a href="./buscar_factura_firmada.php" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-xs">Facturas Firmadas</span>
            </a>
        </div>
    </nav>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>
        let signaturePad;

        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('signatureCanvas');
            signaturePad = new SignaturePad(canvas);

            document.getElementById('saveSignature').addEventListener('click', () => {
                if (signaturePad.isEmpty()) {
                    alert('Por favor, dibuje su firma primero.');
                    return;
                }

                const signatureDataUrl = signaturePad.toDataURL();

                // Enviar la firma al servidor
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'save_signature.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.signedPdfUrl) {
                            // Redirigir a la página de búsqueda de facturas firmadas
                            window.location.href = response.signedPdfUrl;
                        } else {
                            alert('Error al firmar la factura: ' + response.error);
                        }
                    } else {
                        alert('Error al conectar con el servidor.');
                    }
                };
                xhr.send('signature=' + encodeURIComponent(signatureDataUrl));
            });

            document.getElementById('clearSignature').addEventListener('click', () => {
                signaturePad.clear();
            });
        });
    </script>
    <script>
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');
    const saveButton = document.getElementById('saveSignature');
    const clearButton = document.getElementById('clearSignature');
    const signatureInput = document.createElement('input');
    signatureInput.type = 'hidden';
    signatureInput.name = 'signature';
    document.forms[0].appendChild(signatureInput);

    // Ajustar el tamaño del canvas
    canvas.width = 400;
    canvas.height = 200;

    let drawing = false;

    // Empezar a dibujar
    canvas.addEventListener('mousedown', (e) => {
        drawing = true;
        ctx.beginPath();
        ctx.moveTo(e.offsetX, e.offsetY);
    });

    // Dibujar en el canvas
    canvas.addEventListener('mousemove', (e) => {
        if (drawing) {
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
        }
    });

    // Detener el dibujo
    canvas.addEventListener('mouseup', () => {
        drawing = false;
    });

    // Guardar la firma en base64
    saveButton.addEventListener('click', () => {
        const signatureData = canvas.toDataURL('image/png');
        signatureInput.value = signatureData;

        // Enviar el formulario
        document.forms[0].submit();
    });

    // Limpiar el canvas
    clearButton.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });
</script>
</body>

</html>