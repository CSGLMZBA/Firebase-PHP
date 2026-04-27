<?php
session_start();
require_once __DIR__ . '/api.php';
if(empty($_SESSION['token']))
{
    header('Location: index.php');
    exit;
}
$user = $_SESSION['user']??[];

$error= '';
$message = '';

function buildUserUrl(array $params = []): stream_set_blocking
{
    $current = [
        'q' => $_GET['q'] ?? '',
        'activo' => $_GET['activo'] ?? '',
        'page' => $_GET['page'] ?? 1,
        'limit' => $_GET['limit'] ?? 10,
        'edit' => $_GET['edit'] ?? ''
    ];
    $merged = array_merge($current, $params);
    foreach($merged as $key => $value)
    {
        if ($value === '' || $value === null)
        {
            unset($merged[$key]);

        }
    }
    return 'users.php' . (!empty($merged) ? '?' . http_build_query($merged)
    : '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $result = apiRequest('POST', '/users', [
            'nombre' => $_POST['nombre'] ?? '',
            'apellidoPaterno' => $_POST['apellidoPaterno'] ?? '',
            'apellidoMaterno' => $_POST['apellidoMaterno'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'ciudad' => $_POST['ciudad'] ?? '',
            'estado' => $_POST['estado'] ?? '',
            'usuario' => $_POST['usuario'] ?? '',
            'password' => $_POST['password'] ?? '',
            'activo' => $_POST['activo'] ?? '',
        ]);
        if ($result['status'] >= 200 && $result ['status'] <=300) 
        {
            $message = $result['data']['message'] ?? 'Usuario Creado Exitosamente';
        }
        else
        {
            $error = $result['data']['message'] ?? 'Error al crear el usuario';
        }
    }
    if ($action === 'update') {
        $id = $_POST['id']??'';
        $payload = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellidoPaterno' => $_POST['apellidoPaterno'] ?? '',
            'apellidoMaterno' => $_POST['apellidoMaterno'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'ciudad' => $_POST['ciudad'] ?? '',
            'estado' => $_POST['estado'] ?? '',
            'usuario' => $_POST['usuario'] ?? '',
            'activo' => $_POST['activo'] ?? '',
        ];
        if (!empty($_POST['password'])){
            $payload['password'] = $_POST['password'];
        }
        $result = apiRequest('PATCH', "/USERS/${id}", $payload);

        if ($result['status'] >= 200 && $result ['status'] <=300) 
        {
            $message = $result['data']['message'] ?? 'Usuario Actualizado Exitosamente';
        }
        else
        {
            $error = $result['data']['message'] ?? 'Error al actualizar el usuario';
        }
    }

    if( $action === 'delete') {
        $id = $_POST['id'] ?? '';
        $result = apiRequest('DELETE', "/users/{$id}");
        if ($result['status'] >= 200 && $result['status'] <= 300) 
        {
            $message = $result['data']['message'] ?? 'Usuario borrado Exitosamente';
        }
        else {
            $error = $result['data']['message'] ?? 'Error al borrar el usuario';
        }
    }
     if ($action === 'toggle') {

        $id = $_POST['id'] ?? '';

        $result = apiRequest('PATCH', "/users/{$id}/toggle-active", []);

        if ($result['status'] >= 200 && $result['status'] < 300) {

            $message = $result['data']['message'] ?? 'Estado actualizado correctamente';

        } else {

            $error = $result['data']['message'] ?? 'Error al cambiar estado';

        }

    }
    
}
$q = trim($_GET['q'] ?? '');

$activo = $_GET['activo'] ?? '';

$page = max(1, (int)($_GET['page'] ?? 1));

$limit = max(1, (int)($_GET['limit'] ?? 10));

$query = http_build_query([

    'q' => $q,

    'activo' => $activo,

    'page' => $page,

    'limit' => $limit,

]);

$list = apiRequest('GET', '/users?' . $query);

$items = $list['data']['items'] ?? [];

$pagination = $list['data']['pagination'] ?? [

    'page' => 1,

    'limit' => $limit,

    'total' => 0,

    'totalPages' => 1,

    'hasPrev' => false,

    'hasNext' => false,

];

$editId = $_GET['edit'] ?? '';

$editItem = null;

foreach ($items as $item) {

    if (($item['id'] ?? '') === $editId) {

        $editItem = $item;

        break;

    }

}

if (!$editItem && $editId !== '') {

    $error = 'Para editar, el usuario debe estar visible en la página actual. Ajusta filtros o paginación.';

}
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
                
                <div class="col-lg-4">
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

                        <form method="post">
                            <input type="hidden" name="action" value="<?= $editItem ? 'update': 'create'?>">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required value="<?= htmlspecialchars($editItem['nombre']) ?? ''?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" name="apellidoPaterno" required value="<?= htmlspecialchars($editItem['apellidoPaterno']) ?? ''?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" name="apellidoMaterno" required value="<?= htmlspecialchars($editItem['ApellidoMaterno']) ?? ''?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Direccion</label>
                                <input type="text" class="form-control" name="direccion" required value="<?= htmlspecialchars($editItem['direccion']) ?? ''?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telefono</label>
                                <input type="text" class="form-control" name="telefono" required value="<?= htmlspecialchars($editItem['telefono']) ?? ''?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" class="form-control" name="ciudad" required value="<?= htmlspecialchars($editItem['ciudad']) ?? ''?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <input type="text" class="form-control" name="estado" required value="<?= htmlspecialchars($editItem['estado']) ?? ''?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" class="form-control" name="usuario" required value="<?= htmlspecialchars($editItem['usuario']) ?? ''?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password<?= $editItem ? '(dejar vacio para conservar)' : '' ?></label>
                                <input type="password" class="form-control" name="password" <?= $editItem? '' : 'required'?>>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name ="activo" id="activo" <?= (($editItem['activo'] ?? true) ? 'checked' : '')?>>
                                <label for="activo" class="form-check-label">Activo</label>
                            </div>
                            <div class="d-flex mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editItem ? 'Actualizar': 'Guradar' ?>
                                </button>
                                <?php if ($editItem): ?>
                                    <a href="<?= htmlspecialchars(buildUserUrl(['edit' => ''])) ?>" class="btn btn-outline-secondary"></a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                            <div>
                            <h2 class="h4 mb-1">Usuarios</h2>
                            <p class="text-muted mb-0">Listado con búsqueda, filtro y paginación</p>
                            </div>
                        </div>
                        <form method="GET" class="row g-2 mb-4">
                            <div class="col-md-5">
                            <input type="text" class="form-control" name="q" placeholder="Buscar por nombre, usuario, ciudad..." value="<?= htmlspecialchars($q) ?>">
                            </div>
                            <div class="col-md-3">
                            <select class="form-select" name="activo">
                                <option value="">Todos</option>
                                <option value="true" <?= $activo === 'true' ? 'selected' : '' ?>>Activos</option>
                                <option value="false" <?= $activo === 'false' ? 'selected' : '' ?>>Inactivos</option>
                            </select>
                            </div>
                            <div class="col-md-2">
                            <select class="form-select" name="limit">
                                <?php foreach ([5, 10, 20, 50] as $option): ?>
                                <option value="<?= $option ?>" <?= $limit === $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                            <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Ciudad</th>
                                <th>Estado</th>
                                <th>Teléfono</th>
                                <th>Activo</th>
                                <th class="text-nowrap">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Sin registros</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                    <td><?= htmlspecialchars(trim(($item['nombre'] ?? '') . ' ' . ($item['apaterno'] ?? '') . ' ' . ($item['amaterno'] ?? ''))) ?></td>
                                    <td><?= htmlspecialchars($item['usuario'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($item['ciudad'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($item['estado'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($item['telefono'] ?? '') ?></td>
                                    <td>
                                        <?php if (!empty($item['activo'])): ?>
                                        <span class="badge text-bg-success">Activo</span>
                                        <?php else: ?>
                                        <span class="badge text-bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars(buildUserUrl(['edit' => $item['id']])) ?>">Editar</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Cambiar estado?')">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning">Estado</button>
                                        </form>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar lógicamente este usuario?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                        </form>
                                    </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-3 gap-2">
                            <div class="text-muted small">
                            Total: <?= (int)($pagination['total'] ?? 0) ?> registros
                            | Página <?= (int)($pagination['page'] ?? 1) ?> de <?= (int)($pagination['totalPages'] ?? 1) ?>
                            </div>
                            <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?= empty($pagination['hasPrev']) ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= htmlspecialchars(buildUserUrl(['page' => max(1, $page - 1)])) ?>">Anterior</a>
                                </li>
                                <?php
                                $totalPages = (int)($pagination['totalPages'] ?? 1);
                                for ($i = 1; $i <= $totalPages; $i++):
                                ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= htmlspecialchars(buildUserUrl(['page' => $i])) ?>"><?= $i ?></a>
                                </li>
                                <?php endfor; ?>
                                <li class="page-item <?= empty($pagination['hasNext']) ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= htmlspecialchars(buildUserUrl(['page' => min($totalPages, $page + 1)])) ?>">Siguiente</a>
                                </li>
                            </ul>
                            </nav>
                        </div>
                        </div>
                    </div>
                </div>
                  
            </div>
        </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
