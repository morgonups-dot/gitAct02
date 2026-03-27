<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clase_crud.php';

$mostrarFormulario = false;
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crud = new Crud();
    
    if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $correo = trim($_POST['correo']);
        $celular = trim($_POST['celular']);
        
        if (empty($nombre) || empty($apellido) || empty($correo) || empty($celular)) {
            $_SESSION['mensaje'] = 'Todos los campos son obligatorios';
            $_SESSION['tipoMensaje'] = 'error';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['mensaje'] = 'El correo electrónico no es válido';
            $_SESSION['tipoMensaje'] = 'error';
        } else {
            $crud->crear($nombre, $apellido, $correo, $celular);
            $_SESSION['mensaje'] = 'Usuario creado exitosamente';
            $_SESSION['tipoMensaje'] = 'success';
        }
    }
    
    if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
        $id = intval($_POST['id']);
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $correo = trim($_POST['correo']);
        $celular = trim($_POST['celular']);
        
        if ($id <= 0) {
            $_SESSION['mensaje'] = 'ID de usuario inválido';
            $_SESSION['tipoMensaje'] = 'error';
        } elseif (empty($nombre) || empty($apellido) || empty($correo) || empty($celular)) {
            $_SESSION['mensaje'] = 'Todos los campos son obligatorios';
            $_SESSION['tipoMensaje'] = 'error';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['mensaje'] = 'El correo electrónico no es válido';
            $_SESSION['tipoMensaje'] = 'error';
        } else {
            $crud->actualizar($id, $nombre, $apellido, $correo, $celular);
            $_SESSION['mensaje'] = 'Usuario actualizado exitosamente';
            $_SESSION['tipoMensaje'] = 'success';
        }
    }
    
    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $id = intval($_POST['id']);
        if ($id <= 0) {
            $_SESSION['mensaje'] = 'ID de usuario inválido';
            $_SESSION['tipoMensaje'] = 'error';
        } else {
            $crud->eliminar($id);
            $_SESSION['mensaje'] = 'Usuario eliminado exitosamente';
            $_SESSION['tipoMensaje'] = 'success';
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipoMensaje = $_SESSION['tipoMensaje'] ?? 'error';
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipoMensaje']);
}

$crud = new Crud();

$orden = $_GET['orden'] ?? 'id';
$direccion = $_GET['dir'] ?? 'DESC';
$busqueda = $_GET['buscar'] ?? '';
$campoBusqueda = $_GET['campo'] ?? 'todos';

$editarUsuario = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    if ($id > 0) {
        $editarUsuario = $crud->leerPorId($id);
    }
}

if (isset($_GET['nuevo'])) {
    $mostrarFormulario = true;
}

$usuarios = $crud->leerTodo($orden, $direccion, $busqueda, $campoBusqueda);

$columnaOrdenar = function($col) use ($orden, $direccion) {
    return ($orden === $col && $direccion === 'ASC') ? 'DESC' : 'ASC';
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Usuarios</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23007bff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>') 12 12, auto;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        body:hover {
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>') 12 12, auto;
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: white;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(56, 239, 125, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(235, 51, 73, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #485563 0%, #29323c 100%);
            color: white;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(72, 85, 99, 0.4);
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .filtros {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filtros input[type="text"] {
            flex: 1;
            min-width: 200px;
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filtros select {
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filtros select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        
        .filtros input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        th:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: scale(1.02);
        }
        
        th a {
            color: white;
            text-decoration: none;
            display: block;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr {
            transition: all 0.3s ease;
        }
        
        tr:hover {
            background: linear-gradient(90deg, #f8f9ff 0%, #fff5f5 100%);
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .form-container {
            display: none;
            animation: slideDown 0.4s ease;
        }
        
        .form-container.active {
            display: block;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        
        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .sort-icon {
            font-size: 12px;
            margin-left: 5px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-asc {
            background: #38ef7d;
            color: white;
        }
        
        .badge-desc {
            background: #f45c43;
            color: white;
        }
        
        a.btn {
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Usuarios</h1>
        
        <div class="card">
            <div class="filtros">
                <form method="GET" style="display:flex; gap:10px; flex:1; flex-wrap:wrap;">
                    <select name="campo">
                        <option value="todos" <?php echo $campoBusqueda === 'todos' ? 'selected' : ''; ?>>Todos los campos</option>
                        <option value="nombre" <?php echo $campoBusqueda === 'nombre' ? 'selected' : ''; ?>>Nombre</option>
                        <option value="apellido" <?php echo $campoBusqueda === 'apellido' ? 'selected' : ''; ?>>Apellido</option>
                        <option value="correo" <?php echo $campoBusqueda === 'correo' ? 'selected' : ''; ?>>Correo</option>
                        <option value="celular" <?php echo $campoBusqueda === 'celular' ? 'selected' : ''; ?>>Celular</option>
                    </select>
                    <input type="text" name="buscar" placeholder="Ingrese valor a buscar..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Limpiar</a>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div style="margin-bottom:20px;">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('formulario').classList.toggle('active')">
                    + Nuevo Usuario
                </button>
            </div>
            
            <div id="formulario" class="form-container <?php echo $mostrarFormulario || $editarUsuario ? 'active' : ''; ?>">
                <h3><?php echo $editarUsuario ? 'Editar Usuario' : 'Nuevo Usuario'; ?></h3>
                <form method="POST" action="" onsubmit="return validarFormulario(this);">
                    <?php if ($editarUsuario): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($editarUsuario['id']); ?>">
                        <input type="hidden" name="accion" value="actualizar">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="crear">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo $editarUsuario ? htmlspecialchars($editarUsuario['nombre']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" value="<?php echo $editarUsuario ? htmlspecialchars($editarUsuario['apellido']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" value="<?php echo $editarUsuario ? htmlspecialchars($editarUsuario['correo']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="celular">Celular</label>
                        <input type="text" id="celular" name="celular" value="<?php echo $editarUsuario ? htmlspecialchars($editarUsuario['celular']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn <?php echo $editarUsuario ? 'btn-success' : 'btn-primary'; ?>">
                            <?php echo $editarUsuario ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        
                        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn btn-danger">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="?orden=id&dir=<?php echo $columnaOrdenar('id'); ?>&buscar=<?php echo htmlspecialchars($busqueda); ?>&campo=<?php echo htmlspecialchars($campoBusqueda); ?>">
                                    ID <?php if($orden === 'id'): ?><span class="badge badge-<?php echo $direccion; ?>"><?php echo $direccion === 'ASC' ? '↑' : '↓'; ?></span><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?orden=nombre&dir=<?php echo $columnaOrdenar('nombre'); ?>&buscar=<?php echo htmlspecialchars($busqueda); ?>&campo=<?php echo htmlspecialchars($campoBusqueda); ?>">
                                    Nombre <?php if($orden === 'nombre'): ?><span class="badge badge-<?php echo $direccion; ?>"><?php echo $direccion === 'ASC' ? '↑' : '↓'; ?></span><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?orden=apellido&dir=<?php echo $columnaOrdenar('apellido'); ?>&buscar=<?php echo htmlspecialchars($busqueda); ?>&campo=<?php echo htmlspecialchars($campoBusqueda); ?>">
                                    Apellido <?php if($orden === 'apellido'): ?><span class="badge badge-<?php echo $direccion; ?>"><?php echo $direccion === 'ASC' ? '↑' : '↓'; ?></span><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?orden=correo&dir=<?php echo $columnaOrdenar('correo'); ?>&buscar=<?php echo htmlspecialchars($busqueda); ?>&campo=<?php echo htmlspecialchars($campoBusqueda); ?>">
                                    Correo <?php if($orden === 'correo'): ?><span class="badge badge-<?php echo $direccion; ?>"><?php echo $direccion === 'ASC' ? '↑' : '↓'; ?></span><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?orden=celular&dir=<?php echo $columnaOrdenar('celular'); ?>&buscar=<?php echo htmlspecialchars($busqueda); ?>&campo=<?php echo htmlspecialchars($campoBusqueda); ?>">
                                    Celular <?php if($orden === 'celular'): ?><span class="badge badge-<?php echo $direccion; ?>"><?php echo $direccion === 'ASC' ? '↑' : '↓'; ?></span><?php endif; ?>
                                </a>
                            </th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($usuarios) > 0): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['celular']); ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="?editar=<?php echo htmlspecialchars($usuario['id']); ?>" class="btn btn-primary">Editar</a>
                                            <form method="POST" action="" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <button type="button" class="btn btn-danger" onclick="confirmarEliminar(this)">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px;">
                                    No hay usuarios registrados
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        function validarFormulario(form) {
            var nombre = form.nombre.value.trim();
            var apellido = form.apellido.value.trim();
            var correo = form.correo.value.trim();
            var celular = form.celular.value.trim();
            
            if (!nombre || !apellido || !correo || !celular) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Todos los campos son obligatorios',
                    confirmButtonColor: '#667eea'
                });
                return false;
            }
            
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(correo)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El correo electrónico no es válido',
                    confirmButtonColor: '#667eea'
                });
                return false;
            }
            
            return true;
        }
        
        function confirmarEliminar(boton) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'El usuario será eliminado permanentemente',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#eb3349',
                cancelButtonColor: '#485563',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    boton.closest('form').submit();
                }
            });
        }
        
        <?php if ($mensaje): ?>
            Swal.fire({
                icon: '<?php echo $tipoMensaje === 'success' ? 'success' : 'error'; ?>',
                title: '<?php echo $tipoMensaje === 'success' ? 'Éxito' : 'Error'; ?>',
                text: '<?php echo addslashes($mensaje); ?>',
                confirmButtonColor: '<?php echo $tipoMensaje === 'success' ? '#11998e' : '#eb3349'; ?>'
            });
        <?php endif; ?>
    </script>
</body>
</html>
