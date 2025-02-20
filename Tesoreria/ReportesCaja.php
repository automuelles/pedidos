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

    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6">
    <h2 class="text-black-600 text-xl font-bold">Ingresar Reporte de Pago</h2>
    <form action="guardarReportePago.php" method="POST" class="space-y-4">
        <div>
            <label for="inttransaccion" class="block text-left">Transacción:</label>
            <input type="number" name="inttransaccion" id="inttransaccion" required
                class="w-full p-2 rounded border border-gray-300">
        </div>
        <div>
            <label for="intdocumento" class="block text-left">Documento:</label>
            <input type="number" name="intdocumento" id="intdocumento" required
                class="w-full p-2 rounded border border-gray-300">
        </div>
        <div>
    <label for="novedad" class="block text-left">Novedad:</label>
    <select name="novedad" id="novedad" required class="w-full p-2 rounded border border-gray-300">
        <option value="pago en efectivo">Pago en efectivo</option>
        <option value="pago en transferencia">Pago en transferencia</option>
        <option value="Pago Parcial">Pago Parcial</option>
    </select>
</div>
        <div>
            <label for="descripcion" class="block text-left">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4"
                class="w-full p-2 rounded border border-gray-300"></textarea>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Guardar Reporte
        </button>
    </form>
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
            <a href="tesoreria.php" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-xs">Volver</span>
            </a>
            <a href="#" id="openModal" class ="text-gray-500 text-center flex flex-col items-center">
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