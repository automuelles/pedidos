<?php
include('../php/login.php');
include('../php/validate_session.php');
require_once '../php/db.php'; // Conexión a la base de datos
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
        <h1 class="text-black-600 text-2xl font-bold">Reportes de pago</h1>
    </div>

    <!-- Tabla de datos -->
    <div class="neumorphism w-full max-w-4xl p-6 mb-6">
        <?php
        try {
            // Consulta SQL para unir las tablas
            $sql = "SELECT 
                        f.IntTransaccion, 
                        f.IntDocumento, 
                        rp.novedad, 
                        rp.descripcion
                    FROM 
                        factura f
                    INNER JOIN 
                        Reporte_pago rp
                    ON 
                        f.IntTransaccion = rp.inttransaccion 
                        AND f.IntDocumento = rp.intdocumento";
            
            // Ejecutar la consulta
            $stmt = $pdo->query($sql);

            // Mostrar los resultados en una tabla HTML
            echo "<table class='min-w-full table-auto border-collapse border border-gray-300'>";
            echo "<thead>";
            echo "<tr class='bg-gray-100 text-gray-700 text-left'>";
            echo "<th class='border border-gray-300 px-4 py-2'>IntTransaccion</th>";
            echo "<th class='border border-gray-300 px-4 py-2'>IntDocumento</th>";
            echo "<th class='border border-gray-300 px-4 py-2'>Novedad</th>";
            echo "<th class='border border-gray-300 px-4 py-2'>Descripción</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody class='text-gray-600'>";
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr class='border-b hover:bg-gray-50'>";
                echo "<td class='border border-gray-300 px-4 py-2'>{$row['IntTransaccion']}</td>";
                echo "<td class='border border-gray-300 px-4 py-2'>{$row['IntDocumento']}</td>";
                echo "<td class='border border-gray-300 px-4 py-2'>{$row['novedad']}</td>";
                echo "<td class='border border-gray-300 px-4 py-2'>{$row['descripcion']}</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } catch (PDOException $e) {
            echo "<p class='text-red-600 font-semibold'>Error al mostrar los datos: " . $e->getMessage() . "</p>";
        }
        ?>
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
            <a href="tesoreria.php" target="_blank" class="text-gray-500 text-center flex flex-col items-center">
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

