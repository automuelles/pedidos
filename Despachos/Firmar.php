<?php
// Conexión a SQL Server
$serverName = "SERVAUTOMUELLES\SQLDEVELOPER";
$connectionOptions = array(
    "Database" => "AutomuellesDiesel1",
    "Uid" => "Hgi",
    "PWD" => "Hgi"
);

// Establecer conexión con PDO
try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=AutomuellesDiesel1", $connectionOptions["Uid"], $connectionOptions["PWD"]);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Inicializar variables
$documentos = [];
$terceros = [];

// Comprobamos si se han enviado los datos por GET (desde la URL)
if (isset($_GET['IntTransaccion']) && isset($_GET['IntDocumento'])) {
    $IntTransaccion = (int) $_GET['IntTransaccion'];
    $IntDocumento = (int) $_GET['IntDocumento'];

    // Consulta principal para obtener datos de TblDocumentos
    $sql = "
    SELECT 
        D.[IntEmpresa], D.[IntTransaccion], D.[IntDocumento], D.[DatFecha], D.[DatVencimiento], 
        D.[StrTercero], D.[IntValor], D.[IntSubtotal], D.[IntIva], D.[IntTotal], D.[StrReferencia2],
        T.[StrNombre], T.[StrDireccion], T.[StrTelefono], T.[StrIdTercero],
        dp.[StrProducto], p.[StrDescripcion], dp.[IntCantidad], dp.[IntValorUnitario]
    FROM [AutomuellesDiesel1].[dbo].[TblDocumentos] D
    JOIN [AutomuellesDiesel1].[dbo].[TblTerceros] T
        ON D.StrTercero = T.StrIdTercero
    LEFT JOIN [AutomuellesDiesel1].[dbo].[TblDetalleDocumentos] dp
        ON D.IntTransaccion = dp.IntTransaccion AND D.IntDocumento = dp.IntDocumento
    LEFT JOIN [AutomuellesDiesel1].[dbo].[TblProductos] p
        ON dp.StrProducto = p.StrIdProducto
    WHERE D.IntTransaccion = :IntTransaccion
      AND D.IntDocumento = :IntDocumento";

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':IntTransaccion', $IntTransaccion, PDO::PARAM_INT);
    $stmt->bindParam(':IntDocumento', $IntDocumento, PDO::PARAM_INT);
    $stmt->execute();
    $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura Electrónica</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<style>
    /* Neumorphism effect */
    .neumorphism {
        background: #e0e5ec;
        border-radius: 15px;
        box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
    }

    .neumorphism-icon {
        box-shadow: 6px 6px 12px #bebebe, -6px -6px 12px #ffffff;
    }
</style>

<body class="bg-gray-100 font-sans">
    <div class="flex justify-center">
        <div class="w-full max-w-3xl pb-16">

            <div class="container mx-auto mt-10 p-5 bg-white rounded-lg shadow-md">
                <?php if (!empty($documentos)): ?>
                    <div class="text-center mb-5">
                        <h1 class="text-3xl font-bold">COMPROBANTE DE ENTREGA</h1>
                        <p class="text-gray-700">Automuelles Diesel SAS</p>
                        <p class="text-gray-600">NIT: 811021438-4</p>
                        <p class="text-gray-600">Dirección: Cra 61 # 45-04 Medellin (Antioquia)</p>
                        <p class="text-gray-600">Teléfono: 4483179</p>
                        <p class="text-gray-600">Email: automuellesdiesel@outlook.com</p>
                    </div>
                    <div class="mt-10 w-full">
                        <p><span class="font-semibold">Transacción:</span> <?= htmlspecialchars($documentos[0]['IntTransaccion'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><span class="font-semibold">Número de Factura:</span> <?= htmlspecialchars($documentos[0]['IntDocumento'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><span class="font-semibold">PLACA:</span> <?= htmlspecialchars($documentos[0]['StrReferencia2'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2">Detalles del Cliente</h3>
                        <p><span class="font-semibold">Nit/Ced:</span> <?= htmlspecialchars($documentos[0]['StrIdTercero'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><span class="font-semibold">Nombre:</span> <?= htmlspecialchars($documentos[0]['StrNombre'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><span class="font-semibold">Dirección:</span> <?= htmlspecialchars($documentos[0]['StrDireccion'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><span class="font-semibold">Teléfono:</span> <?= htmlspecialchars($documentos[0]['StrTelefono'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="mt-10 w-full">
                        <table class="w-full border-collapse border border-gray-300 text-center table-fixed">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 p-2">Fecha</th>
                                    <th class="border border-gray-300 p-2">Vencimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 p-2"><?= date('d/m/Y', strtotime($documentos[0]['DatFecha'])) ?></td>
                                    <td class="border border-gray-300 p-2"><?= date('d/m/Y', strtotime($documentos[0]['DatVencimiento'])) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Detalles de la factura -->
                    <div class="mt-10 w-full">
                        <h3 class="text-xl font-bold mb-2">Detalles de la Factura</h3>
                        <table class="w-full border-collapse border border-gray-300 text-center table-fixed">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 p-2">Subtotal</th>
                                    <th class="border border-gray-300 p-2">IVA</th>
                                    <th class="border border-gray-300 p-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 p-2"><?= number_format($documentos[0]['IntSubtotal'], 2) ?></td>
                                    <td class="border border-gray-300 p-2"><?= number_format($documentos[0]['IntIva'], 2) ?></td>
                                    <td class="border border-gray-300 p-2"><?= number_format($documentos[0]['IntTotal'], 2) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Listado de ítems -->
                    <div class="mt-10">
                        <h3 class="text-xl font-bold mb-2">Listado de Ítems</h3>
                        <table class="w-full border-collapse border border-gray-300 text-center">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 p-2">Producto</th>
                                    <th class="border border-gray-300 p-2">Descripción</th>
                                    <th class="border border-gray-300 p-2">Cantidad</th>
                                    <th class="border border-gray-300 p-2">Valor Unitario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documentos as $documento): ?>
                                    <?php if (!empty($documento['StrProducto'])): ?>
                                        <tr>
                                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($documento['StrProducto']) ?></td>
                                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($documento['StrDescripcion']) ?></td>
                                            <td class="border border-gray-300 p-2"><?= number_format($documento['IntCantidad'], 2) ?></td>
                                            <td class="border border-gray-300 p-2"><?= number_format($documento['IntValorUnitario'], 2) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer -->
                    <div class="mt-10 text-gray-700">
                        <p>Canales autorizados para el pago: solo en las cuentas bancarias:</p>
                        <p>AHORROS BANCOLOMBIA #46257223761</p>
                        <p>CORRIENTE BANCO DE BOGOTA #434380127</p>
                        <p>Enviar soporte al correo: auxiliar.conta@automuellesdiesel.com o al WSP 3184010693</p>
                    </div>

                    <!-- Sección para la firma -->
                    <div class="mt-10">
                        <h3 class="text-xl font-bold mb-2">Firma del Cliente</h3>
                        <p class="text-gray-600 mb-2">Por favor, firme en el cuadro a continuación:</p>
                        <canvas id="signaturePad" class="border border-gray-400 w-full h-48"></canvas>
                        <button type="button" class="mt-3 px-4 py-2 bg-gray-500 text-white rounded" onclick="clearSignature()">Borrar Firma</button>
                    </div>

                    <!-- Botón para guardar -->
                    <button id="saveSignatureBtn" class="mt-5 px-4 py-2 bg-blue-600 text-white rounded">Guardar Documento</button>
                    <!-- Botón para ver el PDF -->
                    <?php if (!empty($documentos)): ?>
                        <button type="button"
                            class="mt-5 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                            onclick="window.open('../Firma/factura_firmada/<?php echo $IntTransaccion . '-' . $IntDocumento; ?>.pdf', '_blank')">
                            Ver PDF
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-center text-gray-600">No se encontraron datos para la transacción y documento especificados.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let canvas = document.getElementById('signaturePad');
        let ctx = canvas.getContext('2d');
        let drawing = false;

        // Ajustar el tamaño del canvas
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;

        // Manejo de eventos para mouse y touch
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchend', stopDrawing);
        canvas.addEventListener('touchmove', draw);

        function startDrawing(event) {
            event.preventDefault();
            drawing = true;
            ctx.beginPath();
            const {
                offsetX,
                offsetY
            } = getEventPosition(event);
            ctx.moveTo(offsetX, offsetY);
        }

        function stopDrawing(event) {
            event.preventDefault();
            drawing = false;
            ctx.closePath();
        }

        function draw(event) {
            event.preventDefault();
            if (!drawing) return;
            const {
                offsetX,
                offsetY
            } = getEventPosition(event);
            ctx.lineTo(offsetX, offsetY);
            ctx.stroke();
        }

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function getEventPosition(event) {
            let rect = canvas.getBoundingClientRect();
            let x, y;
            if (event.touches) {
                x = event.touches[0].clientX - rect.left;
                y = event.touches[0].clientY - rect.top;
            } else {
                x = event.offsetX;
                y = event.offsetY;
            }
            return {
                offsetX: x,
                offsetY: y
            };
        }

        // Guardar la firma
        document.getElementById('saveSignatureBtn').addEventListener('click', function() {
            let signatureData = canvas.toDataURL('image/png');

            // Recoger los datos que necesitas enviar
            let transactionData = {
                IntTransaccion: <?= json_encode($documentos[0]['IntTransaccion'] ?? '') ?>,
                IntDocumento: <?= json_encode($documentos[0]['IntDocumento'] ?? '') ?>,
                StrReferencia2: <?= json_encode($documentos[0]['StrReferencia2'] ?? '') ?>,
                StrIdTercero: <?= json_encode($documentos[0]['StrIdTercero'] ?? '') ?>,
                StrNombre: <?= json_encode($documentos[0]['StrNombre'] ?? '') ?>,
                StrDireccion: <?= json_encode($documentos[0]['StrDireccion'] ?? '') ?>,
                StrTelefono: <?= json_encode($documentos[0]['StrTelefono'] ?? '') ?>,
                DatFecha: <?= json_encode(isset($documentos[0]['DatFecha']) ? date('d/m/Y', strtotime($documentos[0]['DatFecha'])) : '') ?>,
                DatVencimiento: <?= json_encode(isset($documentos[0]['DatVencimiento']) ? date('d/m/Y', strtotime($documentos[0]['DatVencimiento'])) : '') ?>,
                IntValor: <?= json_encode($documentos[0]['IntValor'] ?? '') ?>,
                IntSubtotal: <?= json_encode($documentos[0]['IntSubtotal'] ?? '') ?>,
                IntIva: <?= json_encode($documentos[0]['IntIva'] ?? '') ?>,
                IntTotal: <?= json_encode($documentos[0]['IntTotal'] ?? '') ?>,
                items: <?= !empty($documentos) ? json_encode($documentos) : '[]' ?>,
                signature: signatureData
            };

            // Enviar la firma y los datos al servidor mediante POST
            fetch('../Firma/guardar_firma.php', {
                    method: 'POST',
                    body: JSON.stringify(transactionData),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('El documento y la firma se han guardado correctamente. Archivo: ' + data.file);
                    } else {
                        alert('Hubo un error al guardar el documento: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error en la conexión: ' + error);
                });
        });
    </script>
</body>

</html>