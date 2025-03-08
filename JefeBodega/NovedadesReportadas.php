<?php
include('../php/login.php');
include('../php/validate_session.php');

// Obtener los datos de la URL
$transaccion = isset($_GET['transaccion']) ? htmlspecialchars($_GET['transaccion']) : null;
$documento = isset($_GET['documento']) ? htmlspecialchars($_GET['documento']) : null;

$query = "SELECT f.id, f.IntTransaccion, f.IntDocumento, f.estado AS factura_estado, f.fecha AS factura_fecha,
                 n.novedad, n.descripcion, n.fecha AS novedad_fecha, n.estado AS novedad_estado, 
                 s.user_name
          FROM factura f
          INNER JOIN Novedades_Bodega n ON f.id = n.factura_id
          INNER JOIN active_sessions s ON n.user_id = s.user_id
          WHERE 1";

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
    <!-- Header -->
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Novedades Reportadas</h1>
    </div>

  <!-- Features Section -->
  <div class="w-full max-w-4xl mx-auto pb-16">
    <table class="w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Transacción</th>
                <th class="border p-2">Documento</th>
                <th class="border p-2">Usuario</th>
                <th class="border p-2">Novedad</th>
                <th class="border p-2">Descripción</th>
                <th class="border p-2">Fecha Novedad</th>
                <th class="border p-2">Estado</th>
                <th class="border p-2"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($facturas as $factura): ?>
                <tr class="bg-white border">
                    <td class="border p-2"><?= htmlspecialchars($factura['IntTransaccion']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($factura['IntDocumento']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($factura['user_name'] ?? 'N/A') ?></td>
                    <td class="border p-2"><?= htmlspecialchars($factura['novedad'] ?? 'N/A') ?></td>
                    <td class="border p-2"><?= htmlspecialchars($factura['descripcion'] ?? 'N/A') ?></td>
                    <td class="border p-2"><?= htmlspecialchars($factura['novedad_fecha'] ?? 'N/A') ?></td>
                    <td class="border p-2"><?= htmlspecialchars($factura['novedad_estado'] ?? 'N/A') ?></td>
                    <td class="border p-2">
                        <a href="#?id=<?= htmlspecialchars($factura['id']) ?>" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-700">
                            Gestionar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>

</html>