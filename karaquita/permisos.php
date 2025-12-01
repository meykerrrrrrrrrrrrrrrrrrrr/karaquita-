<?php
// permisos.php  —  funciones para verificar permisos
if (!isset($_SESSION)) session_start();
require_once "db.php";

/**
 * Retorna 1 si el rol tiene permiso para la accion, 0 en caso contrario.
 * ROOT (rol_id == 1) siempre retorna 1 (superadmin).
 */
function tiene_permiso($accion) {
    global $pdo;

    // Si no hay sesión o rol -> denegar
    if (!isset($_SESSION['role_id'])) return 0;

    $rol = (int) $_SESSION['role_id'];

    // ROOT siempre puede TODO
    if ($rol === 1) return 1;

    // Consulta segura
    $stmt = $pdo->prepare("SELECT permitido FROM permisos WHERE rol_id = :rol AND accion = :accion LIMIT 1");
    $stmt->execute(['rol' => $rol, 'accion' => $accion]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['permitido'] : 0;
}

/**
 * Devuelve la lista de permisos (accion => permitido) para un rol dado.
 * Util para "Mis permisos" o admin.
 */
function obtener_permisos_rol($rol_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT accion, permitido FROM permisos WHERE rol_id = :rol");
    $stmt->execute(['rol' => $rol_id]);
    $out = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $out[$r['accion']] = (int)$r['permitido'];
    }
    return $out;
}

/**
 * Actualiza permisos de un rol dada un array accion=>valor
 */
function actualizar_permisos_rol($rol_id, $perms_assoc) {
    global $pdo;
    $pdo->beginTransaction();
    try {
        $upd = $pdo->prepare("UPDATE permisos SET permitido = :val WHERE rol_id = :rol AND accion = :accion");
        $ins = $pdo->prepare("INSERT INTO permisos (rol_id, accion, permitido) VALUES (:rol, :accion, :val)");
        foreach ($perms_assoc as $accion => $val) {
            $upd->execute(['val' => $val, 'rol' => $rol_id, 'accion' => $accion]);
            if ($upd->rowCount() == 0) {
                // no existía -> insert
                $ins->execute(['rol' => $rol_id, 'accion' => $accion, 'val' => $val]);
            }
        }
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}
