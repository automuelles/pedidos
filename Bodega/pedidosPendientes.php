<?php
include('../php/login.php');
include('../php/validate_session.php');
include('GuardarFactura.php');
include('AsignarServicios.php');
// Obtener los datos de la URL
$transaccion = isset($_GET['transaccion']) ? htmlspecialchars($_GET['transaccion']) : null;
$documento = isset($_GET['documento']) ? htmlspecialchars($_GET['documento']) : null;
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
        <h1 class="text-blue-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-green-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-red-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-red-600 text-2xl font-bold">Pedidos Pendientes</h1>
    </div>

  <!-- Features Section -->
<div class="w-full max-w-4xl mx-auto">
    <h2 class="text-center text-lg font-semibold text-gray-700 mb-6">Pedidos Asignados sin Revisión</h2>
    
    <?php if ($facturas): ?>
        <div class="space-y-4">
            <?php foreach ($facturas as $factura): ?>
                <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-md border border-gray-200">
                    <div>
                        <p class="text-lg font-medium text-gray-800">Transacción: <?php echo htmlspecialchars($factura['IntTransaccion']); ?></p>
                        <p class="text-sm text-gray-600">Documento: <?php echo htmlspecialchars($factura['IntDocumento']); ?></p>
                        <p class="text-xs text-gray-500">Fecha: <?php echo htmlspecialchars($factura['fecha']); ?></p>
                    </div>
                    <div>
                    <form action="picking_factura.php" method="GET">
                            <input type="hidden" name="factura_id" value="<?php echo $factura['factura_id']; ?>">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                                Gestionar
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-500">No hay pedidos asignados sin revisión.</p>
    <?php endif; ?>
</div>

    <!-- Footer Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg">
        <div class="flex justify-around py-2">
            <a href="../php/logout_user.php" class="text-blue-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs">Salir</span>
            </a>
            <a href="Bodega.php" class="text-gray-500 text-center flex flex-col items-center">
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