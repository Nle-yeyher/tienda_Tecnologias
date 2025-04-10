<?php
require 'conexion.php';

// Obtener pedidos aceptados
$sql = "
    SELECT p.id_pedido, pr.id AS id_producto, pr.nombre, dp.cantidad
    FROM pedidos p
    INNER JOIN detalle_pedidos dp ON p.id_pedido = dp.id_pedido
    INNER JOIN productos_proveedor pr ON dp.id_producto = pr.id
    WHERE p.estado = 'aceptado'
    ORDER BY p.fecha DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Abastecer Tienda</title>
    <link rel="stylesheet" href="productos.css">
</head>
<body>
    <h1>Abastecer Tienda</h1>
    <a href="admin.php" class="btn">← Volver al Panel</a>

    <form action="procesar_abastecimiento.php" method="post">
        <table>
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Abastecer</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id_pedido'] ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= $row['cantidad'] ?></td>
                        <td>
                            <input type="checkbox" name="productos[]" value="<?= $row['id_producto'] ?>-<?= $row['cantidad'] ?>-<?= $row['nombre'] ?>">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit">Abastecer</button>
    </form>
</body>
</html>
