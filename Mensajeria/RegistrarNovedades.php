<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');

// Verificar si el usuario es 'mensajeria'
if ($_SESSION['user_role'] !== 'mensajeria') {
    die("Acceso denegado.");
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
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido to Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black-600 text-2xl font-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black-600 text-2xl font-bold">No estás autenticado.</h1>
        <?php endif; ?>
        <h1 class="text-black-600 text-2xl font-bold">Despachos</h1>
    </div>

    <!-- Features Section -->
    <div class="w-full max-w-4xl mx-auto pb-16">
        <h2 class="text-center text-2xl font-semibold text-gray-700 mb-6">Detalles del Despacho</h2>
        <!-- Formulario de despacho -->
        <div class="w-full max-w-4xl mx-auto pb-16">

            <form action="Novedades.php" method="POST" class="space-y-4">
                <!-- Opciones de recogida -->
                <div>
                    <label for="recogida" class="block text-gray-700">Selecciona el tipo de recogida</label>
                    <select name="recogida" id="recogida" class="w-full p-2 border rounded-lg">
                        <option value="recogida_bandas">Recogida de Bandas</option>
                        <option value="recogida_muestras">Recogida de Muestras</option>
                        <option value="recogida_mercancia_terminal">Recogida Mercancía Terminal</option>
                        <option value="recogida_mercancia_terminal">Recogida Cores de Baterias</option>
                        <option value="devolucion_mercancia">Devolución Mercancía</option>
                    </select>
                </div>

                <!-- Parqueadero donde recogen la mercancía -->
                <div>
                    <label for="parqueadero" class="block text-gray-700">Parqueadero</label>
                    <input type="text" name="parqueadero" id="parqueadero" class="w-full p-2 border rounded-lg" required>
                </div>

                <!-- Nombre del vendedor -->
                <div>
                    <label for="vendedor" class="block text-gray-700">Nombre del Vendedor</label>
                    <select name="vendedor" id="vendedor" class="w-full p-2 border rounded-lg" required>
                        <option value="">Seleccione un Vendedor</option>
                        <?php
                        // Incluir el archivo de conexión a la base de datos
                        include 'db.php';

                        // Ejecutar la consulta para obtener los vendedores
                        $sql = "SELECT id, name FROM users WHERE role = 'Vendedor'";
                        $stmt = $pdo->query($sql);

                        // Verificar si hay resultados
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No hay vendedores disponibles</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Nombre del cliente que envía -->
                <div>
                    <label for="cliente" class="block text-gray-700">Nombre del Cliente que Envía</label>
                    <input type="text" name="cliente" id="cliente" class="w-full p-2 border rounded-lg" required>
                </div>

                <div>
                    <label for="foto" class="block text-gray-700">Toma una foto</label>
                    <input type="file" name="foto" id="foto" accept="image/*" capture="camera" class="w-full p-2 border rounded-lg">
                </div>

                <!-- Botón de enviar -->
                <div class="text-center">
                    <button type="submit" class="bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600">Enviar</button>
                </div>
            </form>
        </div>
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
            <a href="mensajeria.php" class="text-gray-500 text-center flex flex-col items-center">
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