<?php
include('../php/login.php');
include('../php/validate_session.php');
include('AsignarServicios.php');
require '../php/db.php';
// Obtener los datos de la URL
$transaccion = isset($_GET['transaccion']) ? htmlspecialchars($_GET['transaccion'], ENT_QUOTES, 'UTF-8') : null;
$documento = isset($_GET['documento']) ? htmlspecialchars($_GET['documento'], ENT_QUOTES, 'UTF-8') : null;

// Contar la cantidad de facturas
$numeroFacturas = count($facturas);

// Almacenar el número de facturas en la sesión
$_SESSION['numero_facturas'] = $numeroFacturas;
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
    <div class="neumorphism w-full max-w-xs p-6 text-center mb-6 mt-16">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold">
        <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
    </h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Pedidos Pendientes</h1>
    </div>

    <!-- Features Section -->
    <div class="w-full max-w-4xl mx-auto pb-16">
        <h2 class="text-center text-lg font-semibold text-gray-700 mb-6">Pedidos Asignados sin Revisión</h2>

        <?php if ($facturas): ?>
            <div class="space-y-4">
                <?php foreach ($facturas as $factura): ?>
                    <?php
                    // Tomamos los valores de IntTransaccion e IntDocumento de la factura
                    $intTransaccion = $factura['IntTransaccion'];
                    $intDocumento = $factura['IntDocumento'];

                    // Consulta para obtener el StrNombre desde SQL Server
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

                    // Asignación correcta de valores con verificación
                    $strNombre = $documento['StrNombre'] ?? 'N/A';
                    $StrUsuarioGra = $documento['StrUsuarioGra'] ?? 'N/A';
                    $strReferencia1 = !empty($documento['StrReferencia1']) ? 'DOMICILIO' : 'MOSTRADOR';
                    $strObservaciones = $documento['StrObservaciones'] ?? 'N/A';
                    ?>

                    <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-md border border-gray-200">
                        <div>
                            <p class="text-lg font-medium text-gray-800">Transacción: <?php echo htmlspecialchars($factura['IntTransaccion']); ?></p>
                            <p class="text-sm text-gray-600">Documento: <?php echo htmlspecialchars($factura['IntDocumento']); ?></p>
                            <p class="text-xs text-gray-500">Fecha: <?php echo htmlspecialchars($factura['fecha']); ?></p>
                            <p class="text-xs text-gray-500">StrNombre: <?php echo htmlspecialchars($strNombre); ?></p>
                            <p class="text-xs text-gray-500">Entregar En: <?php echo htmlspecialchars($strReferencia1); ?></p>
                            <p class="text-xs text-gray-500">Vendedor: <?php echo htmlspecialchars($StrUsuarioGra); ?></p>
                            <p class="text-xs text-gray-500">Observaciones: <?php echo htmlspecialchars($strObservaciones); ?></p>
                        </div>
                        <div>
                            <form action="picking_factura.php" method="GET">
                                <input type="hidden" name="factura_id" value="<?php echo $factura['factura_id']; ?>">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                                    Gestionar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">No hay pedidos asignados sin revisión.</p>
        <?php endif; ?>
    </div>
    <!-- Modal -->
<div id="modalApps" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold text-gray-700 mb-4 text-center">Aplicaciones</h2>
        
        <div class="grid grid-cols-2 gap-4">
            <a href="./RevisionFinal.php" class="bg-red-500 text-white py-2 px-4 rounded text-center">Revision Final</a>
            <a href="pedidos_separados.php" class="bg-green-500 text-white py-2 px-4 rounded text-center">Historial</a>
        </div>

        <button id="closeModal" class="mt-4 w-full bg-gray-500 text-white py-2 rounded">Cerrar</button>
    </div>
</div>

<!-- JavaScript para abrir y cerrar el modal -->
<script>
    document.getElementById('openModal').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('modalApps').classList.remove('hidden');
    });

    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('modalApps').classList.add('hidden');
    });

    // Cerrar modal al hacer clic fuera de él
    window.addEventListener('click', function(event) {
        let modal = document.getElementById('modalApps');
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
    </script>
   <script>
    window.onload = function() {
        var numeroFacturas = <?php echo isset($_SESSION['numero_facturas']) ? $_SESSION['numero_facturas'] : 0; ?>;
        
        if (numeroFacturas > 0) {
            var audio = new Audio('../assets/audio/notification.mp3'); // Ruta al archivo de sonido
            audio.play(); // Reproducir sonido

            // Mostrar alerta con la cantidad de facturas pendientes
            alert("Tienes " + numeroFacturas + " facturas pendientes por revisión.");
        }
    };

    // Recargar la página cada 30 segundos
    setInterval(function() {
        location.reload();
    }, 30000); // 30000 milisegundos = 30 segundos
</script>
</body>

</html>