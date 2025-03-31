<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');

// Conexión a la base de datos
$host = "localhost";
$dbname = "automuelles_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Variables de filtro
$filtro_tercero = $_GET['tercero'] ?? '';
$filtro_transaccion = $_GET['transaccion'] ?? '';
$filtro_documento = $_GET['documento'] ?? '';

// Query base
$query = "SELECT * FROM Notas WHERE estado = 'Realizado'";

// Agregar filtros dinámicamente
$conditions = [];
$params = [];

if (!empty($filtro_tercero)) {
    $conditions[] = "tercero LIKE :tercero";
    $params[':tercero'] = "%$filtro_tercero%";
}

if (!empty($filtro_transaccion)) {
    $conditions[] = "transaccion = :transaccion";
    $params[':transaccion'] = $filtro_transaccion;
}

if (!empty($filtro_documento)) {
    $conditions[] = "documento = :documento";
    $params[':documento'] = $filtro_documento;
}

// Concatenar condiciones a la query
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$notas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <a href="Notas.php" class="text-gray-500 text-center flex flex-col items-center">
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
        <h1 class="text-black-600 text-2xl font-bold">Notas Realizadas</h1>
    </div>
    <div class="p-1 pb-1">
    </div>

    <!-- Diseño del Filtro y Tabla con Tailwind CSS -->
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md pb-20">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Ver Notas en Estado "Realizado"</h2>

        <!-- Formulario de Filtro -->
        <form method="GET" class="mb-6 flex flex-wrap gap-4 items-center justify-center">
            <input type="text" name="tercero" placeholder="Filtrar por Tercero" value="<?= htmlspecialchars($filtro_tercero) ?>" class="p-2 border rounded-lg">
            <input type="number" name="transaccion" placeholder="Filtrar por Transacción" value="<?= htmlspecialchars($filtro_transaccion) ?>" class="p-2 border rounded-lg">
            <input type="number" name="documento" placeholder="Filtrar por Documento" value="<?= htmlspecialchars($filtro_documento) ?>" class="p-2 border rounded-lg">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Filtrar</button>
        </form>

        <!-- Tabla de Resultados -->
        <?php if (!empty($notas)) : ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse bg-white shadow-lg rounded-lg">
                    <thead>
                        <tr class="bg-blue-500 text-white">
                            <th class="px-4 py-2">Tercero</th>
                            <th class="px-4 py-2">Transacción</th>
                            <th class="px-4 py-2">Documento</th>
                            <th class="px-4 py-2">Producto</th>
                            <th class="px-4 py-2">Motivo</th>
                            <th class="px-4 py-2">Usuario</th>
                            <th class="px-4 py-2">Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notas as $nota) : ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="px-4 py-2"><?= htmlspecialchars($nota['tercero']) ?></td>
                                <td class="px-4 py-2 text-center"><?= htmlspecialchars($nota['transaccion']) ?></td>
                                <td class="px-4 py-2 text-center"><?= htmlspecialchars($nota['documento']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($nota['producto']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($nota['motivo']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($nota['usuario']) ?></td>
                                <td class="px-4 py-2 text-center"><?= htmlspecialchars($nota['fecha_registro']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p class="text-center text-red-500 font-semibold mt-4">No hay notas en estado "Realizado".</p>
        <?php endif; ?>
    </div>

</body>

</html>