<?php
require_once "db.php";
require_once "permisos.php";

if (!tiene_permiso('prestamos_update')) {
    echo "<div class='form-container'><h3>No tienes permiso para aprobar préstamos.</h3></div>";
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM prestamos WHERE id = ?");
$stmt->execute([$id]);
$pres = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pres) {
    echo "<div class='form-container'><h3>Préstamo no encontrado.</h3></div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare("UPDATE prestamos SET estado='aprobado', usuario_aprobador = ? , fecha_respuesta = NOW() WHERE id = ?")
        ->execute([$_SESSION['user_id'], $id]);
    echo "<div class='form-container'><h2>✔ Préstamo aprobado</h2>
          <p><a class='btn' href='layout.php?page=ver_prestamos'>Volver a préstamos</a></p></div>";
    exit;
}
?>

<div class="form-container">
    <h2>Aprobar préstamo</h2>
    <p>¿Aprobar préstamo ID <?= $id ?> solicitado por usuario <?= intval($pres['usuario_solicitante'] ?? 0) ?>?</p>

    <form method="POST">
        <button class="neon-button" type="submit">Aprobar</button>
        <a class="btn btn-secondary" href="layout.php?page=ver_prestamos" style="margin-left:10px;">Cancelar</a>
    </form>
</div>
