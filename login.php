<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->username) && !empty($data->password)) {
    $query = "SELECT id, username, nombre_completo, rol FROM usuarios WHERE username = :username AND password = :password AND activo = 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $data->username);
    
    // Para producción, usar password_hash() y password_verify()
    $password = md5($data->password); // Temporal, cambiar a bcrypt
    $stmt->bindParam(":password", $password);
    
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Actualizar último acceso
        $update = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id";
        $stmt2 = $db->prepare($update);
        $stmt2->bindParam(":id", $row['id']);
        $stmt2->execute();
        
        echo json_encode(array(
            "success" => true,
            "message" => "Login exitoso",
            "user" => array(
                "id" => $row['id'],
                "username" => $row['username'],
                "nombre" => $row['nombre_completo'],
                "rol" => $row['rol']
            )
        ));
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Usuario o contraseña incorrectos"
        ));
    }
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Datos incompletos"
    ));
}
?>