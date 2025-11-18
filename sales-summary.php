<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d', strtotime('-7 days'));
$end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');

// Ventas de hoy
$query = "SELECT COALESCE(total_ventas, 0) as total, COALESCE(num_transacciones, 0) as trans 
          FROM ventas_diarias WHERE fecha = CURDATE()";
$stmt = $db->prepare($query);
$stmt->execute();
$today = $stmt->fetch(PDO::FETCH_ASSOC);

// Ventas de la semana
$query = "SELECT COALESCE(SUM(total_ventas), 0) as total 
          FROM ventas_diarias 
          WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$stmt = $db->prepare($query);
$stmt->execute();
$week = $stmt->fetch(PDO::FETCH_ASSOC);

// Promedio por venta
$avg = $today['trans'] > 0 ? $today['total'] / $today['trans'] : 0;

echo json_encode(array(
    "today_sales" => number_format($today['total'], 2, '.', ''),
    "week_sales" => number_format($week['total'], 2, '.', ''),
    "today_transactions" => $today['trans'],
    "avg_sale" => number_format($avg, 2, '.', ''),
    "today_change" => "+0% vs ayer",
    "week_change" => "Última semana",
    "transactions_change" => "Hoy",
    "avg_change" => "Promedio"
));
?>