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
            <a href="../admin/catalogo.php" class="text-xl">
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
                    <h1 class="text-2xl font-semibold">Resumen de Pedidos</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <input class="bg-gray-800 text-white px-4 py-2 rounded-lg" placeholder="Busca aquí..." type="text" />
                    <i class="fas fa-moon text-xl">
                    </i>
                    <i class="fas fa-bell text-xl"></i>
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