<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $categoria_id = $_POST['categoria_id'];
    $precio = $_POST['precio'];
    $stock_actual = $_POST['stock_actual'];
    $stock_minimo = $_POST['stock_minimo'];
    
    $query = "INSERT INTO productos (nombre, categoria_id, precio, stock_actual, stock_minimo) 
              VALUES (:nombre, :categoria_id, :precio, :stock_actual, :stock_minimo)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':categoria_id', $categoria_id);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':stock_actual', $stock_actual);
    $stmt->bindParam(':stock_minimo', $stock_minimo);
    
    if ($stmt->execute()) {
        echo json_encode(array("success" => true, "message" => "Producto agregado correctamente"));
    } else {
        echo json_encode(array("success" => false, "message" => "Error al agregar producto"));
    }
}
?>