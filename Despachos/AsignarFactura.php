<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');

if ($_SESSION['user_role'] !== 'despachos') {
    die("Acceso denegado.");
}

try {
    $sql_servicios = "SELECT fg.id, fg.user_name, fg.estado, f.IntTransaccion, f.IntDocumento
                      FROM factura_gestionada fg
                      LEFT JOIN factura f ON fg.factura_id = f.id
                      WHERE fg.estado = 'Enviado'";
    $stmt_servicios = $pdo->prepare($sql_servicios);
    $stmt_servicios->execute();
    $servicios = $stmt_servicios->fetchAll(PDO::FETCH_ASSOC);

    $sql_usuarios = "SELECT active_sessions.user_name 
                     FROM active_sessions 
                     JOIN users ON active_sessions.user_id = users.id
                     WHERE users.role IN ('Mensajeria', 'despachos')";
    $stmt_usuarios = $pdo->prepare($sql_usuarios);
    $stmt_usuarios->execute();
    $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo "Error al obtener datos: " . $e->getMessage();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id = $_POST['servicio_id'] ?? null;
    $new_user_name = $_POST['new_user_name'] ?? '';

    if ($servicio_id && !empty($new_user_name)) {
        try {
            $sql_update = "UPDATE factura_gestionada SET user_name = :user_name WHERE id = :id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute(['user_name' => $new_user_name, 'id' => $servicio_id]);
            $message = "Asignación actualizada correctamente.";
        } catch (PDOException $e) {
            $message = "Error al actualizar la asignación: " . $e->getMessage();
        }
    } else {
        $message = "Por favor, selecciona un usuario válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal Automuelles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #38a169;
            color: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        .notification.error {
            background-color: #e53e3e;
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
            <a href="despachos.php" class="text-gray-500 text-center flex flex-col items-center">
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
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Reasignar Servicios</h1>
    </div>

    <div class="container mx-auto p-6 pb-16">
        <h2 class="text-center text-lg font-semibold text-gray-700 mb-6">Reasignar Servicios Sin Gestión</h2>
        <?php if (!empty($servicios)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($servicios as $servicio): ?>
                    <?php
                    // Obtener IntTransaccion e IntDocumento del servicio
                    $intTransaccion = $servicio['IntTransaccion'];
                    $intDocumento = $servicio['IntDocumento'];

                    // Consulta en SQL Server para obtener más detalles del documento
                    $sql = "SELECT 
                        T.StrNombre,
                        D.StrReferencia1,
                        D.StrUsuarioGra,
                        D.StrObservaciones
                    FROM [AutomuellesDiesel1].[dbo].[TblDocumentos] D
                    JOIN [AutomuellesDiesel1].[dbo].[TblTerceros] T 
                        ON D.StrTercero = T.StrIdTercero
                    WHERE D.IntTransaccion = :IntTransaccion 
                      AND D.IntDocumento = :IntDocumento";

                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':IntTransaccion', $intTransaccion, PDO::PARAM_INT);
                    $stmt->bindParam(':IntDocumento', $intDocumento, PDO::PARAM_INT);
                    $stmt->execute();

                    $documento = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Asignar valores con verificación de existencia
                    $strNombre = $documento['StrNombre'] ?? 'N/A';
                    $StrUsuarioGra = $documento['StrUsuarioGra'] ?? 'N/A';
                    $strReferencia1 = $documento['StrReferencia1'] ?? 'N/A';
                    $strObservaciones = $documento['StrObservaciones'] ?? 'N/A';
                    ?>

                    <form action="" method="POST" class="bg-white shadow-md rounded-2xl p-4 border border-gray-200">
                        <input type="hidden" name="servicio_id" value="<?= $servicio['id'] ?>">
                        <p class="text-gray-600"><strong>Transacción:</strong> <?= htmlspecialchars($intTransaccion) ?></p>
                        <p class="text-gray-600"><strong>Número de Factura:</strong> <?= htmlspecialchars($intDocumento) ?></p>
                        <p class="text-gray-600"><strong>Estado:</strong>
                            <?= htmlspecialchars(str_replace('gestionado', 'asignado', $servicio['estado'])) ?>
                        </p>
                        <p class="text-gray-600"><strong>Asignado a:</strong> <?= htmlspecialchars($servicio['user_name']) ?></p>

                        <!-- Nueva información obtenida de SQL Server -->
                        <p class="text-gray-600"><strong>Cliente:</strong> <?= htmlspecialchars($strNombre) ?></p>
                        <p class="text-gray-600"><strong>Entregar en:</strong> <?= htmlspecialchars($strReferencia1) ?></p>
                        <p class="text-gray-600"><strong>Vendedor:</strong> <?= htmlspecialchars($StrUsuarioGra) ?></p>
                        <p class="text-gray-600"><strong>Observaciones:</strong> <?= htmlspecialchars($strObservaciones) ?></p>

                        <label for="user-select-<?= $servicio['id'] ?>" class="block text-sm font-medium text-gray-700 mt-4">
                            Reasignar a:
                        </label>
                        <select name="new_user_name" id="user-select-<?= $servicio['id'] ?>"
                            class="w-full mt-2 p-2 border rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccionar usuario</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= htmlspecialchars($usuario) ?>">
                                    <?= htmlspecialchars($usuario) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <a href="ver.php?transaccion=<?= urlencode($intTransaccion) ?>&documento=<?= urlencode($intDocumento) ?>"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-lg inline-block">
                            Ver
                        </a>
                        <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg w-full">
                            Guardar
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600 text-center mt-8">No hay servicios gestionados en este momento.</p>
        <?php endif; ?>
    </div>

    <div id="notification" class="notification"></div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const message = <?= json_encode($message) ?>;
            if (message) {
                const notification = document.getElementById("notification");
                notification.textContent = message;
                notification.style.display = "block";

                // Ocultar la notificación después de 5 segundos
                setTimeout(() => {
                    notification.style.display = "none";
                }, 5000);
            }
        });

        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</body>

</html>