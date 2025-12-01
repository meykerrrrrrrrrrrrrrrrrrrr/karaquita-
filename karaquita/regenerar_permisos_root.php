<?php
// regenerar_permisos_root.php
require_once 'db.php';

// 1 — obtener todas las acciones únicas
$acciones = $pdo->query("SELECT DISTINCT accion FROM permisos")->fetchAll(PDO::FETCH_COLUMN);

// 2 — para cada acción asegurar un registro para root
foreach ($acciones as $accion) {

    // verificar si ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM permisos WHERE rol_id = 1 AND accion = ?");
    $stmt->execute([$accion]);

    if ($stmt->fetchColumn() == 0) {
        // crear permiso si no existe
        $insert = $pdo->prepare("INSERT INTO permisos (rol_id, accion, permitido) VALUES (1, ?, 1)");
        $insert->execute([$accion]);
    } else {
        // asegurar que está permitido
        $upd = $pdo->prepare("UPDATE permisos SET permitido = 1 WHERE rol_id = 1 AND accion = ?");
        $upd->execute([$accion]);
    }
}

echo "<h2>✔ Permisos del ROOT regenerados correctamente.</h2>";
echo "<p>Ya puedes borrar este archivo.</p>";
?>
