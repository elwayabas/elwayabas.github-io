<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Ventas de hoy
$query = "SELECT COALESCE(total_ventas, 0) as total FROM ventas_diarias WHERE fecha = CURDATE()";
$stmt = $db->prepare($query);
$stmt->execute();
$sales_today = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Productos con stock bajo
$query = "SELECT COUNT(*) as count FROM productos WHERE stock_actual <= stock_minimo AND stock_actual > 0 AND activo = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$low_stock = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total de productos
$query = "SELECT COUNT(*) as count FROM productos WHERE activo = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Clientes (transacciones) de hoy
$query = "SELECT COUNT(*) as count FROM transacciones WHERE DATE(fecha_hora) = CURDATE()";
$stmt = $db->prepare($query);
$stmt->execute();
$customers_today = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

echo json_encode(array(
    "sales_today" => number_format($sales_today, 2),
    "low_stock_count" => $low_stock,
    "total_products" => $total_products,
    "customers_today" => $customers_today
));
?>