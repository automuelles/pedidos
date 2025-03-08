<?php
// Incluir archivos necesarios
include('../php/login.php');
include('../php/validate_session.php');

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
    <div class="w-full max-w-xs pb-16">
        <div class="w-full max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10 pb-24"> <!-- Agregar pb-24 para dar espacio abajo -->
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Detalles de la Factura</h1>

            <?php
            // Mostrar el número de factura y la transacción
            if (isset($factura['IntTransaccion']) && isset($factura['IntDocumento'])) {
                echo "<h1 class='text-xl font-semibold text-gray-700 mb-4'> " . htmlspecialchars($factura['IntDocumento']) . " - " . htmlspecialchars($factura['IntTransaccion']) . "</h1>";
            } else {
                echo "<p class='text-red-500'>No se encontraron los datos de la factura.</p>";
            }
            ?>

            <?php
            // Mostrar los detalles de la factura sin agrupar productos
            if ($results) {
                foreach ($results as $factura_detail) {
                    // Mostrar todos los detalles de la factura
                    // Casilla de verificación
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
            <form action="procesar_reporte.php?factura_id=<?php echo htmlspecialchars($factura_id); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-4">
                    <label for="vendedor" class="block text-gray-700 text-sm font-bold mb-2">Vendedor</label>
                    <input type="text" id="vendedor" name="vendedor" value="<?php echo htmlspecialchars($factura_detail['StrUsuarioGra']); ?>" class="w-full border border-gray-300 p-2 rounded-md" readonly>
                </div>
                <div class="mb-4">
                    <label for="novedad" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Novedad</label>
                    <select id="novedad" name="novedad" class="w-full border border-gray-300 p-2 rounded-md">
                        <option value="sin_inventario">Sin Inventario</option>
                        <option value="mercancia_no_encontrada">Mercancía No Encontrada</option>
                        <option value="ubicacion_cedi">Ubicación Mercancía Cedi</option>
                        <option value="ubicacion_sede_principal">Ubicación Mercancía Sede Principal</option>
                        <option value="ubicacion_ambas_sedes">Ubicación Mercancía Ambas Sedes</option>
                        <option value="ubicacion_ambas_sedes">Mercancia de Mostrador</option>
                        <option value="Referencias_Equivocadas">Mercancia Comprada en el barrio</option>
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
   
</body>

</html>