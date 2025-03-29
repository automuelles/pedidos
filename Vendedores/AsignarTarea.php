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
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido a Automuelles Diesel</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
    </div>
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Formulario de Solicitud de Tareas</h2>
    <!-- Features Section -->
    <div class="w-full max-w-lg bg-white p-8 shadow-md rounded-2xl pb-16">
       
        <form action="guardar_tarea.php" method="POST" class="space-y-6">
            <!-- Campo oculto para el nombre del usuario -->
            <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">

            <!-- Campo Solicitud Tarea -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="solicitud-tarea">
                    Solicitud Tarea
                </label>
                <select id="solicitud-tarea" name="solicitud-tarea" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="tomar-foto">Tomar Foto</option>
                    <option value="solicitar-medidas">Solicitar Medidas</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <!-- Campo dinámico para "Otro" -->
            <div id="otro-campo" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="otro-texto">
                    Especificar Solicitud
                </label>
                <input type="text" id="otro-texto" name="otro-texto" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ingrese su solicitud">
            </div>

            <!-- Otros campos -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="ubicacion">
                    Ubicación
                </label>
                <input type="text" id="ubicacion" name="ubicacion" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ingrese la ubicación">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="referencia">
                    Referencia
                </label>
                <input type="text" id="referencia" name="referencia" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ingrese referencia">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="descripcion">
                    Descripción de la Tarea
                </label>
                <textarea id="descripcion" name="descripcion" rows="4" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describa la tarea"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-xl hover:bg-blue-600 focus:ring-2 focus:ring-blue-500">
                Enviar Solicitud
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
            <a href="vendedor.php" class="text-gray-500 text-center flex flex-col items-center">
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
        const solicitudSelect = document.getElementById("solicitud-tarea");
        const otroCampoDiv = document.getElementById("otro-campo");

        solicitudSelect.addEventListener("change", function() {
            if (solicitudSelect.value === "otro") {
                otroCampoDiv.classList.remove("hidden");
            } else {
                otroCampoDiv.classList.add("hidden");
            }
        });
    </script>
    <script>
        // Recargar la página cada 30 segundos
        setInterval(function() {
            location.reload();
        }, 30000); // 30000 milisegundos = 30 segundos
    </script>
</body>

</html>