<?php
include('../php/login.php');
include('../php/validate_session.php');
require_once '../php/db.php'; // Conexión a ambas bases de datos ($pdo para MySQL, $conn para SQL Server)

// 1. Obtener facturas con IntTransaccion 40 y 42 de MySQL usando $pdo
$sql_factura = "SELECT id, IntTransaccion, IntDocumento, estado, fecha 
                FROM factura 
                WHERE IntTransaccion IN ('40', '42')
                ORDER BY fecha DESC";
$stmt_factura = $pdo->prepare($sql_factura);
$stmt_factura->execute();
$facturas = $stmt_factura->fetchAll(PDO::FETCH_ASSOC);

$facturas_con_total = [];
foreach ($facturas as $factura) {
    // 2. Consultar TblDocumentos
    $sql_documento = "SELECT IntTotal, StrReferencia3
                     FROM TblDocumentos 
                     WHERE IntTransaccion = :transaccion 
                     AND IntDocumento = :documento";
    $stmt_documento = $conn->prepare($sql_documento);
    $stmt_documento->execute([
        ':transaccion' => $factura['IntTransaccion'],
        ':documento' => $factura['IntDocumento']
    ]);
    $documento = $stmt_documento->fetch(PDO::FETCH_ASSOC);

    // 3. Consultar TblDetallePagos
    $sql_detalle_pagos = "SELECT IntPago
                         FROM TblDetallePagos 
                         WHERE IntTransaccion = :transaccion 
                         AND IntDocumento = :documento";
    $stmt_detalle_pagos = $conn->prepare($sql_detalle_pagos);
    $stmt_detalle_pagos->execute([
        ':transaccion' => $factura['IntTransaccion'],
        ':documento' => $factura['IntDocumento']
    ]);
    $detalle_pagos = $stmt_detalle_pagos->fetch(PDO::FETCH_ASSOC);

    // 4. Combinar los datos y redondear
    $intTotal = round((float)($documento['IntTotal'] ?? 0)); // Redondear a entero
    $intPago = round((float)($detalle_pagos['IntPago'] ?? 0)); // Redondear a entero

    $factura['IntTotal'] = $intTotal;
    $factura['StrReferencia3'] = $documento['StrReferencia3'] ?? '';
    $factura['IntPago'] = $intPago;

    // 5. Solo agregar si los valores redondeados no son iguales
    if ($intTotal !== $intPago) {
        $facturas_con_total[] = $factura;
    }
}
?>

<!-- El HTML sigue igual -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal Automuelles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
        <h1 class="text-black-600 text-2xl font-bold">Reportes de pago</h1>
    </div>

    <!-- Tabla de datos -->
    <div class="neumorphism w-full max-w-4xl p-6 mb-6 mx-auto overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Transacción</th>
                    <th scope="col" class="px-6 py-3">Documento</th>
                    <th scope="col" class="px-6 py-3">Fecha</th>
                    <th scope="col" class="px-6 py-3">Total</th>
                    <th scope="col" class="px-6 py-3">Forma de Pago</th>
                    <th scope="col" class="px-6 py-3">Pago Hgi</th>
                    <th scope="col" class="px-6 py-3">Ver</th>
                    <th scope="col" class="px-6 py-3">Reportar pago</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($facturas_con_total) > 0): ?>
                    <?php foreach ($facturas_con_total as $row): ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['IntTransaccion']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['IntDocumento']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['fecha']); ?></td>
                            <td class="px-6 py-4"><?php echo number_format($row['IntTotal']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['StrReferencia3']); ?></td>
                            <td class="px-6 py-4"><?php echo number_format($row['IntPago']); ?></td>
                            <td class="px-6 py-4">
                                <a href="detalle_factura.php?id=<?php echo htmlspecialchars($row['id']); ?>&inttransaccion=<?php echo htmlspecialchars($row['IntTransaccion']); ?>&intdocumento=<?php echo htmlspecialchars($row['IntDocumento']); ?>" 
                                   class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2">
                                    Ver
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="GuardarObservacion.php?id=<?php echo htmlspecialchars($row['IntTransaccion']); ?>&IntDocumento=<?php echo htmlspecialchars($row['IntDocumento']); ?>" 
                                   class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2">
                                    Guardar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center">No se encontraron facturas</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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
            <a href="tesoreria.php" class="text-gray-500 text-center flex flex-col items-center">
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
</body>
</html>