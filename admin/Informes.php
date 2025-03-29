<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Informes
    </title>
    <script src="https://cdn.tailwindcss.com">
    </script>
    <link rel="stylesheet" href="../Chat/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="informes.js" crossorigin="anonymous"></script>
    <script src="informePedidosBodega.js" crossorigin="anonymous"></script>
    <script src="informeDespachos.js" crossorigin="anonymous"></script>
    <script src="informeMensajeria.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-900 text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-20 bg-gray-800 flex flex-col items-center py-4 space-y-4">

            <a href="../php/admin_dashboard.php" class="text-xl">
                <i class="fas fa-home"></i>
            </a>

            <a href="Informes.php" class="text-xl">
                <i class="fas fa-chart-bar text-xl"></i>
            </a>

            <a href="catalogo.php" class="text-xl">
                <i class="fa-solid fa-file-import"></i> </a>

            <i class="fas fa-cog text-xl">
            </i>
            <i class="fas fa-user text-xl">
            </i>
            <i class="fas fa-sign-out-alt text-xl">
            </i>
        </div>
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-bars text-2xl">
                    </i>
                    <h1 class="text-2xl font-semibold">
                        Resumen de Pedidos
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <input class="bg-gray-800 text-white px-4 py-2 rounded-lg" placeholder="
Busca aquí..." type="text" />
                    <i class="fas fa-moon text-xl">
                    </i>
                    <i class="fas fa-bell text-xl">
                    </i>
                </div>
            </div>
            <!-- Sales Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

                <!-- Gráfica de pedidos separados-->
                <div class="bg-gray-800 p-6 rounded-lg col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-3xl font-semibold">Facturas separadas</h2>
                            <p class="text-gray-400">Este Mes</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-clock text-xl"></i>
                            <p class="text-green-500">100%</p>
                        </div>
                    </div>
                    <!-- Gráfica -->
                    <div class="h-40 bg-gray-700 rounded-lg mb-4">
                        <canvas id="graficaPedidos" class="w-full h-full"></canvas>
                    </div>

                    <button class="bg-blue-600 px-4 py-2 rounded-lg">
                        Descargar Reporte
                    </button>
                </div>

                <div class="bg-gray-800 p-6 rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">
                            Mes pasado
                        </h2>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">
                                $67.6k
                            </h3>
                            <p class="text-gray-400">
                                Ingreso
                            </p>
                        </div>
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">
                                12.6K
                            </h3>
                            <p class="text-gray-400">
                                Completado
                            </p>
                        </div>
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">
                                143
                            </h3>
                            <p class="text-gray-400">
                                Pending
                            </p>
                        </div>
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">
                                651
                            </h3>
                            <p class="text-gray-400">
                                Dispatch
                            </p>
                        </div>
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">
                                46k
                            </h3>
                            <p class="text-gray-400">
                                Products
                            </p>
                        </div>
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">
                                8.8k
                            </h3>
                            <p class="text-gray-400">
                                Clientes
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Projects Status -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-800 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Informe de Pedidos Bodega</h2>
                    <table class="w-full text-white">
                        <thead>
                            <tr>
                            </tr>
                        </thead>
                        <canvas id="graficaBodega" class="w-full h-full"></canvas>
                    </table>
                    <button class="bg-blue-600 px-4 py-2 rounded-lg">
                        Descargar Reporte
                    </button>
                </div>

                <div class="bg-gray-800 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Informe de Despachos</h2>
                    <table class="w-full text-white">
                        <thead>
                            <tr>
                            </tr>
                        </thead>
                        <canvas id="graficaDespachos" class="w-full h-full"></canvas>
                    </table>
                    <button class="bg-blue-600 px-4 py-2 rounded-lg">
                        Descargar Reporte
                    </button>
                </div>

                <div class="bg-gray-800 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Informe de Mensajeria</h2>
                    <table class="w-full text-white">
                        <thead>
                            <tr>
                            </tr>
                        </thead>
                        <canvas id="graficaMensajeria" class="w-full h-full"></canvas>
                    </table>
                    <button class="bg-blue-600 px-4 py-2 rounded-lg">
                        Descargar Reporte
                    </button>
                </div>

            </div>

            <!-- Top Sellers -->
            <div class="bg-gray-800 p-6 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">
                    VENDEDORES
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-700 p-4 rounded-lg flex items-center space-x-4">
                        <img alt="User profile picture" class="rounded-full" height="40" src="https://storage.googleapis.com/a1aa/image/mOLt58DG6X5oAAxDoGxqrZxyRereQID7QCMPx9ZVNnY.jpg" width="40" />
                        <div>
                            <h3 class="text-lg font-semibold">
                                StarCodeKh
                            </h3>
                            <p class="text-gray-400">
                                Employee
                            </p>
                        </div>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg flex items-center space-x-4">
                        <img alt="User profile picture" class="rounded-full" height="40" src="https://storage.googleapis.com/a1aa/image/mOLt58DG6X5oAAxDoGxqrZxyRereQID7QCMPx9ZVNnY.jpg" width="40" />
                        <div>
                            <h3 class="text-lg font-semibold">
                                Konnor Guzman
                            </h3>
                            <p class="text-gray-400">
                                Employee
                            </p>
                        </div>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg flex items-center space-x-4">
                        <img alt="User profile picture" class="rounded-full" height="40" src="https://storage.googleapis.com/a1aa/image/mOLt58DG6X5oAAxDoGxqrZxyRereQID7QCMPx9ZVNnY.jpg" width="40" />
                        <div>
                            <h3 class="text-lg font-semibold">
                                Alfredo Elliott
                            </h3>
                            <p class="text-gray-400">
                                Contractor
                            </p>
                        </div>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg flex items-center space-x-4">
                        <img alt="User profile picture" class="rounded-full" height="40" src="https://storage.googleapis.com/a1aa/image/mOLt58DG6X5oAAxDoGxqrZxyRereQID7QCMPx9ZVNnY.jpg" width="40" />
                        <div>
                            <h3 class="text-lg font-semibold">
                                Samantha Smith
                            </h3>
                            <p class="text-gray-400">
                                Contractor
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="chat-container">
        <div id="chat-header">Chat en Vivo <span id="close-chat">×</span></div>
        <div id="users-list"></div>
        <div id="chat-messages"></div>
        <input type="text" id="chat-input" placeholder="Escribe un mensaje...">
        <button id="send-btn">Enviar</button>
    </div>
    <div id="chat-button">💬</div>
    <script src="../Chat/chat.js"></script>
</body>

</html>