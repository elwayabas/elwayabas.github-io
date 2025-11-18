<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    
    // Restaurar producto (marcar como activo)
    $query = "UPDATE productos SET activo = 1 WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        echo json_encode(array("success" => true, "message" => "Producto restaurado correctamente"));
    } else {
        echo json_encode(array("success" => false, "message" => "Error al restaurar producto"));
    }
}
?>