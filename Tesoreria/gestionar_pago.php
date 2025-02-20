<?php
session_start();
require '../php/db.php'; // Conectar a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $intTransaccion = $_POST['inttransaccion'];
    $intDocumento = $_POST['intdocumento'];
    $totalRecibido = $_POST['total_recibido'];
    $userId = $_SESSION['user_id'];
    $novedad = $_POST['novedad'];
    $descripcion = $_POST['descripcion'];
    
    // Capturar el valor de forma_pago
    $formaPago = $_POST['forma_pago']; 

    // Determinar el estado basado en forma_pago
    $estado = ($formaPago === 'total') ? 'gestionado' : 'saldo pendiente';

    // Insertar en la tabla Reporte_caja
    $sqlInsertCaja = "INSERT INTO Reporte_caja (inttransaccion, intdocumento, user_id, estado, novedad, total_recibido, descripcion)
                      VALUES (:inttransaccion, :intdocumento, :user_id, :estado, :novedad, :total_recibido, :descripcion)";
    $stmtInsertCaja = $pdo->prepare($sqlInsertCaja);
    $stmtInsertCaja->execute([
        ':inttransaccion' => $intTransaccion,
        ':intdocumento' => $intDocumento,
        ':user_id' => $userId,
        ':estado' => $estado,
        ':novedad' => $novedad,
        ':total_recibido' => $totalRecibido,
        ':descripcion' => $descripcion
    ]);

    // Actualizar el estado en Reporte_pago
    $sqlUpdatePago = "UPDATE Reporte_pago SET estado = :estado WHERE inttransaccion = :inttransaccion AND intdocumento = :intdocumento";
    $stmtUpdatePago = $pdo->prepare($sqlUpdatePago);
    $stmtUpdatePago->execute([
        ':estado' => $estado,
        ':inttransaccion' => $intTransaccion,
        ':intdocumento' => $intDocumento
    ]);

    echo "<script>alert('Pago gestionado exitosamente.'); window.location.href = 'ReportePago.php';</script>";
}
?>