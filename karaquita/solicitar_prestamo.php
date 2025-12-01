<?php
if (!tiene_permiso('prestamos_insert')) {
    echo "<h3>No tienes permiso para solicitar un préstamo.</h3>";
    exit();
}

require_once "db.php";

/* ---------------------------------------------------
   1. Procesar formulario (INSERT)
--------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $inventario_id = intval($_POST['inventario_id']);
    $cantidad      = intval($_POST['cantidad']);
    $ubicacion     = trim($_POST['ubicacion']);
    $usuario_id    = $_SESSION['user_id'];  // usuario solicitante

    // Verificar que el producto exista
    $check = $pdo->prepare("SELECT cantidad FROM inventario WHERE id = ?");
    $check->execute([$inventario_id]);
    $prod = $check->fetch(PDO::FETCH_ASSOC);

    if (!$prod) {
        echo "<div style='color:red;'>❌ Producto inexistente</div>";
    } elseif ($cantidad <= 0) {
        echo "<div style='color:red;'>❌ Cantidad inválida</div>";
    } elseif ($cantidad > $prod['cantidad']) {
        echo "<div style='color:red;'>❌ No hay suficiente stock</div>";
    } else {

        // Insertar préstamo
        $sql = $pdo->prepare("
            INSERT INTO prestamos
            (inventario_id, usuario_solicitante, usuario_aprobador, cantidad, usuario_id, ubicacion, estado, fecha_solicitud)
            VALUES (?, ?, NULL, ?, ?, ?, 'pendiente', NOW())
        ");

        $sql->execute([
            $inventario_id,
            $usuario_id,
            $cantidad,
            $usuario_id,
            $ubicacion
        ]);

        echo "<div style='color:lightgreen;'>✔ Solicitud enviada correctamente</div>";
    }
}

/* ---------------------------------------------------
   2. Obtener lista de inventario disponible
--------------------------------------------------- */
$items = $pdo->query("
    SELECT id, nombre, cantidad 
    FROM inventario 
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="card">
    <h2>Solicitar préstamo</h2>

    <form method="POST">

        <label>Seleccionar producto:</label>
        <select name="inventario_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($items as $item): ?>
                <option value="<?= $item['id'] ?>">
                    <?= htmlspecialchars($item['nombre']) ?> — Stock: <?= $item['cantidad'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        

        <label>Cantidad solicitada:</label><br>
        <input type="number" name="cantidad" min="1" required>
        

        <label>Ubicación donde se usará:</label>
        <input type="text" name="ubicacion" required>
        

        <button type="submit">Enviar solicitud</button>

    </form>
</div>