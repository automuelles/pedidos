<?php
// Incluir archivos necesarios
include('../php/login.php');
include('../php/validate_session.php');

// Obtener los valores de los parámetros desde la URL
$transaccion = isset($_GET['IntTransaccion']) ? (int) $_GET['IntTransaccion'] : 0;
$documento = isset($_GET['IntDocumento']) ? (int) $_GET['IntDocumento'] : 0;

if ($transaccion > 0 && $documento > 0) {
    include('../php/db.php');

    $sql = "SELECT * FROM factura WHERE IntTransaccion = :transaccion AND IntDocumento = :documento";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':transaccion', $transaccion, PDO::PARAM_INT);
    $stmt->bindParam(':documento', $documento, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

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

        $stmt_details = $conn->prepare($query);
        $stmt_details->execute([$transaccion, $documento]);
        $results = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

        $pdo = null;
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
        .neumorphism {
            background: #e0e5ec;
            border-radius: 15px;
            box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
        }
    </style>
</head>
<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6">
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Asignar Mensajero</h1>
    </div>

    <div class="w-full max-w-xs pb-16">
        <div class="w-full max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10 pb-24">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Detalles de la Factura</h1>

            <?php if (isset($factura['IntTransaccion']) && isset($factura['IntDocumento'])): ?>
                <h1 class="text-xl font-semibold text-gray-700 mb-4"><?php echo htmlspecialchars($factura['IntDocumento']) . " - " . htmlspecialchars($factura['IntTransaccion']); ?></h1>
            <?php else: ?>
                <p class="text-red-500">No se encontraron los datos de la factura.</p>
            <?php endif; ?>

            <?php if ($results): ?>
                <?php foreach ($results as $factura_detail): ?>
                    <input type="checkbox" name="productos[]" value="<?php echo htmlspecialchars($factura_detail['StrProducto']); ?>" class="form-checkbox text-blue-500">
                    <p class="text-lg text-gray-700"><strong>Cantidad:</strong> <?php echo number_format((float) $factura_detail['IntCantidad'], 2, '.', ''); ?></p>
                    <p class="text-lg text-gray-700"><strong>Producto:</strong> <?php echo htmlspecialchars($factura_detail['StrProducto']); ?></p>
                    <p class="text-lg text-gray-700"><strong>Descripción:</strong> <?php echo htmlspecialchars($factura_detail['StrDescripcion']); ?></p>
                    <p class="text-lg text-gray-700"><strong>Ubicación:</strong> <?php echo htmlspecialchars($factura_detail['StrParam1']); ?></p>
                    <p class="text-lg text-gray-700"><strong>Vendedor:</strong> <?php echo htmlspecialchars($factura_detail['StrUsuarioGra']); ?></p>
                    <hr class="my-4" />
                <?php endforeach; ?>
                <p class="text-lg text-gray-700"><strong>Observaciones:</strong> <?php echo htmlspecialchars($factura_detail['StrObservaciones']); ?></p>
            <?php else: ?>
                <p class="text-red-500">No se encontraron detalles para la factura solicitada.</p>
            <?php endif; ?>

            <!-- Form with Mensajero Selection -->
            <form method="POST" action="GuardarAsignacionMensajero.php">
                <input type="hidden" name="IntTransaccion" value="<?php echo htmlspecialchars($factura['IntTransaccion']); ?>">
                <input type="hidden" name="IntDocumento" value="<?php echo htmlspecialchars($factura['IntDocumento']); ?>">

                <?php
                include('../php/db.php');
                $sql = "SELECT u.id, u.name FROM users u 
                        INNER JOIN active_sessions s ON u.id = s.user_id 
                        WHERE u.role IN ('mensajeria', 'despachos')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $mensajeros = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <label for="mensajero" class="block text-gray-700 text-sm font-bold mb-2">Seleccionar Mensajero:</label>
                <select id="mensajero" name="mensajero" class="block w-full px-4 py-2 border rounded-lg shadow-sm focus:ring focus:ring-blue-200">
                    <option value="">-- Seleccione un mensajero --</option>
                    <?php foreach ($mensajeros as $mensajero): ?>
                        <option value="<?php echo htmlspecialchars($mensajero['id']); ?>">
                            <?php echo htmlspecialchars($mensajero['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-4">Guardar</button>
            </form>
        </div>
    </div>
</body>
</html>