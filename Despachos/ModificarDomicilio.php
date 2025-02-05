<?php
include('../php/login.php');
include('../php/validate_session.php');
include '../php/db.php';

$mensaje = "";

// Consulta para obtener facturas en estado "RevisionFinal"
$stmt = $pdo->prepare("SELECT * FROM factura WHERE estado = ?");
$stmt->execute(['RevisionFinal']);
$facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario de actualización
if (isset($_POST['actualizar'])) {
    $IntTransaccion = $_POST['IntTransaccion'];
    $IntDocumento = $_POST['IntDocumento'];
    $StrReferencia1 = $_POST['StrReferencia1'];
    $StrReferencia3 = $_POST['StrReferencia3'];

    // Actualizar la tabla factura
    $updateStmt = $pdo->prepare("UPDATE factura 
                                 SET StrReferencia1 = ?, StrReferencia3 = ? 
                                 WHERE IntTransaccion = ? AND IntDocumento = ?");
    $result = $updateStmt->execute([$StrReferencia1, $StrReferencia3, $IntTransaccion, $IntDocumento]);

    if ($result) {
        $mensaje = "Factura actualizada correctamente.";
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
    <title>Facturas en Revisión Final</title>
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
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Revision Final</h1>
    </div>

    <!-- Mostrar Facturas -->
    <div class="w-full max-w-4xl mx-auto bg-white p-8 shadow-md rounded-2xl mt-10 mb-16">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Listado de Facturas</h2>

        <?php if (!empty($facturas)): ?>
            <table class="w-full text-left table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2">Transacción</th>
                        <th class="border px-4 py-2">Documento</th>
                        <th class="border px-4 py-2">Enviar a</th>
                        <th class="border px-4 py-2">Forma de pago</th>
                        <th class="border px-4 py-2">Estado</th>
                        <th class="border px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facturas as $factura): ?>
                        <tr>
                            <form method="POST" action="">
                                <td class="border px-4 py-2"><?= $factura['IntTransaccion'] ?></td>
                                <td class="border px-4 py-2"><?= $factura['IntDocumento'] ?></td>
                                <td class="border px-4 py-2">
                                    <input type="text" name="StrReferencia1" class="w-full p-2 border rounded-lg"
                                        value="<?= htmlspecialchars($factura['StrReferencia1']) ?>">
                                </td>
                                <td class="border px-4 py-2">
                                    <input type="text" name="StrReferencia3" class="w-full p-2 border rounded-lg"
                                        value="<?= htmlspecialchars($factura['StrReferencia3']) ?>">
                                </td>
                                <td class="border px-4 py-2"><?= $factura['estado'] ?></td>
                                <td class="border px-4 py-2">
                                    <input type="hidden" name="IntTransaccion" value="<?= $factura['IntTransaccion'] ?>">
                                    <input type="hidden" name="IntDocumento" value="<?= $factura['IntDocumento'] ?>">
                                    <button type="submit" name="actualizar" class="bg-green-600 text-white px-2 py-1 rounded-lg hover:bg-green-700">
                                        Actualizar
                                    </button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                No se encontraron facturas en revisión final.
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
            <a href="Despachos.php" class="text-gray-500 text-center flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-xs">volver</span>
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
</body>
<script>
    // Recargar la página cada 30 segundos
    setInterval(function() {
        location.reload();
    }, 60000); // 30000 milisegundos = 30 segundos
</script>

</html>