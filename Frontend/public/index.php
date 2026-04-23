<?php
session_start();
require_once __DIR__ . '/api.php';

if (!empty($_SESSION['token'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$debug = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $result = apiRequest('POST', '/api/auth/login', [
        'usuario' => $usuario,
        'password' => $password
    ], false);

    if ($result['status'] >= 200 && $result['status'] < 300) {
        $_SESSION['token'] = $result['data']['token'] ?? '';
        $_SESSION['user'] = $result['data']['user'] ?? null;
        header('Location: dashboard.php');
        exit;
    }

    $error = $result['data']['message'] ?? 'No fue posible iniciar sesión';

    $debug = 'HTTP Status: ' . ($result['status'] ?? 'N/A') . "\n";
    $debug .= 'Content-Type: ' . ($result['content_type'] ?? 'N/A') . "\n";
    if (!empty($result['error'])) {
        $debug .= 'JSON Error: ' . $result['error'] . "\n";
    }
    $debug .= "Respuesta cruda:\n" . ($result['raw'] ?? '');
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-lg border-0" style="max-width: 520px; width: 100%;">
      <div class="card-body p-4">
        <h2 class="fw-bold mb-1">Iniciar sesión</h2>
        <p class="text-muted mb-4">PHP puro + Firebase + JWT</p>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" class="form-control" name="usuario" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>

          <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>

        <?php if ($debug): ?>
          <hr class="my-4">
          <h6 class="fw-bold">Debug login</h6>
          <pre class="bg-dark text-light p-3 rounded small mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars($debug) ?></pre>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>