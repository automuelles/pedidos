<?php
// Incluir archivos necesarios
include('../php/login.php');
include('../php/validate_session.php');

// Obtener los valores de los parámetros desde la URL
$transaccion = isset($_GET['IntTransaccion']) ? (int) $_GET['IntTransaccion'] : 0;
$documento = isset($_GET['IntDocumento']) ? (int) $_GET['IntDocumento'] : 0;

if ($transaccion > 0 && $documento > 0) {
    // Conexión a MySQL (automuelles_db) para obtener la factura
    include('../php/db.php'); // Este archivo contiene la conexión a MySQL

    // Consulta SQL para obtener la factura con los parámetros proporcionados
    $sql = "SELECT * FROM factura WHERE IntTransaccion = :transaccion AND IntDocumento = :documento";
    $stmt = $pdo->prepare($sql); // Usamos $pdo porque estamos trabajando con MySQL

    // Vincular los parámetros con los valores
    $stmt->bindParam(':transaccion', $transaccion, PDO::PARAM_INT);
    $stmt->bindParam(':documento', $documento, PDO::PARAM_INT);

    // Ejecutar la consulta
    $stmt->execute();

    // Verificar si la factura fue encontrada
    if ($stmt->rowCount() > 0) {
        // Obtener la factura
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        // Consulta SQL para obtener los detalles de la factura en SQL Server
        $query = "
               SELECT 
                    d.IntTransaccion, 
                    d.IntDocumento, 
                    d.StrProducto,
                    p.StrDescripcion, 
                    p.StrParam1, 
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
        echo "No se encontró la factura con el número de transacción y documento especificados.";
    }
} else {
    echo "ID de transacción o documento inválido.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Factura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilo neumorfismo */
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
  

    <div class="w-full max-w-xs pb-16">
        <div class="w-full max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10 pb-24"> <!-- Agregar pb-24 para dar espacio abajo -->
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Detalles de la Factura</h1>

            <?php
            // Mostrar el número de factura y la transacción
            if (isset($factura['IntTransaccion']) && isset($factura['IntDocumento'])) {
                echo "<h1 class='text-xl font-semibold text-gray-700 mb-4'> " . htmlspecialchars($factura['IntDocumento']) . " -  " . htmlspecialchars($factura['IntTransaccion']) . "</h1>";
            } else {
                echo "<p class='text-red-500'>No se encontraron los datos de la factura.</p>";
            }
            ?>

            <?php
            // Mostrar los detalles de la factura sin agrupar productos
            if ($results) {
                foreach ($results as $factura_detail) {
                    // Mostrar todos los detalles de la factura
                    echo "<input type='checkbox' name='productos[]' value='" . htmlspecialchars($factura_detail['StrProducto']) . "' class='form-checkbox text-blue-500'>";
                    echo "<p class='text-lg text-gray-700'><strong>Cantidad:</strong> " . number_format((float) $factura_detail['IntCantidad'], 2, '.', '') . "</p>";
                    echo "<p class='text-lg text-gray-700'><strong>Producto:</strong> " . htmlspecialchars($factura_detail['StrProducto']) . "</p>";
                    echo "<p class='text-lg text-gray-700'><strong>Descripcion:</strong> " . htmlspecialchars($factura_detail['StrDescripcion']) . "</p>";
                    echo "<p class='text-lg text-gray-700'><strong>Ubicación:</strong> " . htmlspecialchars($factura_detail['StrParam1']) . "</p>";
                    echo "<p class='text-lg text-gray-700'><strong>Vendedor:</strong> " . htmlspecialchars($factura_detail['StrUsuarioGra']) . "</p>";
                    echo "<p class='text-lg text-gray-700'><strong>Observaciones:</strong> " . htmlspecialchars($factura_detail['StrObservaciones']) . "</p>";
                    echo "<hr class='my-4' />";
                }
            } else {
                echo "<p class='text-red-500'>No se encontraron detalles para la factura solicitada.</p>";
            }
            ?>
            <?php
            // Obtener los valores de IntTransaccion e IntDocumento
            $intTransaccion = htmlspecialchars($factura['IntTransaccion']);
            $intDocumento = htmlspecialchars($factura['IntDocumento']);
            ?>
            <form action="NovedadFinal.php?IntTransaccion=<?php echo $intTransaccion; ?>&IntDocumento=<?php echo $intDocumento; ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-4">
                    <label for="vendedor" class="block text-gray-700 text-sm font-bold mb-2">Vendedor</label>
                    <input type="text" id="vendedor" name="vendedor" value="<?php echo htmlspecialchars($factura_detail['StrUsuarioGra']); ?>" class="w-full border border-gray-300 p-2 rounded-md" readonly>
                </div>
                <div class="mb-4">
                    <label for="novedad" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Novedad</label>
                    <select id="novedad" name="novedad" class="w-full border border-gray-300 p-2 rounded-md">
                        <option value="pago en efectivo">pago en efectivo</option>
                        <option value="pago en tranferencia">pago en tranferencia</option>
                        <option value="Credito">Credito</option>
                        <option value="Despachador autoriza entrega (No pago">Despachador autoriza entrega (No pago)</option>
                        <option value="Vendedor autoriza entrega (No pago)">Vendedor autoriza entrega (No pago)</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4" class="w-full border border-gray-300 p-2 rounded-md" placeholder="Ingrese una descripción detallada"></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700">Enviar Reporte</button>
                </div>
            </form>
        </div>
    </div>

    </nav>
</body>

</html>