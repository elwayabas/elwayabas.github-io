<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=productos_' . date('Y-m-d') . '.csv');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT p.id, p.nombre, c.nombre as categoria, p.precio, 
          p.stock_actual, p.stock_minimo, p.activo, p.fecha_creacion
          FROM productos p
          LEFT JOIN categorias c ON p.categoria_id = c.id
          ORDER BY p.activo DESC, p.nombre ASC";

$stmt = $db->prepare($query);
$stmt->execute();

$output = fopen('php://output', 'w');

// BOM para UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Encabezados
fputcsv($output, array('CATÁLOGO DE PRODUCTOS - NATURACAFÉ'));
fputcsv($output, array('Fecha de exportación: ' . date('d/m/Y H:i:s')));
fputcsv($output, array(''));
fputcsv($output, array('ID', 'Nombre', 'Categoría', 'Precio', 'Stock Actual', 'Stock Mínimo', 'Estado', 'Fecha Registro'));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $estado = $row['activo'] == 1 ? 'Activo' : 'Inactivo';
    
    fputcsv($output, array(
        $row['id'],
        $row['nombre'],
        $row['categoria'] ?? 'Sin categoría',
        '$' . number_format($row['precio'], 2),
        $row['stock_actual'],
        $row['stock_minimo'],
        $estado,
        date('d/m/Y', strtotime($row['fecha_creacion']))
    ));
}

fclose($output);
?>