<?php
// Conexión a la base de datos con PDO
$host = "localhost";
$dbname = "automuelles_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "Conexión fallida: " . $e->getMessage()]));
}

// Obtener el mes actual
$mes_actual = date('Y-m');

// Consulta SQL para contar los pedidos por estado en el mes actual
$sql = "SELECT estado, COUNT(*) as total 
        FROM factura 
        WHERE DATE_FORMAT(fecha, '%Y-%m') = :mes_actual 
        GROUP BY estado";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":mes_actual", $mes_actual, PDO::PARAM_STR);
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Guardar los datos en un array para la gráfica
$data = [];
foreach ($resultado as $fila) {
    $data[$fila['estado']] = (int) $fila['total'];
}

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
