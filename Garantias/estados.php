<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estados de Reclamos</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido a Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Formulario de Garantías</h1>
    </div>

    <!-- Form to Add Status -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6 w-full max-w-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Agregar Cambio de Estado</h2>
        <form id="statusForm" method="POST" action="">
            <div class="mb-4">
                <label for="nit_cedula" class="block text-sm font-medium text-gray-600 mb-1">Buscar por Número de Documento o NIT:</label>
                <input type="text" name="nit_cedula" id="nit_cedula" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Ingrese NIT o cédula">
            </div>
           
</body>
</html>