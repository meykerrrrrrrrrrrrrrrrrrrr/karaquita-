<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Hasta luego</title>

<style>
    body {
        margin: 0;
        padding: 0;
        background: #0c0c0c;
        color: white;
        font-family: Arial, sans-serif;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        overflow: hidden;
    }

    .msg {
        font-size: 40px;
        text-shadow: 0 0 10px #ffcc00;
        animation: fadein 1.5s ease forwards;
        opacity: 0;
    }

    @keyframes fadein {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .sub {
        font-size: 22px;
        margin-top: 10px;
        animation: fadein2 2.5s ease forwards;
        opacity: 0;
    }

    @keyframes fadein2 {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    /* Efecto de brillo tipo "sistema" */
    .glow {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, #ffcc00 10%, #ffaa00 40%, transparent 70%);
        opacity: 0.35;
        filter: blur(25px);
        position: absolute;
        animation: pulse 2s infinite ease-in-out;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.3; }
        50% { transform: scale(1.3); opacity: 0.5; }
        100% { transform: scale(1); opacity: 0.3; }
    }

</style>

<script>
// Redirigir automáticamente al index luego de 2.5 segundos
setTimeout(() => {
    window.location.href = "index.php";
}, 2500);
</script>

</head>
<body>

<div class="glow"></div>

<div class="msg">¡Hasta luego!</div>
<div class="sub">Cerrando sesión…</div>

</body>
</html>
