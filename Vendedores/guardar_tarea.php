<?php
// Incluir conexión centralizada
include '../php/db.php';

try {
    // Validación básica y obtención de datos del formulario
    if (!empty($_POST['user_name']) && !empty($_POST['solicitud-tarea']) && 
        !empty($_POST['ubicacion']) && !empty($_POST['referencia']) && 
        !empty($_POST['descripcion'])) {
        
        $user_name = $_POST['user_name'];
        $solicitud_tarea = $_POST['solicitud-tarea'];
        $otro_texto = $_POST['otro-texto'] ?? '';
        $ubicacion = $_POST['ubicacion'];
        $referencia = $_POST['referencia'];
        $descripcion = $_POST['descripcion'];

        // Preparar la consulta SQL
        $sql = "INSERT INTO tareas (user_name, solicitud_tarea, especificar_solicitud, ubicacion, referencia, descripcion) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_name, $solicitud_tarea, $otro_texto, $ubicacion, $referencia, $descripcion]);

        // Mostrar mensaje y redirigir tras 2 segundos
        echo "<script>alert('Tarea registrada correctamente.'); window.location.href='vendedor.php';</script>";
    } else {
        echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
    }
} catch (PDOException $e) {
    echo "<script>alert('Error al insertar tarea: " . $e->getMessage() . "'); window.history.back();</script>";
}
?>