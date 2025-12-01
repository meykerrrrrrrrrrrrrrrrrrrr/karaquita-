<?php
// gestion_usuarios.php
if (!tiene_permiso('usuarios_crud')) { echo "No tienes permiso."; exit(); }
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add'])) {
    $username = trim($_POST['username']); $password = trim($_POST['password']); $rol = (int)$_POST['rol_id'];
    if ($username === '' || $password === '') $err="Faltan campos."; else {
        $hash = password_hash($password,PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO usuarios (username,password_hash,rol_id,activo) VALUES (:u,:p,:r,1)")->execute(['u'=>$username,'p'=>$hash,'r'=>$rol]);
        header("Location: layout.php?page=gestion_usuarios"); exit();
    }
}
$users = $pdo->query("SELECT u.*, r.nombre AS rol FROM usuarios u LEFT JOIN roles r ON r.id=u.rol_id ORDER BY u.id")->fetchAll(PDO::FETCH_ASSOC);
$roles = $pdo->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card">
    <h2>Gestión de usuarios</h2>
    <?php if (!empty($err)) echo "<div style='color:red;'>".htmlspecialchars($err)."</div>"; ?>
    <h3>Agregar usuario</h3>
    <form method="POST">
    <input name="username" placeholder="usuario" required>
    <input name="password" placeholder="contraseña" required>
    <select name="rol_id"><?php foreach($roles as $r): ?><option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option><?php endforeach; ?></select>
    <button name="add">Agregar</button>
    </form>
    <h3>Lista</h3>
    <table><thead><tr><th>ID</th><th>Usuario</th><th>Rol</th><th>Activo</th></tr></thead><tbody>
    <?php foreach($users as $u): ?><tr><td><?= $u['id'] ?></td><td><?= htmlspecialchars($u['username']) ?></td><td><?= htmlspecialchars($u['rol']) ?></td><td><?= $u['activo']?'Sí':'No' ?></td></tr><?php endforeach; ?>
    </tbody></table>
</div>