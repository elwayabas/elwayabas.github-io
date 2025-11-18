<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT fecha, COALESCE(total_ventas, 0) as total 
          FROM ventas_diarias 
          WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
          ORDER BY fecha ASC";

$stmt = $db->prepare($query);
$stmt->execute();

$sales = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sales[] = array(
        "fecha" => $row['fecha'],
        "total" => number_format($row['total'], 2, '.', '')
    );
}

// Asegurar que siempre hay 7 días
while (count($sales) < 7) {
    $sales[] = array("fecha" => "", "total" => "0.00");
}

echo json_encode($sales);
?>