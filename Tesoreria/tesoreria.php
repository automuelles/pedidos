<?php
include('../php/login.php');
include('../php/validate_session.php');
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
        <h1 class="text-black-600 text-2xl font-bold">Tesoreria</h1>
    </div>

    <!-- Features Section -->
    <div class="w-full max-w-xs pb-16">
        <h2 class="text-center text-lg font-semibold text-gray-700 mb-4">Modulos</h2>
        <div class="grid grid-cols-3 gap-4">
            <div class="neumorphism p-4 text-center">
                <!-- Icono de vendedor -->
                <div
                    class="neumorphism-icon w-10 h-10 bg-yellow-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fa-solid fa-user text-white"></i>
                </div>
                <!-- Etiqueta como enlace -->
                <a href="HistorialFacturas.php" class="text-sm text-gray-700 hover:underline">Historial Facturas</a>
            </div>
            <div class="neumorphism p-4 text-center">
                <!-- Icono de Bodega -->
                <div
                    class="neumorphism-icon w-10 h-10 bg-orange-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fa-solid fa-shop text-white"></i>
                </div>
                <!-- Etiqueta como enlace -->
                <a href="FacturaContado.php" class="text-sm text-gray-700 hover:underline">Facturas de Contado</a>
            </div>
            <div class="neumorphism p-4 text-center">
                <!-- Icono de Bodega -->
                <div
                    class="neumorphism-icon w-10 h-10 bg-green-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fa-solid fa-motorcycle text-white"></i>
                </div>
                <!-- Etiqueta como enlace -->
                <a href="FacturasCredito.php" class="text-sm text-gray-700 hover:underline">Facturas de Credito</a>
            </div>
            <div class="neumorphism p-4 text-center">
                <!-- Icono de Bodega -->
                <div
                    class="neumorphism-icon w-10 h-10 bg-red-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fa-solid fa-lock text-white"></i>
                </div>
                <!-- Etiqueta como enlace -->
                <a href="#" class="text-sm text-gray-700 hover:underline">#</a>
            </div>
            <div class="neumorphism p-4 text-center">
                <!-- Icono de Bodega -->
                <div
                    class="neumorphism-icon w-10 h-10 bg-purple-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fa-solid fa-car text-white"></i>
                </div>
                <!-- Etiqueta como enlace -->
                <a href="#" class="text-sm text-gray-700 hover:underline">#</a>
            </div>
            <div class="neumorphism p-4 text-center">
                <!-- Icono de Bodega -->
                <div
                    class="neumorphism-icon w-10 h-10 bg-purple-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fa-solid fa-car text-white"></i>
                </div>
                <!-- Etiqueta como enlace -->
                <a href="#" class="text-sm text-gray-700 hover:underline">#</a>
            </div>
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
            <a href="../Firma/Firma.php" target="_blank" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-xs">Firma Facturas</span>
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