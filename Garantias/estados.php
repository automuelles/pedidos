<?php
$required_files = ['../php/db.php', '../php/login.php', '../php/validate_session.php'];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Error: No se pudo cargar el archivo $file.");
    }
    include($file);
}

$results = [];
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nit_cedula'])) {
    $nit_cedula = trim(filter_input(INPUT_POST, 'nit_cedula', FILTER_SANITIZE_SPECIAL_CHARS));
    if (!empty($nit_cedula)) {
        try {
            // Query to search by nit_cedula and fetch associated photos and videos
            $stmt = $pdo->prepare("
                SELECT 
                    er.id AS estado_id,
                    er.reclamo_id,
                    er.nit_cedula,
                    er.estado,
                    er.fecha_actualizacion,
                    r.*,
                    (SELECT GROUP_CONCAT(f.ruta) FROM fotos f WHERE f.reclamo_id = r.id) AS fotos,
                    (SELECT GROUP_CONCAT(v.ruta) FROM videos v WHERE v.reclamo_id = r.id) AS videos
                FROM estado_reclamo er
                INNER JOIN reclamos r ON er.reclamo_id = r.id
                WHERE er.nit_cedula = ?
            ");
            $stmt->execute([$nit_cedula]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) {
                $message = "<p class='text-red-500'>No se encontraron registros para el NIT o cédula proporcionado.</p>";
            }
        } catch (PDOException $e) {
            $message = "<p class='text-red-500'>Error en la consulta: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $message = "<p class='text-red-500'>Por favor, ingrese un NIT o cédula válido.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estados de Reclamos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .neumorphism {
            background: #e0e0e0;
            box-shadow: 10px 10px 20px #bebebe, -10px -10px 20px #ffffff;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-lg z-50">
        <div class="flex justify-around py-2">
            <a href="../php/logout_index.php" class="text-blue-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs">Salir</span>
            </a>
        </div>
    </nav>

    <!-- Header -->
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6 mt-16">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido a Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Formulario de Garantías</h1>
    </div>

    <!-- Formulario -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6 w-full max-w-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Buscar Estado de Reclamo</h2>
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-4">
                <label for="nit_cedula" class="block text-sm font-medium text-gray-600 mb-1">Número de Documento o NIT:</label>
                <input type="text" name="nit_cedula" id="nit_cedula" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Ingrese NIT o cédula" required>
            </div>
            <button type="submit" class="w-full bg-red-500 text-white p-2 rounded-md hover:bg-red-600">Buscar</button>
        </form>
    </div>

    <!-- Resultados -->
    <?php if (!empty($results)): ?>
        <div class="w-full max-w-3xl space-y-6">
            <?php foreach ($results as $row): ?>
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 space-y-4">
                    <h3 class="text-2xl font-bold text-gray-800">Reclamo ID: <?php echo htmlspecialchars($row['reclamo_id']); ?></h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                        <p><strong>Estado:</strong> <?php echo htmlspecialchars($row['estado']); ?></p>
                        <p><strong>Actualización:</strong> <?php echo htmlspecialchars($row['fecha_actualizacion']); ?></p>
                        <p><strong>Referencia Producto:</strong> <?php echo htmlspecialchars($row['referencia_producto']); ?></p>
                        <p><strong>Instalación:</strong> <?php echo htmlspecialchars($row['fecha_instalacion']); ?></p>
                        <p><strong>Falla:</strong> <?php echo htmlspecialchars($row['fecha_fallo']); ?></p>
                        <p><strong>Vehículo:</strong> <?php echo htmlspecialchars($row['marca_vehiculo'] . ' ' . $row['modelo_vehiculo']); ?></p>
                        <p><strong>Chasis:</strong> <?php echo htmlspecialchars($row['chasis']); ?></p>
                        <p><strong>VIN:</strong> <?php echo htmlspecialchars($row['vin']); ?></p>
                        <p><strong>Motor:</strong> <?php echo htmlspecialchars($row['motor']); ?></p>
                        <p><strong>Kilómetros:</strong> <?php echo htmlspecialchars($row['kms_desplazados']); ?> km</p>
                        <p><strong>Terreno:</strong> <?php echo htmlspecialchars($row['tipo_terreno']); ?></p>
                        <p class="col-span-2"><strong>Detalle Falla:</strong> <?php echo nl2br(htmlspecialchars($row['detalle_falla'])); ?></p>
                    </div>

                    <!-- Fotos -->
                    <?php if (!empty($row['fotos'])): ?>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Fotos:</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <?php foreach (explode(',', $row['fotos']) as $foto): ?>
                                    <div class="rounded overflow-hidden border border-gray-300">
                                        <img src="<?php echo htmlspecialchars($foto); ?>" class="object-cover w-full h-40">
                                        <div class="p-2 text-center">
                                            <a href="<?php echo htmlspecialchars($foto); ?>" download class="text-blue-600 hover:underline text-sm">Descargar</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Videos -->
                    <?php if (!empty($row['videos'])): ?>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2 mt-4">Videos:</h4>
                            <div class="space-y-4">
                                <?php foreach (explode(',', $row['videos']) as $video): ?>
                                    <div class="rounded border border-gray-300 p-2 bg-gray-50">
                                        <video controls class="w-full rounded">
                                            <source src="<?php echo htmlspecialchars($video); ?>" type="video/mp4">
                                            Tu navegador no soporta el video.
                                        </video>
                                        <div class="mt-1 text-center">
                                            <a href="<?php echo htmlspecialchars($video); ?>" download class="text-blue-600 hover:underline text-sm">Descargar</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>

</html>