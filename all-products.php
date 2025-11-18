<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener TODOS los productos (activos e inactivos)
$query = "SELECT p.id, p.nombre, p.precio, p.stock_actual, p.stock_minimo, 
          p.activo, c.id as categoria_id, c.nombre as categoria 
          FROM productos p 
          LEFT JOIN categorias c ON p.categoria_id = c.id 
          ORDER BY p.activo DESC, p.nombre ASC";

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
        "stock_minimo" => (int)$row['stock_minimo'],
        "activo" => (int)$row['activo']
    );
}

echo json_encode($products);
?>