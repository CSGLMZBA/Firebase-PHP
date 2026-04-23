<?php
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
   <nav class="navbar navbar-expand-lg bg-dark navbar-dark shadow-sm">
            <div class="container">
                <a href="dashboard.php" class="navbar-brand fw-bold">
                    Sistema PHP
                </a>
                <div class="d-flex gap-2">
                    <a href="users.php" class="btn btn-outline-light btn-sm">
                        Usuarios
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-sm">
                        Logout
                    </a>
                </div>
            </div>
        </nav>
        <main class="container py-4">
            <div class="row g-4">
                <div class="card border-0 shadow-sm">
                    <h2 class="h4 mb-3">
                        <?= $editItem ? 'Editar Usuario' : 'Nuevo Usuario' ?>
                    </h2>
                    <?php if ($message): ?>
                        <div class="aler alert-success">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
