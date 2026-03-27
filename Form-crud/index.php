<?php
$archivo = 'personas.txt';

function cargarPersonas($archivo) {
    if (!file_exists($archivo)) {
        return [];
    }
    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true) ?? [];
}

function guardarPersonas($archivo, $personas) {
    file_put_contents($archivo, json_encode($personas, JSON_PRETTY_PRINT));
}

$personas = cargarPersonas($archivo);
$mensaje = '';
$personaEditar = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $edad = trim($_POST['edad'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $rfc = strtoupper(trim($_POST['rfc'] ?? ''));
    $editarIndex = $_POST['editar_index'] ?? null;

    if ($nombre && $rfc) {
        if ($editarIndex !== '' && $editarIndex !== null) {
            $personas[$editarIndex] = [
                'nombre' => $nombre,
                'email' => $email,
                'telefono' => $telefono,
                'edad' => $edad,
                'direccion' => $direccion,
                'rfc' => $rfc
            ];
            $mensaje = 'Persona actualizada correctamente';
        } else {
            $existe = false;
            foreach ($personas as $p) {
                if ($p['rfc'] === $rfc) {
                    $existe = true;
                    break;
                }
            }
            if ($existe) {
                $mensaje = 'Error: Ya existe una persona con ese RFC';
            } else {
                $personas[] = [
                    'nombre' => $nombre,
                    'email' => $email,
                    'telefono' => $telefono,
                    'edad' => $edad,
                    'direccion' => $direccion,
                    'rfc' => $rfc
                ];
                $mensaje = 'Persona registrada correctamente';
            }
        }
        guardarPersonas($archivo, $personas);
    } else {
        $mensaje = 'Error: Nombre y RFC son obligatorios';
    }
}

if (isset($_GET['eliminar'])) {
    $eliminarIndex = (int)$_GET['eliminar'];
    if (isset($personas[$eliminarIndex])) {
        unset($personas[$eliminarIndex]);
        $personas = array_values($personas);
        guardarPersonas($archivo, $personas);
        $mensaje = 'Persona eliminada correctamente';
    }
}

if (isset($_GET['editar'])) {
    $editarIndex = (int)$_GET['editar'];
    if (isset($personas[$editarIndex])) {
        $personaEditar = $personas[$editarIndex];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Personas</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { max-width: 900px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        h1 { text-align: center; color: #333; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        input:focus { outline: none; border-color: #007bff; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 5px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #545b62; }
        .mensaje { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .mensaje.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .mensaje.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f8f9fa; }
        .acciones { white-space: nowrap; }
        .empty { text-align: center; color: #999; padding: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <h1>CRUD de Personas</h1>
    <div class="container">
        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo strpos($mensaje, 'Error') === 0 ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <h2><?php echo $personaEditar ? 'Editar Persona' : 'Registrar Persona'; ?></h2>
        <form method="POST">
            <?php if ($personaEditar): ?>
                <input type="hidden" name="editar_index" value="<?php echo htmlspecialchars($_GET['editar']); ?>">
            <?php endif; ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($personaEditar['nombre'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($personaEditar['email'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($personaEditar['telefono'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edad">Edad</label>
                    <input type="number" id="edad" name="edad" value="<?php echo htmlspecialchars($personaEditar['edad'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($personaEditar['direccion'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="rfc">RFC *</label>
                <input type="text" id="rfc" name="rfc" required placeholder="RFC (10-13 caracteres)" value="<?php echo htmlspecialchars($personaEditar['rfc'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $personaEditar ? 'Actualizar' : 'Guardar'; ?></button>
            <?php if ($personaEditar): ?>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
        </form>

        <h2>Lista de Personas (<?php echo count($personas); ?>)</h2>
        <?php if (empty($personas)): ?>
            <p class="empty">No hay personas registradas</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Edad</th>
                        <th>Dirección</th>
                        <th>RFC</th>
                        <th class="acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personas as $index => $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($p['email']); ?></td>
                            <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($p['edad']); ?></td>
                            <td><?php echo htmlspecialchars($p['direccion']); ?></td>
                            <td><?php echo htmlspecialchars($p['rfc']); ?></td>
                            <td class="acciones">
                                <a href="?editar=<?php echo $index; ?>" class="btn btn-warning">Editar</a>
                                <a href="?eliminar=<?php echo $index; ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar esta persona?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
