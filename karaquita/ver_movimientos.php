<?php
// ver_movimientos.php
if (!tiene_permiso('movimientos_crud')) { echo "No tienes permiso."; exit(); }
$movs = $pdo->query("SELECT m.*, i.nombre AS item, u.username AS usuario FROM movimientos m LEFT JOIN inventario i ON i.id=m.inventario_id LEFT JOIN usuarios u ON u.id=m.usuario_id ORDER BY m.fecha DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card">
    <h2>Movimientos</h2>
    <table><thead><tr><th>Artículo</th><th>Tipo</th><th>Cantidad</th><th>Usuario</th><th>Fecha</th><th>Descripción</th></tr></thead><tbody>
    <?php foreach($movs as $m): ?>
    <tr><td><?= htmlspecialchars($m['item']) ?></td><td><?= htmlspecialchars($m['tipo']) ?></td><td><?= (int)$m['cantidad'] ?></td><td><?= htmlspecialchars($m['usuario']) ?></td><td><?= $m['fecha'] ?></td><td><?= htmlspecialchars($m['descripcion']) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table>
</div>