<?php
include('../php/login.php');
include('../php/validate_session.php');
include('GuardarFactura.php');
// Obtener los datos de la URL
$transaccion = isset($_GET['transaccion']) ? htmlspecialchars($_GET['transaccion']) : null;
$documento = isset($_GET['documento']) ? htmlspecialchars($_GET['documento']) : null;

$query = "SELECT f.id, f.IntTransaccion, f.IntDocumento, f.estado AS factura_estado, f.fecha AS factura_fecha,
                 e.estado AS estado_actual, e.fecha AS estado_fecha, e.user_name
          FROM factura f
          LEFT JOIN estado e ON f.id = e.factura_id
          WHERE DATE(f.fecha) = CURDATE()";

$params = [];
if (!empty($transaccion)) {
    $query .= " AND f.IntTransaccion = ?";
    $params[] = $transaccion;
}
if (!empty($documento)) {
    $query .= " AND f.IntDocumento = ?";
    $params[] = $documento;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- Header -->
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Pedidos Gestionados</h1>
    </div>

  <!-- Features Section -->
<div class="w-full max-w-4xl mx-auto pb-16">
<form method="GET" class="mb-6 flex space-x-4">
            <input type="text" name="transaccion" placeholder="Transacción" value="<?= htmlspecialchars($transaccion) ?>" class="p-2 border rounded w-full">
            <input type="text" name="documento" placeholder="Documento" value="<?= htmlspecialchars($documento) ?>" class="p-2 border rounded w-full">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filtrar</button>
        </form>

        <table class="w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Transacción</th>
                    <th class="border p-2">Documento</th>
                    <th class="border p-2">Estado Factura</th>
                    <th class="border p-2">Fecha Creacion</th>
                    <th class="border p-2">Último Estado</th>
                    <th class="border p-2">Fecha Cambio Estado</th>
                    <th class="border p-2">Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $factura): ?>
                    <tr class="bg-white border">
                        <td class="border p-2"><?= htmlspecialchars($factura['IntTransaccion']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($factura['IntDocumento']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($factura['factura_estado']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($factura['factura_fecha']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($factura['estado_actual'] ?? 'N/A') ?></td>
                        <td class="border p-2"><?= htmlspecialchars($factura['estado_fecha'] ?? 'N/A') ?></td>
                        <td class="border p-2"><?= htmlspecialchars($factura['user_name'] ?? 'N/A') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    
</div>

    <!-- Footer Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg">
        <div class="flex justify-around py-2">
            <a href="../php/logout_index.php" class="text-blue-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs">Salir</span>
            </a>
            <a href="Despachos.php" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
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
</body>

</html>