<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT p.id, p.nombre, p.precio, p.stock_actual, p.stock_minimo, 
          c.id as categoria_id, c.nombre as categoria 
          FROM productos p 
          LEFT JOIN categorias c ON p.categoria_id = c.id 
          WHERE p.activo = 1 
          ORDER BY p.nombre ASC";

$stmt = $db->prepare($query);
$stmt->execute();

$products = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $products[] = array(
        "id" => $row['id'],
        "nombre" => $row['nombre'],
        "categoria" => $row['categoria'] ?? 'Sin categoría',
        "categoria_id" => $row['categoria_id'],
        "precio" => number_format($row['precio'], 2, '.', ''),
        "stock_actual" => (int)$row['stock_actual'],
        "stock_minimo" => (int)$row['stock_minimo']
    );
}

echo json_encode($products);
?>