<?php
session_start();
include '../php/db.php'; // Asegúrate de que este archivo contiene la conexión PDO a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recogida = $_POST['recogida'];
    $parqueadero = $_POST['parqueadero'];
    $vendedor = $_POST['vendedor'];
    $cliente = $_POST['cliente'];
    $user_name = $_SESSION['user_name'];
    $fecha = date('Y-m-d H:i:s');
    
    // Manejo de la imagen
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fotoNombre = time() . '_' . $_FILES['foto']['name'];
        $fotoRuta = '../Foto/' . $fotoNombre;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $fotoRuta)) {
            $foto = $fotoRuta;
        }
    }
    
    try {
        $sql = "INSERT INTO Novedades (recogida, parqueadero, vendedor, cliente, fecha, user_name, foto) VALUES (:recogida, :parqueadero, :vendedor, :cliente, :fecha, :user_name, :foto)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':recogida' => $recogida,
            ':parqueadero' => $parqueadero,
            ':vendedor' => $vendedor,
            ':cliente' => $cliente,
            ':fecha' => $fecha,
            ':user_name' => $user_name,
            ':foto' => $foto
        ]);
        echo "<script>alert('Registro guardado con éxito'); window.location.href='Mensajeria.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>