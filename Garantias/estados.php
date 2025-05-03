<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estados de Reclamos</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        <h1 class="text-black-600 text-2xl font-bold">Formulario de Garantias</h1>
    </div>
        <!-- Form to Add Status -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Agregar Cambio de Estado</h2>
            <form id="statusForm" method="POST" action="">
                <div class="mb-4">
                    <label for="reclamo_id" class="block text-sm font-medium text-gray-600 mb-1">Seleccionar Reclamo:</label>
                    <select name="reclamo_id" id="reclamo_id" required class="w-full max-w-xs p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Seleccione un reclamo</option>
                        <?php
                        $conn = new mysqli("localhost", "username", "password", "database");
                        if ($conn->connect_error) {
                            die("Conexión fallida: " . $conn->connect_error);
                        }
                        $result = $conn->query("SELECT id, nit_cedula FROM reclamos");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>Reclamo #{$row['id']} - {$row['nit_cedula']}</option>";
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="estado" class="block text-sm font-medium text-gray-600 mb-1">Estado:</label>
                    <select name="estado" id="estado" required class="w-full max-w-xs p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="Recibido">Recibido</option>
                        <option value="En revisión">En revisión</option>
                        <option value="En revisión proveedor">En revisión proveedor</option>
                        <option value="Aprobado">Aprobado</option>
                        <option value="Denegado">Denegado</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="motivo" class="block text-sm font-medium text-gray-600 mb-1">Motivo (opcional):</label>
                    <textarea name="motivo" id="motivo" rows="3" class="w-full max-w-xs p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>

                <button type="submit" class="w-full max-w-xs bg-red-600 text-white p-2 rounded-md hover:bg-red-700 transition">Agregar Estado</button>
            </form>
        </div>

        <!-- Timeline Display -->
        <div class="timeline-section">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Historial de Estados</h2>
            <div class="relative">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $reclamo_id = $_POST["reclamo_id"];
                    $estado = $_POST["estado"];
                    $motivo = $_POST["motivo"];

                    $conn = new mysqli("localhost", "username", "password", "database");
                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }

                    $stmt = $conn->prepare("INSERT INTO reclamo_estados (reclamo_id, estado, motivo) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $reclamo_id, $estado, $motivo);
                    $stmt->execute();
                    $stmt->close();
                }

                if (isset($_POST["reclamo_id"])) {
                    $reclamo_id = $_POST["reclamo_id"];
                    $conn = new mysqli("localhost", "username", "password", "database");
                    $stmt = $conn->prepare("SELECT estado, motivo, fecha_cambio FROM reclamo_estados WHERE reclamo_id = ? ORDER BY fecha_cambio ASC");
                    $stmt->bind_param("i", $reclamo_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        $estado = $row['estado'];
                        $motivo = $row['motivo'] ? "<p class='text-sm text-gray-600'>Motivo: {$row['motivo']}</p>" : "";
                        $fecha = $row['fecha_cambio'];
                        echo "
                        <div class='flex items-center mb-6'>
                            <div class='bg-red-600 text-white font-bold py-2 px-4 rounded-l-md'>" . sprintf("%02d", $count) . "</div>
                            <div class='bg-white p-4 flex-1 border border-gray-200 rounded-r-md'>
                                <p class='font-semibold text-gray-800'>$estado</p>
                                $motivo
                                <p class='text-xs text-gray-500'>$fecha</p>
                            </div>
                        </div>";
                        $count++;
                    }
                    $stmt->close();
                    $conn->close();
                }
                ?>
                <!-- Timeline Line -->
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-red-600 z-[-1]"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('statusForm').addEventListener('submit', function() {
            setTimeout(() => {
                document.getElementById('motivo').value = '';
            }, 100);
        });
    </script>
</body>
</html>