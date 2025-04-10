<?php
require 'conexion.php';

// Obtener los productos aceptados con los nombres y descripciones de la tabla productos_proveedor
$sql = "SELECT pt.id_producto, pp.nombre AS producto_nombre, pp.descripcion AS producto_descripcion
        FROM productos_tienda pt
        INNER JOIN productos_proveedor pp ON pt.id_producto = pp.id
        WHERE pt.estado = 'aceptado'"; // Filtra productos con estado 'aceptado'

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Tienda</title>
    <link rel="stylesheet" href="productos.css">
</head>
<body>
    <div class="container">
        <h1>Productos Disponibles</h1>
        <a href="cliente.php" class="back-btn">Volver al Panel</a>

        <!-- Mostrar lista de productos -->
        <form action="metodo_pago.php" method="POST">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Seleccionar</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="productos[]" value="<?= $row['id_producto'] ?>">
                                </td>
                                <td><?= $row['producto_nombre'] ?></td>
                                <td><?= $row['producto_descripcion'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn-comprar">Comprar</button>
            <?php else: ?>
                <p>No hay productos disponibles.</p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
