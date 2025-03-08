<?php
include('../php/login.php');
include('../php/validate_session.php');
include('../php/db.php');

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
    </div>

    <!-- Features Section -->
    <div class="w-full max-w-3xl pb-16 px-4 mx-auto">
        <?php
        try {
            // Consulta para obtener los registros donde user_id o user_name coincidan
            $sql = "
            SELECT DISTINCT e.*, f.IntTransaccion, f.IntDocumento
            FROM estado e
            INNER JOIN factura f ON e.factura_id = f.id
            WHERE e.user_id = :user_id OR e.user_name = :user_name
        ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
            $stmt->execute();

            // Almacena los resultados
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($registros) > 0) {
                echo "<h2 class='text-xl font-semibold text-gray-800 mb-4 text-center'> Estos son los pedidos y gestiones realizados el día de hoy <?php echo htmlspecialchars($user_id); ?></h2>";
                echo "<table class='min-w-full bg-white shadow-md rounded-lg overflow-hidden'>
                    <thead>
                        <tr class='bg-gray-100'>
                            <th class='px-4 py-2 border-b text-left text-sm text-gray-600'>IntTransaccion</th>
                            <th class='px-4 py-2 border-b text-left text-sm text-gray-600'>IntDocumento</th>
                            <th class='px-4 py-2 border-b text-left text-sm text-gray-600'>Estado</th>
                            <th class='px-4 py-2 border-b text-left text-sm text-gray-600'>Fecha</th>
                            <th class='px-4 py-2 border-b text-left text-sm text-gray-600'>User Name</th>
                        </tr>
                    </thead>
                    <tbody>";

                    foreach ($registros as $registro) {
                        // Reemplazar 'gestionado' por 'asignado' en el estado
                        $estado = str_replace('gestionado', 'asignado', $registro['estado']);
                        echo "<tr class='border-b hover:bg-gray-50'>
                            <td class='px-4 py-2 text-sm text-gray-700'>" . htmlspecialchars($registro['IntTransaccion']) . "</td>
                            <td class='px-4 py-2 text-sm text-gray-700'>" . htmlspecialchars($registro['IntDocumento']) . "</td>
                            <td class='px-4 py-2 text-sm text-gray-700'>" . htmlspecialchars($estado) . "</td>
                            <td class='px-4 py-2 text-sm text-gray-700'>" . htmlspecialchars($registro['fecha']) . "</td>
                            <td class='px-4 py-2 text-sm text-gray-700'>" . htmlspecialchars($registro['user_name']) . "</td>
                        </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p class='text-gray-600'>No se encontraron registros para $user_name.</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='text-red-500'>Error al obtener los registros: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
    <script>
        // Recargar la página cada 30 segundos
        setInterval(function() {
            location.reload();
        }, 30000); // 30000 milisegundos = 30 segundos
    </script>
</body>

</html>