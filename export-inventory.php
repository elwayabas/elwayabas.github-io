<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inventario_' . date('Y-m-d') . '.csv');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT p.id, p.nombre, c.nombre as categoria, p.precio, 
          p.stock_actual, p.stock_minimo, p.fecha_actualizacion
          FROM productos p
          LEFT JOIN categorias c ON p.categoria_id = c.id
          WHERE p.activo = 1
          ORDER BY p.nombre ASC";

$stmt = $db->prepare($query);
$stmt->execute();

$output = fopen('php://output', 'w');

// BOM para UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Encabezados
fputcsv($output, array('INVENTARIO DE PRODUCTOS - NATURACAFÉ'));
fputcsv($output, array('Fecha de exportación: ' . date('d/m/Y H:i:s')));
fputcsv($output, array(''));

// Calcular totales
$total_productos = 0;
$total_valor = 0;
$productos_bajo_stock = 0;

$stmt_stats = $db->prepare($query);
$stmt_stats->execute();

while ($row = $stmt_stats->fetch(PDO::FETCH_ASSOC)) {
    $total_productos++;
    $total_valor += $row['precio'] * $row['stock_actual'];
    if ($row['stock_actual'] <= $row['stock_minimo']) {
        $productos_bajo_stock++;
    }
}

// Resumen
fputcsv($output, array('RESUMEN'));
fputcsv($output, array('Total de productos', $total_productos));
fputcsv($output, array('Valor total del inventario', '$' . number_format($total_valor, 2)));
fputcsv($output, array('Productos con stock bajo', $productos_bajo_stock));
fputcsv($output, array(''));

// Tabla de productos
fputcsv($output, array('ID', 'Producto', 'Categoría', 'Precio', 'Stock Actual', 'Stock Mínimo', 'Estado', 'Última Actualización'));

$stmt->execute(); // Re-ejecutar para obtener los datos de nuevo

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $estado = $row['stock_actual'] == 0 ? 'Agotado' : 
             ($row['stock_actual'] <= $row['stock_minimo'] ? 'Stock Bajo' : 'En Stock');
    
    fputcsv($output, array(
        $row['id'],
        $row['nombre'],
        $row['categoria'] ?? 'Sin categoría',
        '$' . number_format($row['precio'], 2),
        $row['stock_actual'],
        $row['stock_minimo'],
        $estado,
        date('d/m/Y H:i', strtotime($row['fecha_actualizacion']))
    ));
}

fclose($output);
?>