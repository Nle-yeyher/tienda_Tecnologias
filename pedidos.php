<?php
session_start();
require 'conexion.php';

$proveedor = $_SESSION['usuarios'];

$sql = "SELECT p.id AS id_pedido, pr.nombre, pr.stock, p.cantidad_solicitada, p.estado
        FROM pedidos_tienda p
        JOIN productos_proveedor pr ON p.id_producto_proveedor = pr.id
        WHERE pr.proveedor = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $proveedor);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<h2>Pedidos Recibidos</h2>
<table border="1">
    <tr>
        <th>Producto</th>
        <th>Cantidad Pedida</th>
        <th>Stock Disponible</th>
        <th>Estado</th>
        <th>Acción</th>
    </tr>

    <?php while ($pedido = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?= $pedido['nombre'] ?></td>
        <td><?= $pedido['cantidad_solicitada'] ?></td>
        <td><?= $pedido['stock'] ?></td>
        <td><?= ucfirst($pedido['estado']) ?></td>
        <td>
            <?php if ($pedido['estado'] === 'pendiente' && $pedido['stock'] >= $pedido['cantidad_solicitada']): ?>
                <form action="enviar_pedido.php" method="POST">
                    <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">
                    <input type="submit" value="Enviar a tienda">
                </form>
            <?php else: ?>
                <?= $pedido['estado'] === 'pendiente' ? 'Sin stock suficiente' : 'Enviado' ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="proveedor.php">Volver</a>