<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../Chat/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="Modos.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-900 text-white transition-colors duration-300">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-20 bg-gray-800 flex flex-col items-center py-4 space-y-4">
            <a href="../php/admin_dashboard.php" class="text-xl">
                <i class="fas fa-home"></i>
            </a>
            <a href="../admin/Informes.php" class="text-xl">
                <i class="fas fa-chart-bar text-xl"></i>
            </a>
            <a href="catalogo.php" class="text-xl">
                <i class="fa-solid fa-file-import"></i> </a>
            <i class="fas fa-cog text-xl"></i>
            <i class="fas fa-user text-xl"></i>
            <a href="../php/logout_index.php" class="text-xl">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </a>
        </div>
        <!-- Main Content -->
      
        <div class="flex-1 p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-bars text-2xl"></i>
                    <h1 class="text-2xl font-semibold">Catalogo de Productos</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <input class="bg-gray-800 text-white px-4 py-2 rounded-lg" placeholder="Busca aquí..." type="text" />
                    <i class="fas fa-moon text-xl"></i>
                    <i class="fas fa-bell text-xl"></i>
                </div>
            </div>

            <!-- Product Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Card 1: Rin -->
                <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                    <div class="h-48 bg-gray-700 rounded-lg mb-4">
                        <img src="../assets//img/Rin.png" alt="Rin de Hierro" class="w-full h-full object-cover rounded-lg">
                    </div>
                    <h2 class="text-xl font-semibold text-white">Guardar Productos</h2>
                    <p class="text-gray-400">Rin de alta resistencia, diseño moderno.</p>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700" onclick="window.open('../Catalogo/GuardarProductos.php', '_blank')">Cargar Información</button>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700" onclick="window.open('../Catalogo/BuscarProductosGuardados.php', '_blank')">Buscar Información</button>
                </div>

                <!-- Card 2: Llanta -->
                <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                    <div class="h-48 bg-gray-700 rounded-lg mb-4">
                        <img src="#" alt="Formulario Garantias" class="w-full h-full object-cover rounded-lg">
                    </div>
                    <h2 class="text-xl font-semibold text-white">Formulario de Garantias</h2>
                    <p class="text-gray-400">Rin de alta resistencia, diseño moderno.</p>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700" onclick="window.open('../Garantias/Garantias.php', '_blank')">Registrar Información</button>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700" onclick="window.open('../Garantias/estados.php', '_blank')">Consultar Informacion Garantia</button>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700" onclick="window.open('../Garantias/ActualizarEstado.php', '_blank')">Actualizar Estado Garantia</button>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700" onclick="window.open('../Garantias/Consultar.php', '_blank')">Linea Temporal</button>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700" onclick="window.open('../Garantias/HistoricoSolicitudes.php', '_blank')">Historico de Solicitudes</button>
                </div>

                <!-- Card 3: Hoja -->
                <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                    <div class="h-48 bg-gray-700 rounded-lg mb-4">
                        <!-- Espacio para imagen de hoja -->
                        <p class="text-center text-gray-400 pt-20">Imagen de Hoja</p>
                    </div>
                    <h2 class="text-xl font-semibold text-white">Hojas Hercules</h2>
                    <p class="text-gray-400">Hoja resistente para corte preciso.</p>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Cargar Informacion</button>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Descargar Informacion</button>
                </div>

                <!-- Card 4: # -->
                <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                    <div class="h-48 bg-gray-700 rounded-lg mb-4">
                        <!-- Espacio para imagen -->
                        <p class="text-center text-gray-400 pt-20">Imagen de Producto</p>
                    </div>
                    <h2 class="text-xl font-semibold text-white">#</h2>
                    <p class="text-gray-400">#</p>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Cargar Informacion</button>
                    <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Descargar Informacion</button>
                </div>
            </div>

        </div>
</body>

</html>