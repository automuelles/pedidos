<?php
include('../php/login.php');
include('../php/validate_session.php');
require '../php/db.php';

// Fetch all records from reclamos with their associated estado_reclamo
try {
    $stmt = $pdo->prepare("
        SELECT 
            r.*, 
            er.estado, 
            er.fecha_actualizacion,
            (SELECT GROUP_CONCAT(f.ruta) FROM fotos f WHERE f.reclamo_id = r.id) AS fotos,
            (SELECT GROUP_CONCAT(v.ruta) FROM videos v WHERE v.reclamo_id = r.id) AS videos
        FROM reclamos r
        LEFT JOIN (
            SELECT reclamo_id, estado, fecha_actualizacion
            FROM estado_reclamo
            WHERE (reclamo_id, fecha_actualizacion) IN (
                SELECT reclamo_id, MAX(fecha_actualizacion)
                FROM estado_reclamo
                GROUP BY reclamo_id
            )
        ) er ON r.id = er.reclamo_id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute();
    $reclamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
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

        /* Table styling */
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-lg z-50">
        <div class="flex justify-around py-2">
            <a href="../php/logout_index.php" class="text-blue-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs">Salir</span>
            </a>

            <a href="#" id="openModal" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-xs">Apps</span>
            </a>
        </div>
    </nav>

    <!-- Header -->
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6 mt-16">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            </h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Historico De Solicitudes</h1>
    </div>

    <!-- Table to display reclamos with estado -->
    <div class="neumorphism w-full max-w-4xl p-6 table-container">
        <table class="min-w-full bg-white rounded-lg overflow-hidden">
            <thead class="bg-gray-100 sticky top-0">
                <tr>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">NIT/Cédula</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Nombre Cliente</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Vendedor</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Referencia Producto</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Fecha Instalación</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Fecha Fallo</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Marca Vehículo</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Modelo Vehículo</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Chasis</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">VIN</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Motor</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Kms Desplazados</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Tipo Terreno</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Detalle Falla</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Estado</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Creado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reclamos)): ?>
                    <?php foreach ($reclamos as $reclamo): ?>
                        <tr class="border-b">
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['nit_cedula']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['nombre_cliente']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['vendedor']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['referencia_producto']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['fecha_instalacion']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['fecha_fallo']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['marca_vehiculo']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['modelo_vehiculo']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['chasis']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['vin']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['motor']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['kms_desplazados']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['tipo_terreno']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['detalle_falla']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['estado'] ?? 'Sin estado'); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($reclamo['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="18" class="py-2 px-4 text-sm text-gray-600 text-center">No hay solicitudes registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>