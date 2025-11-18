<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : 0;

$query = "SELECT datos_reporte FROM reportes_inventario WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $report = json_decode($row['datos_reporte'], true);
    
    echo json_encode(array(
        "success" => true,
        "report" => $report
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Reporte no encontrado"
    ));
}
?>