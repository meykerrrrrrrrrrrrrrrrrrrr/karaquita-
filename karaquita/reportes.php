<?php
// reportes.php
if (!tiene_permiso('reportes')) { echo "No tienes permiso."; exit(); }
$tipo = $_GET['tipo'] ?? '';
if ($tipo === 'inventario') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=inventario.csv');
    $out = fopen('php://output','w');
    fputcsv($out, ['id','codigo','nombre','categoria_id','cantidad','tipo','estado','fecha_registro']);
    foreach($pdo->query("SELECT * FROM inventario ORDER BY id") as $row) fputcsv($out, $row);
    exit();
}
if ($tipo === 'movimientos') {
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename=movimientos.csv');
    $out = fopen('php://output','w'); fputcsv($out, ['id','inventario_id','usuario_id','tipo','cantidad','fecha','descripcion']);
    foreach($pdo->query("SELECT * FROM movimientos ORDER BY id") as $row) fputcsv($out, $row); exit();
}
if ($tipo === 'prestamos') {
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename=prestamos.csv');
    $out = fopen('php://output','w'); fputcsv($out, ['id','inventario_id','usuario_solicitante','usuario_aprobador','cantidad','estado','fecha_solicitud','fecha_respuesta','fecha_devolucion']);
    foreach($pdo->query("SELECT * FROM prestamos ORDER BY id") as $row) fputcsv($out, $row); exit();
}
?>
<div class="card">
    <h2>Reportes</h2>
    <div class="card">
        <a href="layout.php?page=reportes&tipo=inventario" class="card-stat fade" style="text-decoration:none;color:white;">
                <div class="icon">📗📉</div>
                <div class="card-title">Descargar inventario (CSV)</div>
                <div class="card-number" style="font-size:22px;"></div>
        </a>
    </div>
    <div class="card">
        <a href="layout.php?page=reportes&tipo=movimientos" class="card-stat fade" style="text-decoration:none;color:white;">
                <div class="icon">📗📉</div>
                <div class="card-title">Descargar movimientos (CSV)</div>
                <div class="card-number" style="font-size:22px;"></div>
        </a>
    </div>
    <div class="card">
        <a href="layout.php?page=reportes&tipo=prestamos" class="card-stat fade" style="text-decoration:none;color:white;">
                <div class="icon">📗📉</div>
                <div class="card-title">Descargar préstamos (CSV)</div>
                <div class="card-number" style="font-size:22px;"></div>
        </a>
    </div>
</div>