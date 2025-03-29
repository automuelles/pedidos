<?php
// Conexión a la base de datos
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

// Consulta para obtener el número de pedidos en "Enviado" y "Entregado" para el rol "mensajería"
$sql = "SELECT 
            u.name AS usuario, 
            u.role, 
            SUM(CASE WHEN e.estado = 'Enviado' THEN 1 ELSE 0 END) AS total_enviado,
            SUM(CASE WHEN e.estado = 'Entregado' THEN 1 ELSE 0 END) AS total_entregado
        FROM estado e
        INNER JOIN users u ON e.user_id = u.id
        WHERE u.role = 'mensajeria'
        AND e.estado IN ('Enviado', 'Entregado') -- Asegura que los estados sean los correctos
        AND MONTH(e.fecha) = MONTH(CURRENT_DATE()) 
        AND YEAR(e.fecha) = YEAR(CURRENT_DATE())
        GROUP BY u.id, u.name, u.role
        ORDER BY total_enviado DESC, total_entregado DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($resultado);
?>
