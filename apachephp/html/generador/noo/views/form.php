<?php
$defaults = [
    'n' => 10,
    'min' => 1,
    'max' => 10000
];
$values = array_merge($defaults, $data ?? []);
?>
<form method="POST" action="./index.php" class="mb-4">
    <div class="mb-3">
        <label for="n" class="form-label">Cantidad de números (N):</label>
        <input type="number" class="form-control" id="n" name="n" value="<?php echo htmlspecialchars($values['n'], ENT_QUOTES, 'UTF-8'); ?>" min="1" max="1000" required>
    </div>
    <div class="mb-3">
        <label for="min" class="form-label">Mínimo (opcional):</label>
        <input type="number" class="form-control" id="min" name="min" value="<?php echo htmlspecialchars($values['min'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="mb-3">
        <label for="max" class="form-label">Máximo (opcional):</label>
        <input type="number" class="form-control" id="max" name="max" value="<?php echo htmlspecialchars($values['max'], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <button type="submit" class="btn btn-primary">Generar</button>
</form>
