<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d', strtotime('-30 days'));
$end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');

$query = "SELECT fecha, total_ventas as total, num_transacciones as transactions, status 
          FROM ventas_diarias 
          WHERE fecha BETWEEN :start AND :end 
          ORDER BY fecha DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(':start', $start);
$stmt->bindParam(':end', $end);
$stmt->execute();

$sales = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sales[] = array(
        "fecha" => $row['fecha'],
        "total" => number_format($row['total'], 2, '.', ''),
        "transactions" => $row['transactions'],
        "status" => $row['status'] ?? 'completed'
    );
}

echo json_encode($sales);
?>