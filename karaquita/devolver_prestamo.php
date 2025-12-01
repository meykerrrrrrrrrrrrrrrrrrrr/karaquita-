<?php
require_once "db.php";
require_once "permisos.php";

if (!tiene_permiso('prestamos_update')) {
    echo "<div class='form-container'><h3>No tienes permiso para devolver préstamos.</h3></div>";
    exit;
}

$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM prestamos WHERE id = ?");
$stmt->execute([$id]);
$prest = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prest) {
    echo "<div class='form-container'><h3>Préstamo no encontrado.</h3></div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // lógica de devolución (mantengo tu funcionalidad)
    $pdo->prepare("UPDATE prestamos SET estado='devuelto', fecha_devolucion = NOW() WHERE id = ?")->execute([$id]);
    echo "<div class='form-container'><h2>✔ Préstamo devuelto</h2>
          <p>Préstamo ID {$id} marcado como devuelto.</p>
          <p><a class='btn' href='layout.php?page=ver_prestamos'>Volver a préstamos</a></p>
          </div>";
    exit;
}

?>

<div class="form-container">
    <h2>Devolver préstamo</h2>
    <p>¿Deseas marcar como devuelto el préstamo de ID <?= $id ?> (usuario <?= intval($prest['usuario_solicitante'] ?? 0) ?>)?</p>

    <form method="POST">
        <button class="neon-button" type="submit">Confirmar devolución</button>
        <a class="btn btn-secondary" href="layout.php?page=ver_prestamos" style="margin-left:10px;">Cancelar</a>
    </form>
</div>
