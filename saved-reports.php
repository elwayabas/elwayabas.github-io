<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT r.id, r.fecha_generacion, r.total_productos, r.inversion_total,
          u.username as usuario
          FROM reportes_inventario r
          LEFT JOIN usuarios u ON r.usuario_id = u.id
          ORDER BY r.fecha_generacion DESC
          LIMIT 20";

$stmt = $db->prepare($query);
$stmt->execute();

$reports = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $reports[] = array(
        "id" => $row['id'],
        "fecha_generacion" => $row['fecha_generacion'],
        "total_productos" => $row['total_productos'],
        "inversion_total" => $row['inversion_total'],
        "usuario" => $row['usuario'] ?? 'Sistema'
    );
}

echo json_encode($reports);
?>