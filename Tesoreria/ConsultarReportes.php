<?php
include('../php/login.php');
include('../php/validate_session.php');
require_once '../php/db.php'; // Conexión a la base de datos

// Conexión a la base de datos MySQL
$host = "localhost";
$dbname = "automuelles_db";
$username = "root";
$password = "";

try {
    $pdoMySQL = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdoMySQL->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed to MySQL: " . $e->getMessage();
}

// Conexión a la base de datos SQL Server
$serverName = "SERVAUTOMUELLES\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "AutomuellesDiesel1",
    "Uid" => "AutomuellesDiesel",
    "PWD" => "Complex@2024Pass!"
);

try {
    $pdoSQLServer = new PDO("sqlsrv:server=$serverName;Database=AutomuellesDiesel1", $connectionOptions["Uid"], $connectionOptions["PWD"]);
    $pdoSQLServer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a SQL Server: " . $e->getMessage());
}

// Obtener los estados únicos
$sqlEstados = "SELECT DISTINCT estado FROM Reporte_caja";
$stmtEstados = $pdoMySQL->query($sqlEstados);
$estados = $stmtEstados->fetchAll(PDO::FETCH_COLUMN);

// Filtrar por estado y tipo de pago si se han seleccionado
$estadoSeleccionado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Consulta en MySQL
$sqlMySQL = "SELECT 
        f.IntTransaccion, 
        f.IntDocumento, 
        rc.novedad, 
        rc.descripcion,
        rc.estado,
        rc.total_recibido
    FROM 
        factura f
    INNER JOIN 
        Reporte_caja rc
    ON 
        f.IntTransaccion = rc.inttransaccion 
        AND f.IntDocumento = rc.intdocumento
   ";
$conditions = [];
if ($estadoSeleccionado) {
    $conditions[] = "LOWER(rc.estado) = :estado";
}

if (count($conditions) > 0) {
    $sqlMySQL .= " WHERE " . implode(" AND ", $conditions);
}
$stmtMySQL = $pdoMySQL->prepare($sqlMySQL);
$params = [];
if ($estadoSeleccionado) {
    $params[':estado'] = strtolower($estadoSeleccionado);
}

$stmtMySQL->execute($params);
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
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Reportes de pago</h1>
    </div>
    <div class="neumorphism w-full max-w-xs p-6 mb-6 mx-auto">
        <form method="GET" action="">
            <label for="estado" class="block mb-2 text-gray-700">Filtrar por estado:</label>
            <select name="estado" id="estado" class="border border-gray-300 px-2 py-1 w-full">
                <option value="">Todos</option>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?php echo htmlspecialchars($estado); ?>" <?php echo ($estadoSeleccionado == $estado) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($estado); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Filtrar</button>
        </form>
    </div>
    <!-- Tabla de datos -->
    <div class="neumorphism w-full p-6 mb-6 mx-auto overflow-x-auto">
        <?php
        echo "<table class='w-full min-w-max table-auto border-collapse border border-gray-300'>"; // Añadir w-full aquí
        echo "<thead>";
        echo "<tr class='bg-gray-100 text-gray-700 text-left'>";
        echo "<th class='border border-gray-300 px-4 py-2'>IntTransaccion</th>";
        echo "<th class='border border-gray-300 px-4 py-2'>IntDocumento</th>";
        echo "<th class='border border-gray-300 px-4 py-2'>Estado</th>";
        echo "<th class='border border-gray-300 px-4 py-2'>Forma de pago</th>";
        echo "<th class='border border-gray-300 px-4 py-2'>Descripción</th>";
        echo "<th class='border border-gray-300 px-4 py-2'>Total Recibido</th>";
        echo "<th class='border border-gray-300 px-4 py-2'>Total</th>";
        echo "<th class='border border-gray-300 px-4 py-2'>Vendedor</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody class='text-gray-600'>";

        while ($rowMySQL = $stmtMySQL->fetch(PDO::FETCH_ASSOC)) {
            $sqlSQLServer = "SELECT IntTotal, StrUsuarioGra FROM TblDocumentos WHERE IntTransaccion = :transaccion AND IntDocumento = :documento";
            $stmtSQLServer = $pdoSQLServer->prepare($sqlSQLServer);
            $stmtSQLServer->execute([':transaccion' => $rowMySQL['IntTransaccion'], ':documento' => $rowMySQL['IntDocumento']]);
            $rowSQLServer = $stmtSQLServer->fetch(PDO::FETCH_ASSOC);

            echo "<tr class='border-b hover:bg-gray-50'>";
            echo "<form method='POST' action='gestionar_pago.php'>";
            echo "<td class='border border-gray-300 px-4 py-2'>{$rowMySQL['IntTransaccion']}</td>";
            echo "<td class='border border-gray-300 px-4 py-2'>{$rowMySQL['IntDocumento']}</td>";
            echo "<td class='border border-gray-300 px-4 py-2'>{$rowMySQL['estado']}</td>";
            echo "<td class='border border-gray-300 px-4 py-2'>{$rowMySQL['novedad']}</td>";
            echo "<td class='border border-gray-300 px-4 py-2'>{$rowMySQL['descripcion']}</td>";
            echo "<td class='border border-gray-300 px-4 py-2'>" . number_format($rowMySQL['total_recibido'], 2) . "</td>";
            echo "<td class='border border-gray-300 px-4 py-2'>" . number_format($rowSQLServer['IntTotal'], 0) . "</td>";
            echo "<td class='border border-gray-300 px-4 py-2'>{$rowSQLServer['StrUsuarioGra']}</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        ?>
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
            <a href="tesoreria.php" class="text-gray-500 text-center flex flex-col items-center">
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
</body>

</html>