<?php
require_once "db.php";
require_once "permisos.php";

if (!tiene_permiso('categorias_crud')) {
    echo "<div class='form-container'><h3>No tienes permiso para crear categorías.</h3></div>";
    exit;
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre !== '') {
        $pdo->prepare("INSERT INTO categorias (nombre) VALUES (?)")->execute([$nombre]);
        $mensaje = "Categoría creada correctamente.";
    } else {
        $mensaje = "El nombre no puede estar vacío.";
    }
}
?>

<div class="form-container">
    <h2>Crear categoría</h2>

    <?php if ($mensaje): ?>
        <div style="color:lightgreen;margin-bottom:10px;"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre</label>
        <input type="text" name="nombre" required>

        <button class="neon-button" type="submit">Crear</button>
        <a class="btn btn-secondary" href="layout.php?page=categorias_listar" style="margin-left:8px;">Cancelar</a>
    </form>
</div>
