<?php
declare(strict_types=1);

session_start();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $name = (string) ($_FILES['file']['name'] ?? 'sin-nombre');
    $message = 'Archivo recibido: ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Portal de legajos — laboratorio</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 3rem auto; max-width: 42rem; }
        code { background: #eee; padding: .1rem .3rem; }
        .ok { color: #146c2e; }
        .warn { color: #8a4b00; }
    </style>
</head>
<body>
    <h1>Portal de legajos</h1>
    <p class="warn">Servicio de laboratorio. No usar fuera de la red aislada.</p>

    <ul>
        <li>PHP: <code><?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?></code></li>
        <li>SAPI: <code><?= htmlspecialchars(PHP_SAPI, ENT_QUOTES, 'UTF-8') ?></code></li>
        <li><code>session.upload_progress.enabled</code>:
            <code><?= ini_get('session.upload_progress.enabled') ? '1' : '0' ?></code></li>
    </ul>

    <?php if ($message !== ''): ?>
        <p class="ok"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Archivo: <input type="file" name="file"></label>
        <button type="submit">Subir</button>
    </form>

    <p>El formulario <strong>no guarda</strong> el archivo: solo muestra el nombre.
       El objetivo es inspeccionar la configuración del runtime, no explotar nada.</p>
</body>
</html>
