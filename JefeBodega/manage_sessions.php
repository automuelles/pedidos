<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');

// Obtener las sesiones activas
$stmt = $pdo->prepare("SELECT * FROM active_sessions");
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Sesiones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
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
<nav class="fixed top-0 left-0 right-0 bg-white shadow-lg z-50">
    <div class="flex justify-around py-2">
        <a href="../php/logout_index.php" class="text-blue-500 text-center flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M9 5l7 7-7 7" />
            </svg>
            <span class="text-xs">Salir</span>
        </a>
        <a href="Bodega.php" class="text-gray-500 text-center flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
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

<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">
    <!-- Header -->
    <div class="bg-white shadow-lg rounded-lg w-full max-w-xs p-6 text-center mb-6">
        <h1 class="text-yellow-600 text-2xl font-bold mb-2">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold mb-2"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold mb-2">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Desloguear Usuario</h1>
    </div>

    <!-- Tabla -->
    <div class="w-full max-w-4xl p-4 bg-white shadow-lg rounded-lg pb-16">
        <table class="w-full table-auto border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left border-b">Nombre</th>
                    <th class="px-4 py-2 text-left border-b">Hora de Inicio de Sesión</th>
                    <th class="px-4 py-2 text-left border-b">Desloguear Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $session): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b"><?= htmlspecialchars($session['user_name']) ?></td>
                        <td class="px-4 py-2 border-b"><?= htmlspecialchars($session['login_time']) ?></td>
                        <td class="px-4 py-2 border-b">
                            <form action="../php/logout_user.php" method="POST" class="d-inline">
                                <input type="hidden" name="session_id" value="<?= htmlspecialchars($session['session_id']) ?>">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 focus:outline-none">Cerrar Sesión</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Recargar la página cada 30 segundos
        setInterval(function() {
            location.reload();
        }, 30000); // 30000 milisegundos = 30 segundos
    </script>
</body>

</html>