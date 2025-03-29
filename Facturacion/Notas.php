<?php
include('../php/db.php');
include('../php/login.php');
include('../php/validate_session.php');
try {
    // excluyendo aquellas que tengan un registro en gestiones con estado "Realizado"
    $stmt = $pdo->query("
        SELECT 
            n.*, 
            g.usuario AS usuario
        FROM Notas n
        INNER JOIN gestiones g ON n.id = g.nota_id
        WHERE g.estado = 'Autorizado'
        AND n.id NOT IN (
            SELECT nota_id FROM gestiones WHERE estado = 'Realizado'
        )
        ORDER BY n.fecha_registro DESC
    ");
    
    $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al recuperar las notas: " . $e->getMessage());
}



?>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas Solicitadas - Automuelles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .neumorphism {
            background: #e0e5ec;
            border-radius: 15px;
            box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
        }

        .table-container {
            overflow-x: auto;
        }
    </style>
</head>

<body class="bg-gray-200 min-h-screen flex flex-col items-center justify-center">

    <!-- Header -->
    <div class="neumorphism w-full max-w-md p-6 text-center mb-6">
        <h1 class="text-yellow-600 text-2xl font-bold">Bienvenido a Automuelles</h1>
        <?php if (isset($_SESSION['user_name'])): ?>
            <h1 class="text-black text-xl font-semibold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <?php else: ?>
            <h1 class="text-black text-xl font-semibold">No estás autenticado.</h1>
        <?php endif; ?>
        <h2 class="text-black text-lg font-bold mt-2">Notas Solicitadas</h2>
    </div>

    <!-- Tabla de Notas -->
    <div class="neumorphism w-full p-6 mb-6 mx-auto overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="py-3 px-4">Tercero</th>
                    <th class="py-3 px-4">Transacción</th>
                    <th class="py-3 px-4">Documento</th>
                    <th class="py-3 px-4">Producto</th>
                    <th class="py-3 px-4">Motivo</th>
                    <th class="py-3 px-4">Autorizado Por</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4">Fecha</th>
                    <th class="py-3 px-4">Comentario</th>
                    <th class="py-3 px-4">Acción</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($notas as $nota): ?>
                    <tr class="border-b">
                        <td class="py-2 px-4"><?php echo htmlspecialchars($nota['tercero']); ?></td>
                        <td class="py-2 px-4"><?php echo $nota['transaccion']; ?></td>
                        <td class="py-2 px-4"><?php echo $nota['documento']; ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($nota['producto']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($nota['motivo']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($nota['usuario'] ?? 'Pendiente'); ?></td>
                        <td class="py-2 px-4">
                            <span class="px-2 py-1 rounded-full text-white
                        <?php echo ($nota['estado'] == 'sin gestión') ? 'bg-red-500' : 'bg-green-500'; ?>">
                                <?php echo htmlspecialchars($nota['estado']); ?>
                            </span>
                        </td>
                        <td class="py-2 px-4"><?php echo $nota['fecha_registro']; ?></td>
                        <td class="py-2 px-4">
                            <input type="text" id="comentario_<?php echo $nota['id']; ?>" class="border rounded px-2 py-1 w-full" placeholder="Añadir comentario" required>
                        </td>
                        <td class="py-2 px-4">
                            <a href="javascript:void(0);" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded"
                                onclick="guardarGestion(<?php echo $nota['id']; ?>)">
                                Gestionar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
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
            <a href="facturacion.php"text-gray-500 text-center flex flex-col items-center">
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
function guardarGestion(notaId) {
    let comentario = document.getElementById('comentario_' + notaId).value;
    
    if (comentario.trim() === "") {
        alert("Por favor, añade un comentario antes de gestionar.");
        return;
    }

    let formData = new FormData();
    formData.append('nota_id', notaId);
    formData.append('comentario', comentario);

    fetch('gestionar_nota.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Esperamos una respuesta JSON
    .then(data => {
        alert(data.message); // Mostramos el mensaje en un alert

        if (data.status === "success") {
            // Opcional: actualizar la interfaz sin recargar la página
            document.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Hubo un error al procesar la gestión.");
    });
}
</script>
</body>

</html>