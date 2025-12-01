<?php
// ver_inventario.php
if (!tiene_permiso('inventario_ver')) { echo "No tienes permiso."; exit(); }
$id = (int)($_GET['id'] ?? 0);
if (!$id) { echo "ID faltante."; exit(); }
$item = $pdo->prepare("SELECT i.*, c.nombre AS categoria FROM inventario i LEFT JOIN categorias c ON i.categoria_id=c.id WHERE i.id=:id");
$item->execute(['id'=>$id]); $it = $item->fetch(PDO::FETCH_ASSOC); if (!$it) { echo "No encontrado."; exit(); }
?>
<h2><?= htmlspecialchars($it['nombre']) ?></h2>
<p><strong>Código:</strong> <?= htmlspecialchars($it['codigo']) ?></p>
<p><strong>Categoría:</strong> <?= htmlspecialchars($it['categoria'] ?? '—') ?></p>
<p><strong>Cantidad:</strong> <?= (int)$it['cantidad'] ?></p>
<p><strong>Tipo:</strong> <?= htmlspecialchars($it['tipo']) ?></p>
<p><strong>Estado:</strong> <?= htmlspecialchars($it['estado']) ?></p>
<p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($it['descripcion'])) ?></p>
