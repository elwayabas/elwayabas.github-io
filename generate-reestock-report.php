<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener productos que necesitan reabastecimiento
// Stock ideal = stock_minimo * 2 (puedes ajustar este multiplicador)
$query = "SELECT p.id, p.nombre, p.precio, p.stock_actual, p.stock_minimo,
          (p.stock_minimo * 2) as stock_ideal,
          c.nombre as categoria
          FROM productos p
          LEFT JOIN categorias c ON p.categoria_id = c.id
          WHERE p.stock_actual < (p.stock_minimo * 2) AND p.activo = 1
          ORDER BY p.stock_actual ASC";

$stmt = $db->prepare($query);
$stmt->execute();

$products = array();
$total_investment = 0;
$critical_products = 0;
$ideal_stock_total = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $stock_ideal = $row['stock_ideal'];
    $cantidad_comprar = $stock_ideal - $row['stock_actual'];
    $total_inversion = $cantidad_comprar * $row['precio'];
    
    if ($row['stock_actual'] <= $row['stock_minimo']) {
        $critical_products++;
    }
    
    $ideal_stock_total += $stock_ideal;
    $total_investment += $total_inversion;
    
    $products[] = array(
        "id" => $row['id'],
        "nombre" => $row['nombre'],
        "categoria" => $row['categoria'] ?? 'Sin categoría',
        "precio" => $row['precio'],
        "stock_actual" => $row['stock_actual'],
        "stock_minimo" => $row['stock_minimo'],
        "stock_ideal" => $stock_ideal,
        "cantidad_comprar" => $cantidad_comprar,
        "total_inversion" => $total_inversion
    );
}

$report = array(
    "summary" => array(
        "total_products" => count($products),
        "total_investment" => $total_investment,
        "critical_products" => $critical_products,
        "ideal_stock_total" => $ideal_stock_total
    ),
    "products" => $products
);

// Guardar reporte en la base de datos
$insert_query = "INSERT INTO reportes_inventario 
                (total_productos, inversion_total, productos_criticos, stock_ideal_total, datos_reporte, usuario_id) 
                VALUES (:total, :inversion, :criticos, :stock_ideal, :datos, 1)";

$insert_stmt = $db->prepare($insert_query);
$insert_stmt->bindParam(':total', $report['summary']['total_products']);
$insert_stmt->bindParam(':inversion', $total_investment);
$insert_stmt->bindParam(':criticos', $critical_products);
$insert_stmt->bindParam(':stock_ideal', $ideal_stock_total);
$datos_json = json_encode($report);
$insert_stmt->bindParam(':datos', $datos_json);
$insert_stmt->execute();

echo json_encode(array(
    "success" => true,
    "report" => $report
));
?>