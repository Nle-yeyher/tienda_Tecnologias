<?php
// Inicia sesión si estás usando sesiones
// session_start();
require 'conexion.php';

// Validamos que vengan productos desde el formulario anterior
if (!isset($_POST['productos']) || empty($_POST['productos'])) {
    echo "<script>alert('No seleccionaste ningún producto.'); window.location.href = 'productos.php';</script>";
    exit;
}

// Guardamos los productos seleccionados
$productos_seleccionados = $_POST['productos'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Método de Pago</title>
    <link rel="stylesheet" href="metodo_pago.css">
</head>
<body>
    <div class="container">
        <h1>Selecciona tu Método de Pago</h1>

        <form action="procesar_compra.php" method="POST">
            <!-- Enviar los productos seleccionados como un campo oculto -->
            <?php foreach ($productos_seleccionados as $producto): ?>
                <input type="hidden" name="productos[]" value="<?= htmlspecialchars($producto) ?>">
            <?php endforeach; ?>

            <div class="metodo-pago">
                <label><input type="radio" name="metodo_pago" value="efectivo" required> Pago en Efectivo</label><br>
                <label><input type="radio" name="metodo_pago" value="tarjeta" required> Pago con Tarjeta</label>
            </div>

            <button type="submit" class="btn-finalizar-compra">Finalizar Compra</button>
        </form>
    </div>
</body>
</html>
