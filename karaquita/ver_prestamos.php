<?php
// ver_prestamos.php
if (!tiene_permiso('prestamos_ver')) { echo "No tienes permiso."; exit(); }
$rol = (int)$_SESSION['role_id'];
if ($rol === 1) {
    $stmt = $pdo->query("SELECT p.*, i.nombre AS item, u.username AS solicitante FROM prestamos p LEFT JOIN inventario i ON i.id=p.inventario_id LEFT JOIN usuarios u ON u.id=p.usuario_solicitante ORDER BY p.fecha_solicitud DESC");
    $prest = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("SELECT p.*, i.nombre AS item FROM prestamos p LEFT JOIN inventario i ON i.id=p.inventario_id WHERE p.usuario_solicitante = :u OR :is_admin=1 ORDER BY p.fecha_solicitud DESC");
    $stmt->execute(['u'=>$_SESSION['user_id'],'is_admin'=> ($rol===1?1:0)]);
    $prest = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="card">
    <h2>Préstamos</h2>
    <table><thead><tr><th>ID</th><th>Artículo</th><th>Cantidad</th><th>Solicitante</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr></thead><tbody>
    <?php foreach($prest as $p): ?>
    <tr>
    <td><?= $p['id'] ?></td>
    <td><?= htmlspecialchars($p['item']) ?></td>
    <td><?= (int)$p['cantidad'] ?></td>
    <td><?= htmlspecialchars($p['usuario_solicitante'] ?? $p['usuario_id'] ?? '—') ?></td>
    <td><?= htmlspecialchars($p['estado']) ?></td>
    <td><?= $p['fecha_solicitud'] ?? $p['fecha'] ?? '' ?></td>
    <td>
    <?php if ($p['estado']=='pendiente' && tiene_permiso('prestamos_crud')): ?>
        <a href="layout.php?page=aprobar_prestamo&id=<?= $p['id'] ?>&accion=aprobar">Aprobar</a>
        <a href="layout.php?page=aprobar_prestamo&id=<?= $p['id'] ?>&accion=rechazar">Rechazar</a>
    <?php endif; ?>

    <?php if ($p['estado']=='aprobado' && ($p['usuario_solicitante']==$_SESSION['user_id'] || tiene_permiso('prestamos_crud'))): ?>
        <a href="layout.php?page=devolver_prestamo&id=<?= $p['id'] ?>">Devolver</a>
    <?php endif; ?>
    </td>
    </tr>
    <?php endforeach; ?>
    </tbody></table>
</div>