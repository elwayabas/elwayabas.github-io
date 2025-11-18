<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT p.id, p.nombre, p.precio, p.stock_actual as stock 
          FROM productos p 
          WHERE p.activo = 1 
          ORDER BY p.stock_actual DESC 
          LIMIT 4";

$stmt = $db->prepare($query);
$stmt->execute();

$products = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $products[] = array(
        "id" => $row['id'],
        "nombre" => $row['nombre'],
        "precio" => number_format($row['precio'], 2),
        "stock" => $row['stock']
    );
}

echo json_encode($products);
?>