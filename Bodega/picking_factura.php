<?php
// Incluir archivos necesarios
include('../php/login.php');
include('../php/validate_session.php');
include('AsignarServicios.php');

// Obtener el ID de la factura desde la URL
$factura_id = isset($_GET['factura_id']) ? (int) $_GET['factura_id'] : 0;

if ($factura_id > 0) {
    // Conexión a MySQL (automuelles_db) para obtener la factura
    include('../php/db.php'); // Este archivo contiene la conexión a MySQL

    // Consulta para obtener los datos de la factura con el ID proporcionado en la base de datos MySQL
    $sql = "SELECT * FROM factura WHERE id = :factura_id";
    $stmt = $pdo->prepare($sql); // Usamos $pdo porque estamos trabajando con MySQL

    // Vincular el parámetro con el valor
    $stmt->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);

    // Ejecutar la consulta
    $stmt->execute();

    // Verificar si la factura fue encontrada
    if ($stmt->rowCount() > 0) {
        // Obtener la factura
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtenemos el número de transacción y documento de la factura
        $transaccion = $factura['IntTransaccion'];
        $documento = $factura['IntDocumento'];

        // Consulta SQL para obtener los detalles de la factura en SQL Server
        $query = "
                SELECT 
                    d.IntTransaccion, 
                    d.IntDocumento, 
                    d.StrProducto,
                    p.StrDescripcion, 
                    p.StrParam1, 
                    d.IntBodega,
                    d.IntCantidad, 
                    d.StrUnidad, 
                    d.DatFecha1, 
                    d.StrVendedor,
                    doc.StrObservaciones,
                    doc.StrUsuarioGra, 
                    doc.StrReferencia1,
                    doc.StrReferencia3, 
                    doc.IntTotal
                FROM TblDetalleDocumentos d
                LEFT JOIN TblProductos p ON d.StrProducto = p.StrIdProducto
                LEFT JOIN TblDocumentos doc ON d.IntTransaccion = doc.IntTransaccion AND d.IntDocumento = doc.IntDocumento
                WHERE d.IntTransaccion = ? AND d.IntDocumento = ?
                ORDER BY d.IntDocumento";

        // Preparar y ejecutar la consulta de detalle de la factura
        $stmt_details = $conn->prepare($query); // Usamos la conexión a SQL Server
        $stmt_details->execute([$transaccion, $documento]);

        // Obtener los resultados
        $results = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

        // Cerrar la conexión de MySQL
        $pdo = null;

        // Cerrar la conexión a SQL Server
        $conn = null;
    } else {
        echo "ID de factura inválido.";
    }
} else {
    echo "ID de factura inválido.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal Automuelles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
</head>

<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-lg z-50">
        <div class="flex justify-around py-2">
            <a href="../php/logout_index.php" class="text-blue-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs">Salir</span>
            </a>
            <a href="Bodega.php" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-xs">Volver</span>
            </a>
            <a href="#" id="openModal" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-xs">Apps</span>
            </a>
        </div>
    </nav>
    <div class="w-full max-w-5xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10 pb-24 overflow-x-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Detalles de la Factura</h1>

        <?php
        // Mostrar el número de factura y la transacción
        if (isset($factura['IntTransaccion']) && isset($factura['IntDocumento'])) {
            echo "<h1 class='text-xl font-semibold text-gray-700 mb-4'>" . htmlspecialchars($factura['IntDocumento']) . " - " . htmlspecialchars($factura['IntTransaccion']) . "</h1>";
        } else {
            echo "<p class='text-red-500'>No se encontraron los datos de la factura.</p>";
        }
        ?>

        <?php  if (!empty($results)) {
            $observaciones = htmlspecialchars($results[0]['StrObservaciones'] ?? 'Sin observaciones');
            $vendedor = htmlspecialchars($results[0]['StrUsuarioGra'] ?? 'No especificado');

            echo "<p class='text-lg text-gray-700'><strong>Observaciones:</strong> $observaciones</p>";
            echo "<p class='text-lg text-gray-700'><strong>Vendedor:</strong> $vendedor</p>";
        ?>
            <table class="w-full border-collapse border border-gray-300 text-center mt-4">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2">Seleccionar</th>
                        <th class="border border-gray-300 p-2">Cantidad</th>
                        <th class="border border-gray-300 p-2">Producto</th>
                        <th class="border border-gray-300 p-2">Descripción</th>
                        <th class="border border-gray-300 p-2">Bodega</th>
                        <th class="border border-gray-300 p-2">Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $factura_detail) : ?>
                        <tr>
                            <td class="border border-gray-300 p-2">
                            <input type="checkbox" name="productos[]" value="<?= htmlspecialchars($factura_detail['StrProducto']) ?>" class="form-checkbox text-blue-500" required>
                            </td>
                            <td class="border border-gray-300 p-2"><?= number_format((float) $factura_detail['IntCantidad'], 2, '.', '') ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($factura_detail['StrProducto']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($factura_detail['StrDescripcion']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($factura_detail['IntBodega']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($factura_detail['StrParam1']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Mostrar Observaciones al final de la tabla -->
            <p class="text-lg text-gray-700 mt-4"><strong>Observaciones:</strong> <?= htmlspecialchars($results[0]['StrObservaciones'] ?? 'Sin observaciones') ?></p>

        <?php } else {
            echo "<p class='text-red-500'>No se encontraron detalles para la factura solicitada.</p>";
        } ?>

        <!-- Botones de acción -->
        <div class="mt-6">
            <button type="button"
                class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75"
                onclick="updateEstado()">
                Guardar
            </button>
            <button type="button"
                class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75"
                onclick="reportarNovedad(<?php echo $factura['id']; ?>)">
                Reportar Novedad
            </button>
        </div>
    </div>


    <script>
        // Recargar la página cada 30 segundos
        setInterval(function() {
            location.reload();
        }, 30000); // 30000 milisegundos = 30 segundos
    </script>
    <script>
        function updateEstado() {
            // Obtener el ID de la factura de la URL (dynamic)
            const urlParams = new URLSearchParams(window.location.search);
            const facturaId = urlParams.get('factura_id'); // Obtiene el valor de 'factura_id' de la URL

            if (!facturaId) {
                alert('No se pudo obtener el ID de la factura.');
                return;
            }

            // Crear los datos que se van a enviar
            var data = new FormData();
            data.append("factura_id", facturaId);
            data.append("estado", "picking"); // Cambiar el estado a "picking"

            // Enviar la solicitud al archivo PHP
            fetch('actualizar_estado.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.text()) // Cambiar a .text() para ver la respuesta como texto
                .then(responseText => {
                    console.log(responseText); // Imprimir la respuesta para inspeccionarla
                    try {
                        const data = JSON.parse(responseText); // Intentar convertir a JSON
                        if (data.success) {
                            alert('Estado actualizado a "picking"');
                            window.location.href = 'Bodega.php'; // Redirigir a la página deseada
                        } else {
                            console.error('Error en la actualización:', data.message);
                            alert('Hubo un error al actualizar el estado: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error al analizar la respuesta como JSON:', error);
                        alert('Hubo un error en la solicitud: ' + error.message);
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    alert('Hubo un error en la solicitud: ' + error.message);
                });
        }
    </script>
    <script>
        function reportarNovedad(facturaId) {
            if (!facturaId) {
                alert('No se pudo obtener el ID de la factura.');
                return;
            }
            window.open('ReportesNovedades.php?factura_id=' + facturaId, '_blank');
        }
    </script>
</body>

</html>