<?php
// login.php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario  = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario === '' || $password === '') {
        $error = "Completa usuario y contraseña.";
    } else {

        // Buscar usuario
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = :usuario LIMIT 1");
        $stmt->execute(['usuario' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            $password_ok = false;
            $hash = $user['password_hash'];

            // 1️⃣ password_hash() moderno
            if (password_verify($password, $hash)) {
                $password_ok = true;
            }

            // 2️⃣ SHA1 antiguo (tu base lo usa)
            if (!$password_ok && sha1($password) === $hash) {
                $password_ok = true;

                // 🔄 Actualizar automáticamente a password_hash()
                $nuevo = password_hash($password, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE usuarios SET password_hash = :h WHERE id = :id");
                $upd->execute(['h' => $nuevo, 'id' => $user['id']]);
            }

            // 3️⃣ MD5 (por si existiera)
            if (!$password_ok && md5($password) === $hash) {
                $password_ok = true;

                // 🔄 Actualiza automáticamente a password_hash()
                $nuevo = password_hash($password, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE usuarios SET password_hash = :h WHERE id = :id");
                $upd->execute(['h' => $nuevo, 'id' => $user['id']]);
            }

            if ($password_ok) {

                if ((int)$user['activo'] === 0) {
                    $error = "Tu cuenta está desactivada.";
                } else {

                    // Iniciar sesión
                    session_regenerate_id(true);
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role_id']  = $user['rol_id'];

                    header("Location: layout.php");
                    exit;
                }
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }

        } else {
            $error = "Usuario no encontrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Login - Sistema</title>
<link rel="stylesheet" href="styles.css">
<style>
body {
    display:flex; align-items:center; justify-content:center;
    height:100vh; background:#0f0f0f; color:#eaeaea;
}
.card {
    background:#191919; padding:24px; border-radius:10px;
    box-shadow:0 6px 20px rgba(0,0,0,0.6); width:360px;
}
input, button { width:100%; margin-top:8px; }
h2 { margin:0 0 12px 0; }
.error {
    color:#ffb3b3; background:#3a1a1a;
    padding:8px; border-radius:6px; margin-bottom:10px;
}
</style>
</head>
<body>
<div class="card">
    <h2>Iniciar sesión</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <label>Usuario</label>
        <input type="text" name="usuario" required autofocus>

        <label>Contraseña</label>
        <input type="password" name="password" required>

        <button type="submit">Entrar</button>
    </form>
</div>
</body>
</html>
