<?php
// registrar_prestamo.php (admin)
if (!tiene_permiso('prestamos_crud')) { echo "No tienes permiso."; exit(); }
// Similar a aprobar: crear préstamo aprobado directo y restar stock
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $inventario_id=(int)$_POST['inventario_id']; $cantidad=(int)$_POST['cantidad']; $usuario=(int)$_POST['usuario'];
    $stock = $pdo->prepare("SELECT cantidad FROM inventario WHERE id=:id"); $stock->execute(['id'=>$inventario_id]); $s=(int)$stock->fetchColumn();
    if ($s < $cantidad) $err="Stock insuficiente."; else {
        $pdo->prepare("INSERT INTO prestamos (inventario_id,usuario_solicitante,usuario_aprobador,cantidad,estado,fecha_solicitud,fecha_respuesta) VALUES (:i,:u, :ap, :c,'aprobado',NOW(),NOW())")->execute(['i'=>$inventario_id,'u'=>$usuario,'ap'=>$_SESSION['user_id'],'c'=>$cantidad]);
        $pdo->prepare("UPDATE inventario SET cantidad = cantidad - :c WHERE id=:id")->execute(['c'=>$cantidad,'id'=>$inventario_id]);
        $pdo->prepare("INSERT INTO movimientos (inventario_id,usuario_id,tipo,cantidad,descripcion) VALUES (:i,:u,'prestamo',:c,'Préstamo aprobado')")->execute(['i'=>$inventario_id,'u'=>$_SESSION['user_id'],'c'=>$cantidad]);
        header("Location: layout.php?page=ver_prestamos"); exit();
    }
}
$users = $pdo->query("SELECT id,username FROM usuarios ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
$items = $pdo->query("SELECT id,nombre,cantidad FROM inventario ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Registrar préstamo (admin)</h2>
<?php if(!empty($err)) echo "<div style='color:red;'>".htmlspecialchars($err)."</div>"; ?>
<form method="POST">
<label>Usuario</label><select name="usuario"><?php foreach($users as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option><?php endforeach; ?></select>
<label>Artículo</label><select name="inventario_id"><?php foreach($items as $it): ?><option value="<?= $it['id'] ?>"><?= htmlspecialchars($it['nombre']) ?> (<?= (int)$it['cantidad'] ?>)</option><?php endforeach; ?></select>
<label>Cantidad</label><input type="number" name="cantidad" value="1" required>
<button>Registrar préstamo</button>
</form>
