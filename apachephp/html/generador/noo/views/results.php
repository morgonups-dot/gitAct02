<?php
function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<table class="table table-striped table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Índice</th>
            <th>Número Aleatorio</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($numbers as $index => $number): ?>
        <tr>
            <td><?php echo escape($index + 1); ?></td>
            <td><?php echo escape($number); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="table-success fw-bold">
            <td>Suma</td>
            <td><?php echo escape($stats['sum']); ?></td>
        </tr>
        <tr class="table-success fw-bold">
            <td>Promedio</td>
            <td><?php echo escape(number_format($stats['average'], 2)); ?></td>
        </tr>
        <tr class="table-success fw-bold">
            <td>Mínimo</td>
            <td><?php echo escape($stats['min']); ?></td>
        </tr>
        <tr class="table-success fw-bold">
            <td>Máximo</td>
            <td><?php echo escape($stats['max']); ?></td>
        </tr>
    </tbody>
</table>
