<?php
require_once 'db.php';
require_once 'permisos.php';

$rol = $_SESSION['role_id'];

$stmt = $pdo->prepare("SELECT accion, permitido FROM permisos WHERE rol_id = ? ORDER BY accion");
$stmt->execute([$rol]);
$permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Mis permisos</h2>
<table class="tabla">
    <tr><th>Permiso</th><th>Estado</th></tr>
<?php foreach ($permisos as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['accion']) ?></td>
        <td><?= $p['permitido'] ? '✔ Permitido' : '✖ No permitido' ?></td>
    </tr>
<?php endforeach; ?>
</table>
