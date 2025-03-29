<?php
include('../php/login.php');
include('../php/validate_session.php');
include('AsignarServicios.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $transaccion = $_POST['transaccion'];
    $documento = $_POST['documento'];

    // Validar si la combinación ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM factura WHERE IntTransaccion = ? AND IntDocumento = ?");
    $stmt->execute([$transaccion, $documento]);
    $existe = $stmt->fetchColumn();

    if ($existe > 0) {
        echo "<p style='color:red;'>La factura con transacción $transaccion y documento $documento ya existe.</p>";
    } else {
        // Insertar nueva factura
        $stmt = $pdo->prepare("INSERT INTO factura (IntTransaccion, IntDocumento) VALUES (?, ?)");
        if ($stmt->execute([$transaccion, $documento])) {
            echo "<p style='color:green;'>Factura registrada con éxito.</p>";
        } else {
            echo "<p style='color:red;'>Error al registrar la factura.</p>";
        }
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
    <!-- Header -->
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Crear Documentos</h1>
    </div>

    <!-- Features Section -->
    <div class="w-full max-w-4xl mx-auto pb-16">
    <form method="POST" class="space-y-4">
            <div>
                <label for="transaccion" class="block text-gray-700 font-medium">Número de Transacción:</label>
                <input type="number" id="transaccion" name="transaccion" required
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>
            <div>
                <label for="documento" class="block text-gray-700 font-medium">Número de Factura:</label>
                <input type="number" id="documento" name="documento" required
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>
            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                Registrar
            </button>
        </form>
    </div>    
</body>

</html>