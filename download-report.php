<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=reporte_reabastecimiento_' . date('Y-m-d') . '.csv');

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : 0;

$query = "SELECT datos_reporte, fecha_generacion FROM reportes_inventario WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $report = json_decode($row['datos_reporte'], true);
    
    $output = fopen('php://output', 'w');
    
    // Encabezado
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8
    
    fputcsv($output, array('REPORTE DE REABASTECIMIENTO DE INVENTARIO'));
    fputcsv($output, array('Fecha de generación: ' . $row['fecha_generacion']));
    fputcsv($output, array(''));
    fputcsv($output, array('RESUMEN'));
    fputcsv($output, array('Total de productos a reabastecer', $report['summary']['total_products']));
    fputcsv($output, array('Inversión total estimada', '$' . number_format($report['summary']['total_investment'], 2)));
    fputcsv($output, array('Productos críticos', $report['summary']['critical_products']));
    fputcsv($output, array('Stock ideal total', $report['summary']['ideal_stock_total']));
    fputcsv($output, array(''));
    
    // Tabla de productos
    fputcsv($output, array('Producto', 'Categoría', 'Stock Actual', 'Stock Mínimo', 'Stock Ideal', 'Cantidad a Comprar', 'Precio Unitario', 'Total'));
    
    foreach ($report['products'] as $product) {
        fputcsv($output, array(
            $product['nombre'],
            $product['categoria'],
            $product['stock_actual'],
            $product['stock_minimo'],
            $product['stock_ideal'],
            $product['cantidad_comprar'],
            '$' . number_format($product['precio'], 2),
            '$' . number_format($product['total_inversion'], 2)
        ));
    }
    
    fclose($output);
}
?>