<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');
// Verificar si el usuario es admin
if ($_SESSION['user_role'] !== 'Vendedor') {
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
        <h1 class="text-black-600 text-2xl font-bold">Solicitar Nota</h1>
    </div>
    <div class="p-1 pb-1">
        <form action="VerNotas.php" method="POST">
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 my-4">
                Ver Notas Gestionadas
            </button>
        </form>
    </div>

    <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md pb-20">
        <h2 class="text-xl font-bold mb-4">Formulario de Reporte de Pago</h2>
        <form action="guardar_Nota.php" method="post" id="paymentForm" onsubmit="return validateForm()">
            <!-- Campo de Tercero -->
            <div class="mb-4">
                <label for="tercero" class="block text-gray-700 font-bold mb-2">Tercero:</label>
                <input type="text" id="tercero" name="tercero" placeholder="Ingrese el nombre del tercero"
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Campo de Transacción -->
            <div class="mb-4">
                <label for="transaccion" class="block text-gray-700 font-bold mb-2">Transacción:</label>
                <input type="number" id="transaccion" name="transaccion" placeholder="Ingrese la transacción"
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Campo de Documento -->
            <div class="mb-4">
                <label for="documento" class="block text-gray-700 font-bold mb-2">Documento:</label>
                <input type="number" id="documento" name="documento" placeholder="Ingrese el documento"
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Campo de Producto -->
            <div class="mb-4">
                <label for="producto" class="block text-gray-700 font-bold mb-2">Producto:</label>
                <input type="text" id="producto" name="producto" placeholder="Ingrese el producto"
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Campo de Motivo -->
            <div class="mb-4">
                <label for="motivo" class="block text-gray-700 font-bold mb-2">Motivo:</label>
                <textarea id="motivo" name="motivo" rows="3" placeholder="Ingrese el motivo"
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required></textarea>
            </div>

            <!-- Botón de Envío -->
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">
                Guardar Reporte
            </button>
        </form>
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
            <a href="vendedor.php" class="text-gray-500 text-center flex flex-col items-center">
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
    function validateForm() {
        // Obtener todos los campos del formulario
        const tercero = document.getElementById('tercero').value.trim();
        const transaccion = document.getElementById('transaccion').value.trim();
        const documento = document.getElementById('documento').value.trim();
        const producto = document.getElementById('producto').value.trim();
        const motivo = document.getElementById('motivo').value.trim();

        // Verificar que todos los campos estén llenos
        if (!tercero || !transaccion || !documento || !producto || !motivo) {
            alert('Por favor, completa todos los campos antes de enviar el formulario.');
            return false; // Evita el envío del formulario
        }

        return true; // Permite el envío del formulario
    }
</script>
</body>

</html>