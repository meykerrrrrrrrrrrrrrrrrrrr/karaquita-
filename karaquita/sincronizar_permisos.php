<?php
require_once 'db.php';

/*
    SINCRONIZADOR DE PERMISOS
    -------------------------
    - Asegura que todos los roles tengan todos los permisos posibles.
    - ROOT (rol_id = 1) siempre tiene permitido = 1.
    - Los demás roles tendrán permitido = 0 (hasta que el admin los active).
*/

// 1. Obtener todos los roles existentes
$roles = $pdo->query("SELECT id FROM roles")->fetchAll(PDO::FETCH_COLUMN);

// 2. Definir lista MAESTRA de todos los permisos posibles
$permisos = [
    'inventario_ver',
    'inventario_crud',
    'inventario_insert',
    'inventario_update',
    'inventario_delete',

    'categorias_ver',
    'categorias_crud',
    'categorias_insert',
    'categorias_update',
    'categorias_delete',

    'usuarios_crud',
    'roles_crud',

    'movimientos_crud',
    'movimientos_ver',

    'prestamos_crud',
    'prestamos_insert',
    'prestamos_update',
    'prestamos_delete',

    'solicitudes_crud',
    'solicitudes_insert',
    'solicitudes_update',

    'tickets_crud',
    'tickets_insert',
    'tickets_update',

    'auditoria_ver',
    'reportes'
];

// 3. Insertar permisos faltantes para cada rol
foreach ($roles as $rol_id) {
    foreach ($permisos as $perm) {

        // Verificar si existe el registro
        $check = $pdo->prepare(
            "SELECT COUNT(*) FROM permisos WHERE rol_id = ? AND accion = ?"
        );
        $check->execute([$rol_id, $perm]);

        if ($check->fetchColumn() == 0) {

            // ROOT = siempre permitido
            $permitido = ($rol_id == 1) ? 1 : 0;

            $insert = $pdo->prepare(
                "INSERT INTO permisos (rol_id, accion, permitido) VALUES (?, ?, ?)"
            );
            $insert->execute([$rol_id, $perm, $permitido]);
        } else {
            // Forzar root = permitido
            if ($rol_id == 1) {
                $pdo->prepare(
                    "UPDATE permisos SET permitido = 1 WHERE rol_id = 1 AND accion = ?"
                )->execute([$perm]);
            }
        }
    }
}

echo "<h2>✔ Sincronización completada</h2>";
echo "<p>ROOT ahora tiene TODOS los permisos.</p>";
echo "<p>Los demás roles tienen permisos generados, listos para activar en gestión de roles.</p>";
echo "<p>Ya puedes borrar este archivo.</p>";
?>
