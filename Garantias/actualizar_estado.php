<?php
require '../php/db.php'; // Esto usa tu conexión PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nit_cedula = trim($_POST['nit_cedula']);
    $nuevo_estado = trim($_POST['nuevo_estado']);

    if (!empty($nit_cedula) && !empty($nuevo_estado)) {
        // Verificar si existe al menos un reclamo con esa cédula
        $query = "SELECT id FROM estado_reclamo WHERE nit_cedula = :nit_cedula";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':nit_cedula' => $nit_cedula]);

        if ($stmt->rowCount() > 0) {
            // Actualizar registros
            $update = "UPDATE estado_reclamo SET estado = :nuevo_estado, fecha_actualizacion = NOW() WHERE nit_cedula = :nit_cedula";
            $updateStmt = $pdo->prepare($update);
            $updateStmt->execute([
                ':nuevo_estado' => $nuevo_estado,
                ':nit_cedula' => $nit_cedula
            ]);

            echo "✅ Estado actualizado correctamente.";
        } else {
            echo "⚠️ No se encontraron reclamos con esa cédula.";
        }
    } else {
        echo "❌ Faltan datos.";
    }
}
?>
