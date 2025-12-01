<?php
// registrar_movimiento.php
if (!tiene_permiso('movimientos_crud')) { echo "No tienes permiso."; exit(); }
$items = $pdo->query("SELECT id,nombre,cantidad FROM inventario ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $inventario_id = (int)($_POST['inventario_id'] ?? 0);
    $tipo = $_POST['tipo'] ?? 'ingreso';
    $cantidad = (int)($_POST['cantidad'] ?? 0);
    $descripcion = $_POST['descripcion'] ?? null;
    if ($cantidad <= 0) { $error = "Cantidad inválida."; }
    else {
        if ($tipo === 'salida' && $pdo->prepare("SELECT cantidad FROM inventario WHERE id=:id")->execute(['id'=>$inventario_id]) ) {
            $stock = $pdo->query("SELECT cantidad FROM inventario WHERE id=".$inventario_id)->fetchColumn();
            if ($stock < $cantidad) { $error = "Stock insuficiente."; }
        }
        if (empty($error)) {
            $pdo->prepare("INSERT INTO movimientos (inventario_id,usuario_id,tipo,cantidad,descripcion) VALUES (:i,:u,:t,:c,:d)")->execute(['i'=>$inventario_id,'u'=>$_SESSION['user_id'],'t'=>$tipo,'c'=>$cantidad,'d'=>$descripcion]);
            if ($tipo === 'ingreso') $pdo->prepare("UPDATE inventario SET cantidad = cantidad + :c WHERE id = :id")->execute(['c'=>$cantidad,'id'=>$inventario_id]);
            if ($tipo === 'salida') $pdo->prepare("UPDATE inventario SET cantidad = cantidad - :c WHERE id = :id")->execute(['c'=>$cantidad,'id'=>$inventario_id]);
            header("Location: layout.php?page=ver_movimientos"); exit();
        }
    }
}
?>
<div class="card">
    <h2>Registrar movimiento</h2>
    <?php if (!empty($error)) echo "<div style='color:red;'>".htmlspecialchars($error)."</div>"; ?>
    <form method="POST">
    <label>Artículo</label><select name="inventario_id"><?php foreach($items as $it): ?><option value="<?= $it['id'] ?>"><?= htmlspecialchars($it['nombre']) ?> (<?= (int)$it['cantidad'] ?>)</option><?php endforeach; ?></select>
    <label>Tipo</label><select name="tipo"><option value="ingreso">Ingreso</option><option value="salida">Salida</option></select>
    <label>Cantidad</label><input type="number" name="cantidad" value="1" required>
    <label>Descripción</label><textarea name="descripcion"></textarea>
    <button>Registrar</button>
    </form>
</div>