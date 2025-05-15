<?php
include('../php/login.php');
include('../php/validate_session.php');
require '../php/db.php';
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
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6 mt-16">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            </h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Formulario de Garantias</h1>
    </div>
    <div class="neumorphism w-full max-w-md p-6 text-center">
    <form action="actualizar_estado.php" method="POST" class="space-y-4">
        <input type="text" name="nit_cedula" placeholder="Número de Cédula" required
            class="w-full px-4 py-2 border rounded">
        
        <select name="nuevo_estado" required class="w-full px-4 py-2 border rounded">
            <option value="">-- Seleccione Estado --</option>
            <option value="Recibido">Recibido</option>
            <option value="En revisión">En revisión</option>
            <option value="En revisión proveedor">En revisión proveedor</option>
            <option value="Aprobado">Aprobado</option>
            <option value="Denegado">Denegado</option>
        </select>
        
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Actualizar Estado</button>
    </form>
</div>

</body>

</html>