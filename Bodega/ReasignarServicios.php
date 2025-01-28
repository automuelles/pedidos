<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');

// Verificar si el usuario es admin
if ($_SESSION['user_role'] !== 'jefeBodega')  {
    die("Acceso denegado.");
}

// Consultar los servicios asignados
$stmt = $pdo->prepare("SELECT * FROM factura_gestionada WHERE estado = 'asignado'");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener las sesiones activas
$stmt = $pdo->prepare("SELECT * FROM active_sessions");
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios disponibles de active_sessions
$stmt = $pdo->prepare("SELECT id, user_name FROM active_sessions");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['servicio']) && isset($_POST['usuario'])) {
        $servicio_id = $_POST['servicio'];
        $usuario_id = $_POST['usuario'];
        
        // Verificar si el usuario está activo en active_sessions
        $stmt = $pdo->prepare("SELECT user_name FROM active_sessions WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $usuario_name = $usuario['user_name']; // Obtener el nombre del usuario

            // Guardar la gestión del servicio en la tabla factura_gestionada
            $stmt = $pdo->prepare("UPDATE factura_gestionada SET user_id = ?, user_name = ?, estado = 'gestionado' WHERE id = ?");
            $stmt->execute([$usuario_id, $usuario_name, $servicio_id]);

            echo "Servicio reasignado exitosamente.";
        } else {
            echo "El usuario seleccionado no está activo.";
        }
    } else {
        echo "Por favor, seleccione un servicio y un usuario.";
    }
}
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
        <h1 class="text-blue-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-green-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-red-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-red-600 text-2xl font-bold">Reasignar Servicios</h1>
    </div>

    <!-- Features Section -->
    <div class="w-full max-w-4xl mx-auto">
        <h2 class="text-center text-lg font-semibold text-gray-700 mb-6">Reasignar Servicios Sin Gestión</h2>
        
        <form action="" method="POST">
            <div class="mb-4">
                <label for="servicio" class="block text-gray-700">Seleccione un servicio:</label>
                <select name="servicio" id="servicio" class="w-full p-2 border border-gray-300 rounded">
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?php echo $servicio['id']; ?>"><?php echo $servicio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="usuario" class="block text-gray-700">Seleccione un usuario:</label>
                <select name="usuario" id="usuario" class="w-full p-2 border border-gray-300 rounded">
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['id']; ?>"><?php echo $usuario['user_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded">Reasignar Servicio</button>
        </form>
    </div>

    <!-- Footer Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg">
        <div class="flex justify-around py-2">
            <a href="../php/logout_user.php" class="text-blue-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
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
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
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