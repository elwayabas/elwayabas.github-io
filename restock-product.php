<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    
    $query = "UPDATE productos SET stock_actual = stock_actual + :quantity WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':quantity', $quantity);
    
    if ($stmt->execute()) {
        echo json_encode(array("success" => true, "message" => "Stock actualizado correctamente"));
    } else {
        echo json_encode(array("success" => false, "message" => "Error al actualizar stock"));
    }
}
?>