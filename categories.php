<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre ASC";
$stmt = $db->prepare($query);
$stmt->execute();

$categories = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categories[] = array(
        "id" => $row['id'],
        "nombre" => $row['nombre']
    );
}

echo json_encode($categories);
?>