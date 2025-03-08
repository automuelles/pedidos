<?php
include('../php/db.php');

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaccion = htmlspecialchars($_POST['transaccion']);
    $documento = htmlspecialchars($_POST['documento']);
    $zonificacion = htmlspecialchars($_POST['zonificacion']);

    // Insertar en la base de datos
    $sql = "INSERT INTO facturas_zonificacion (transaccion, documento, zonificacion) VALUES (:transaccion, :documento, :zonificacion)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':transaccion', $transaccion);
    $stmt->bindParam(':documento', $documento);
    $stmt->bindParam(':zonificacion', $zonificacion);

    if ($stmt->execute()) {
        // Redirigir a RegistroViajes.php después de guardar exitosamente
        header("Location: RegistroViajes.php");
        exit();
    } else {
        echo "Error al guardar los datos.";
    }
} else {
    echo "No se recibieron datos.";
}
?>
