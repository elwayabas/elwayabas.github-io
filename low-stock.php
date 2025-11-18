<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT p.id, p.nombre, c.nombre as categoria, p.stock_actual, p.stock_minimo 
          FROM productos p 
          LEFT JOIN categorias c ON p.categoria_id = c.id 
          WHERE p.stock_actual <= p.stock_minimo AND p.activo = 1 
          ORDER BY p.stock_actual ASC";

$stmt = $db->prepare($query);
$stmt->execute();

$products = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $products[] = array(
        "id" => $row['id'],
        "nombre" => $row['nombre'],
        "categoria" => $row['categoria'] ?? 'Sin categoría',
        "stock_actual" => $row['stock_actual'],
        "stock_minimo" => $row['stock_minimo']
    );
}

echo json_encode($products);
?>