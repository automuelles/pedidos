<?php
include('login.php');
include('validate_session.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/user_dashboard.css">
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

<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">
    <!-- Header -->
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6">
        <h1 class="text-blue-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-green-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-red-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-red-600 text-2xl font-bold">USUARIO SIN ROL ASIGNADO</h1>
    </div>
    <div class="message">
        <p><?php echo htmlspecialchars($message); ?></p>
        <div class="user-info">
            <p><strong>Nombre de usuario:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>Rol:</strong> <?php echo htmlspecialchars($role); ?></p>
        </div>
        <?php if ($role == 'user' || $role == 'Sin rol'): ?>
            <p class="admin-contact">Por favor, comunícate con el administrador.</p>
            <a href="logout_user.php">
                <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    VOLVER
                </button>
            </a>
        <?php endif; ?>
    </div>
    <script>
        // Recargar la página cada 30 segundos
        setInterval(function() {
            location.reload();
        }, 30000); // 30000 milisegundos = 30 segundos
    </script>
</body>

</html>