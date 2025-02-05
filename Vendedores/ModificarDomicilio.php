<?php
include('../php/login.php');
include('../php/validate_session.php');
include '../php/db.php';

$facturaEncontrada = false;
$mensaje = "";

// Procesar la búsqueda de la factura
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['buscar'])) {
    $transaccion = $_POST['IntTransaccion'];
    $documento = $_POST['IntDocumento'];

    // Consulta para obtener la factura
    $stmt = $pdo->prepare("SELECT * FROM factura WHERE IntTransaccion = ? AND IntDocumento = ?");
    $stmt->execute([$transaccion, $documento]);
    $factura = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($factura) {
        $facturaEncontrada = true;
    } else {
        $mensaje = "Factura no encontrada.";
    }
}

// Procesar la actualización de datos
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actualizar'])) {
    $transaccion = $_POST['IntTransaccion'];
    $documento = $_POST['IntDocumento'];
    $referencia1 = $_POST['StrReferencia1'];
    $referencia3 = $_POST['StrReferencia3'];

    $updateStmt = $pdo->prepare("UPDATE factura SET StrReferencia1 = ?, StrReferencia3 = ? WHERE IntTransaccion = ? AND IntDocumento = ?");
    if ($updateStmt->execute([$referencia1, $referencia3, $transaccion, $documento])) {
        $mensaje = "Datos actualizados correctamente.";
    } else {
        $mensaje = "Error al actualizar la factura.";
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
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido a Automuelles Diesel</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
    </div>

    <!-- Features Section -->
    <div class="w-full max-w-lg mx-auto bg-white p-8 shadow-md rounded-2xl mt-10">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Notificar o Modificar Datos del Domicilio</h2>

        <!-- Formulario de búsqueda -->
        <form method="POST" action="">
            <label class="block mb-2 text-gray-700 font-medium">Número de Transacción:</label>
            <input type="number" name="IntTransaccion" required class="w-full p-2 mb-4 border rounded-lg" value="<?= $_POST['IntTransaccion'] ?? '' ?>">

            <label class="block mb-2 text-gray-700 font-medium">Número de Documento:</label>
            <input type="number" name="IntDocumento" required class="w-full p-2 mb-4 border rounded-lg" value="<?= $_POST['IntDocumento'] ?? '' ?>">

            <button type="submit" name="buscar" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Buscar Factura</button>
        </form>

        <?php if ($facturaEncontrada): ?>
            <div class="mt-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Datos de la Factura</h3>
                <form method="POST" action="">
                    <input type="hidden" name="IntTransaccion" value="<?= $factura['IntTransaccion'] ?>">
                    <input type="hidden" name="IntDocumento" value="<?= $factura['IntDocumento'] ?>">

                    <label class="block mb-2 text-gray-700 font-medium">Referencia 1:</label>
                    <input type="text" name="StrReferencia1" class="w-full p-2 mb-4 border rounded-lg" value="<?= $factura['StrReferencia1'] ?>">

                    <label class="block mb-2 text-gray-700 font-medium">Referencia 3:</label>
                    <input type="text" name="StrReferencia3" class="w-full p-2 mb-4 border rounded-lg" value="<?= $factura['StrReferencia3'] ?>">

                    <button type="submit" name="actualizar" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Actualizar Factura</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensaje)): ?>
            <div class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>
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
        // Recargar la página cada 30 segundos
        setInterval(function() {
            location.reload();
        }, 30000); // 30000 milisegundos = 30 segundos
    </script>
</body>

</html>