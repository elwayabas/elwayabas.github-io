<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    
    // No eliminar físicamente, solo marcar como inactivo
    $query = "UPDATE productos SET activo = 0 WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        echo json_encode(array("success" => true, "message" => "Producto eliminado correctamente"));
    } else {
        echo json_encode(array("success" => false, "message" => "Error al eliminar producto"));
    }
}
?>