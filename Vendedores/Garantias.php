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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <h1 class="text-black-600 text-2xl font-bold">Garantias</h1>
    </div>

    <!-- Features Section -->
    <div class="w-full max-w-xs  pb-16">
        <h2 class="text-center text-lg font-semibold text-gray-700 mb-4">Modulos</h2>
        <div class="grid grid-cols-3 gap-4">
           
            
            <div class="neumorphism p-4 text-center">
                <!-- Icono de Bodega -->
                <div
                    class="neumorphism-icon w-10 h-10 bg-red-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fa-solid fa-lock text-white"></i>
                </div>
                <!-- Etiqueta como enlace -->
                <a href="../Garantias/Garantias.php" target="_blank" class="text-sm text-gray-700 hover:underline">Solicitar Garantia</a>
            </div>
            <div class="neumorphism p-4 text-center">
                <!-- Icono de Bodega -->
                <div
                    class="neumorphism-icon w-10 h-10 bg-purple-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fa-solid fa-file-invoice text-white"></i>
                </div>
                <!-- Etiqueta como enlace -->
                <a href="../Garantias/HistoricoSolicitudes.php" target="_blank" class="text-sm text-gray-700 hover:underline">Historial</a>
            </div>
            <div class="neumorphism p-4 text-center">
                <!-- Icono de Bodega -->
                <div
                    class="neumorphism-icon w-10 h-10 bg-purple-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fa-solid fa-file-invoice text-white"></i>
                </div>
                <!-- Etiqueta como enlace -->
                <a href="../Garantias/Consultar.php" target="_blank" class="text-sm text-gray-700 hover:underline">Cambios de Estado</a>
            </div>
        </div>
        <!-- Footer Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg">
            <div class="flex justify-around py-2">
                <a href="../php/logout_index.php" class="text-blue-500 text-center flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-xs">Salir</span>
                </a>
                <a href="../Firma/Firma.php" target="_blank" class="text-gray-500 text-center flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-xs">Firma Facturas</span>
                </a>
                <a href="#" id="openModal" class="text-gray-500 text-center flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-xs">Apps</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Botón flotante y contenedor del chat -->
    <div id="chat-widget" class="fixed bottom-16 right-4">
        <button id="chat-toggle" class="neumorphism w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-blue-600 transition">
            <i class="fa-solid fa-comments"></i>
        </button>
        <div id="chat-box" class="hidden neumorphism absolute bottom-16 right-0 w-80 bg-white rounded-lg shadow-lg p-4">
            <div id="chat-container" class="w-full h-64 border border-gray-300 rounded overflow-y-scroll mb-2 p-2 bg-gray-50"></div>
            <select id="user-select" class="w-full p-2 border border-gray-300 rounded mb-2">
                <option value="">Selecciona un usuario</option>
                <?php
                $host = "localhost";
                $dbname = "automuelles_db";
                $username = "root";
                $password = ""; // Update if you have a password

                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Get active users
                    $activeStmt = $pdo->query("SELECT user_name FROM active_sessions");
                    $activeUsers = $activeStmt->fetchAll(PDO::FETCH_COLUMN);

                    // Get all users
                    $allStmt = $pdo->query("SELECT name FROM users");
                    while ($row = $allStmt->fetch(PDO::FETCH_ASSOC)) {
                        $name = $row['name'];
                        $status = in_array($name, $activeUsers) ? '🟢' : '🔴';
                        echo "<option value='" . htmlspecialchars($name) . "'>$status " . htmlspecialchars($name) . "</option>";
                    }
                } catch (PDOException $e) {
                    echo "<option>Error de conexión: " . $e->getMessage() . "</option>";
                }
                ?>
            </select>
            <div class="flex">
                <input type="text" id="message-input" class="w-full p-2 border border-gray-300 rounded-l focus:outline-none" placeholder="Escribe tu mensaje">
                <button id="send-button" class="p-2 bg-blue-500 text-white rounded-r hover:bg-blue-600">Enviar</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#chat-toggle').click(function() {
                console.log("Toggle clicked");
                $('#chat-box').toggleClass('hidden');
            });

            $('#send-button').click(function() {
                let user = $('#user-select').val();
                let message = $('#message-input').val();
                if (user && message) {
                    let fullMessage = "enviar " + user + " " + message;
                    console.log("Sending:", fullMessage);
                    $('#chat-container').append('<p class="text-right text-blue-600">Tú: ' + message + ' (para ' + user + ')</p>');
                    $.post('../Chat/chat.php', {
                        message: fullMessage
                    }, function(response) {
                        console.log("Response from chat.php:", response); // Debug
                        $('#chat-container').append('<p class="text-left text-gray-700">Bot: ' + response + '</p>');
                        $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX error:", textStatus, errorThrown); // Debug
                    });
                    $('#message-input').val('');
                } else {
                    alert("Por favor, selecciona un usuario y escribe un mensaje.");
                }
            });

            $('#message-input').keypress(function(e) {
                if (e.which == 13) $('#send-button').click();
            });
        });
    </script>
</body>

</html>