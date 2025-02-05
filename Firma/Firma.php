<?php
session_start();

require_once('fpdi/src/autoload.php');
require_once('fpdf/fpdf.php');

use setasign\Fpdi\Fpdi;

// Función para obtener el nombre del usuario desde la sesión
function obtenerNombreDeUsuario()
{
    return isset($_SESSION['usuario']) ? strtoupper($_SESSION['usuario']) : "ADMIN";
}

// Función para buscar una factura en la carpeta compartida
function buscarFactura($numeroFactura)
{
    $carpetaCompartida = 'Z:\\';
    $archivo = $carpetaCompartida . $numeroFactura . '.pdf';

    if (file_exists($archivo)) {
        $contenido = file_get_contents($archivo);
        return base64_encode($contenido);
    } else {
        return false;
    }
}

// Función para firmar una factura
function firmarFactura($numeroFactura, $firmaImagen)
{
    $carpetaCompartida = 'z:\\';
    $archivo = $carpetaCompartida . $numeroFactura . '.pdf';
    $pdfFirmadoPath = $carpetaCompartida . $numeroFactura . '_firmado.pdf';

    if (file_exists($archivo)) {
        $pdf = new Fpdi();
        $pdf->AddPage();
        $pdf->setSourceFile($archivo);
        $templateId = $pdf->importPage(1);
        $pdf->useTemplate($templateId);

        // Añadir información del usuario al documento
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(10, 10);
        $pdf->Write(0, 'Firmado por: ' . obtenerNombreDeUsuario());

        // Agregar la imagen de la firma
        $pdf->Image($firmaImagen, 10, 250, 100);

        $pdf->Output('F', $pdfFirmadoPath);

        return base64_encode(file_get_contents($pdfFirmadoPath));
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Facturas</title>
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .modal-dialog {
            max-width: 100%;
            width: 100%;
            height: 90vh;
            margin: 0;
        }

        .modal-body {
            padding: 0;
            overflow: auto;
            max-height: 90vh;
        }

        #pdfPreview {
            width: 100%;
            height: 80vh;
        }

        .firma-icon {
            font-size: 24px;
            cursor: pointer;
            color: #007bff;
            margin-left: 10px;
        }

        .modal-dialog {
            max-width: 100%;
            width: 100%;
            height: 90vh;
            margin: 0;
        }

        .neumorphism {
            background: #e0e5ec;
            border-radius: 15px;
            box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
        }

        .neumorphism-icon {
            box-shadow: 6px 6px 12px #bebebe, -6px -6px 12px #ffffff;
        }
    </style>

</head>

<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">
    <!-- Header -->
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Firmar Factura</h1>
    </div>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <form id="buscarFacturaForm" method="get" action="">
                    <div class="mb-3">
                        <label for="numeroFactura" class="form-label"></label>
                        <input type="text" class="form-control" name="numeroFactura" id="numeroFactura" aria-describedby="facturaHelp">
                        <div id="facturaHelp" class="form-text">Ingresa el número de la factura (ejemplo: 40-15300)</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
                <hr>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar el PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Vista previa de la Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfPreview" type="application/pdf" frameborder="0"></iframe>
                    <p>El visor PDF no es compatible con su navegador. Puede descargar el archivo PDF usando el siguiente enlace: <a id="pdfDownloadLink" href="#" target="_blank">Descargar PDF</a></p>
                    <p><a id="pdfViewLink" href="verPdf.php?numeroFactura=12345" target="_blank">Ver PDF</a></p>
                    <span class="firma-icon" id="firmarIcon" title="Firmar documento"><i class="fas fa-signature"></i></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para dibujar firma -->
    <div class="modal fade" id="drawSignatureModal" tabindex="-1" aria-labelledby="drawSignatureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="drawSignatureModalLabel">Dibujar Firma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <canvas id="signatureCanvas" width="600" height="200" style="border: 1px solid #000;"></canvas>
                    <div class="mt-3">
                        <button class="btn btn-primary" id="saveSignature">Guardar Firma</button>
                        <button class="btn btn-secondary" id="clearSignature">Borrar Firma</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg">
        <div class="flex justify-around py-2">
            <a href="../php/logout_index.php" class="text-blue-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs">Salir</span>
            </a>

            <a href="./buscar_factura_firmada.php" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-xs">Facturas Firmadas</span>
            </a>
        </div>
        <nav>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
            <script>
                <?php
                if (isset($_GET['numeroFactura'])) {
                    $numeroFactura = $_GET['numeroFactura'];
                    $archivoBase64 = buscarFactura($numeroFactura);
                    $archivoUrl = "data:application/pdf;base64," . $archivoBase64;

                    if ($archivoBase64) {
                        echo "document.getElementById('pdfPreview').src = '$archivoUrl';
                document.getElementById('pdfDownloadLink').href = '$archivoUrl';
                var myModal = new bootstrap.Modal(document.getElementById('pdfModal'), { backdrop: 'static' });
                myModal.show();";

                        // Mostrar el ícono de firma
                        echo "document.getElementById('firmarIcon').style.display = 'inline';";

                        // Mostrar el modal para dibujar la firma
                        echo "document.getElementById('firmarIcon').onclick = function() {
                    var drawSignatureModal = new bootstrap.Modal(document.getElementById('drawSignatureModal'), { backdrop: 'static' });
                    drawSignatureModal.show();
                };";
                    } else {
                        echo "alert('Factura no encontrada.');";
                    }
                }
                ?>
            </script>
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
                        const numeroFactura = new URLSearchParams(window.location.search).get('numeroFactura');

                        // Enviar la firma al servidor
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'save_signature.php?numeroFactura=' + numeroFactura, true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.send('signature=' + encodeURIComponent(signatureDataUrl));
                    });

                    document.getElementById('clearSignature').addEventListener('click', () => {
                        signaturePad.clear();
                    });
                });
            </script>
</body>

</html>