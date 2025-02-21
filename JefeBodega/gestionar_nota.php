<?php
session_start();
require '../php/db.php'; // Conexión a la BD

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nota_id = filter_input(INPUT_POST, 'nota_id', FILTER_VALIDATE_INT);
    $comentario = trim($_POST['comentario']);
    $usuario = $_SESSION['user_name'];

    if (!$nota_id || empty($comentario)) {
        echo json_encode(["status" => "error", "message" => "Comentario vacío o ID inválido."]);
        exit();
    }

    try {
        // Iniciar transacción para asegurar consistencia
        $pdo->beginTransaction();

        // Insertar la gestión con estado "Autorizado"
        $sql = "INSERT INTO gestiones (nota_id, usuario, comentario, estado) 
                VALUES (:nota_id, :usuario, :comentario, 'Autorizado')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nota_id' => $nota_id,
            ':usuario' => $usuario,
            ':comentario' => $comentario
        ]);

        // Actualizar el estado de la nota a "Autorizado"
        $updateSql = "UPDATE notas SET estado = 'Autorizado' WHERE id = :nota_id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([':nota_id' => $nota_id]);

        // Confirmar la transacción
        $pdo->commit();

        echo json_encode(["status" => "success", "message" => "Gestión guardada y nota autorizada correctamente."]);
        exit();
    } catch (PDOException $e) {
        // Revertir cambios si hay un error
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Error al guardar la gestión: " . $e->getMessage()]);
        exit();
    }
}