<?php
$edad = 25;
$tiene_licencia = true;

// AND: ambos deben cumplirse
if ($edad >= 18 && $tiene_licencia) {
    echo "Puede conducir<br>";
}

// OR: al menos uno
if ($edad < 18 || !$tiene_licencia) {
    echo "No puede conducir";
}
?>
