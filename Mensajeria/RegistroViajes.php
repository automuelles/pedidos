<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');

// Verificar si el usuario es 'mensajeria'
if ($_SESSION['user_role'] !== 'mensajeria') {
    die("Acceso denegado.");
}

$user_name = $_SESSION['user_name'];

// Query para obtener las facturas que aún no están en facturas_zonificacion
$sql = "SELECT DISTINCT fg.*, f.IntTransaccion, f.IntDocumento
        FROM factura_gestionada fg
        JOIN factura f ON fg.factura_id = f.id
        LEFT JOIN facturas_zonificacion fz ON f.IntTransaccion = fz.transaccion AND f.IntDocumento = fz.documento
        WHERE fg.estado = 'Entregado' 
        AND fg.user_name = ?
        AND fz.transaccion IS NULL";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_name]);

// Obtener los resultados
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
        <h1 class="text-black-600 text-2xl font-bold">Despachos</h1>
    </div>

    <!-- Features Section -->
    <div class="w-full max-w-4xl mx-auto pb-16">
        <h2 class="text-center text-2xl font-semibold text-gray-700 mb-6">Módulos</h2>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <!-- Mensajes de Error o Vacíos -->
            <?php if (isset($errorMessage)) { ?>
                <p class="text-red-500 text-center"><?php echo $errorMessage; ?></p>
            <?php } elseif (empty($facturas)) { ?>
                <p class="text-gray-500 text-center">Ya se realizaron todos los registros</p>
            <?php } else { ?>
                <table class="min-w-full table-auto border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 border-b border-gray-200">Transacción</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 border-b border-gray-200">Número de Factura</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 border-b border-gray-200">Zonificación</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 border-b border-gray-200">Ver</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 border-b border-gray-200">Guardar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($facturas as $factura) { ?>
                            <tr class="hover:bg-gray-50">
                                <!-- Formulario para cada fila -->
                                <form action="guardarViaje.php" method="post">
                                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-200">
                                        <?php echo htmlspecialchars($factura['IntTransaccion']); ?>
                                        <input type="hidden" name="transaccion" value="<?php echo htmlspecialchars($factura['IntTransaccion']); ?>">
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-200">
                                        <?php echo htmlspecialchars($factura['IntDocumento']); ?>
                                        <input type="hidden" name="documento" value="<?php echo htmlspecialchars($factura['IntDocumento']); ?>">
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-200">
                                        <select name="zonificacion" class="p-2 border rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" required>
                                            <option value="">Seleccionar zona</option>
                                            <option value="Norte Cercano">Norte Cercano</option>
                                            <option value="Norte Lejano">Norte Lejano</option>
                                            <option value="Sur Cercano">Sur Cercano</option>
                                            <option value="Sur Lejano">Sur Lejano</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-200">
                                        <a href="ver.php?transaccion=<?= urlencode($factura['IntTransaccion']) ?>&documento=<?= urlencode($factura['IntDocumento']) ?>"
                                            target="_blank" rel="noopener noreferrer"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-lg inline-block">
                                            Ver
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-200">
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-lg">
                                            Guardar
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
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
            <a href="mensajeria.php" class="text-gray-500 text-center flex flex-col items-center">
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
    <script>
        // Recargar la página cada 30 segundos
        setInterval(function() {
            location.reload();
        }, 30000); // 30000 milisegundos = 30 segundos
    </script>
</body>

</html>