<?php
require 'conexion.php';

// Si el proveedor ha respondido un pedido
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['pedido_id'], $_POST['accion'])) {
    $pedido_id = $_POST['pedido_id'];
    $accion = $_POST['accion']; // 'aceptado' o 'rechazado'

    if ($accion === "aceptado") {
        // Verificar si hay suficiente stock
        $sql_detalles = "
            SELECT id_producto_proveedor, cantidad_solicitada 
            FROM pedidos_tienda 
            WHERE id = ? 
        ";
        $stmt_detalle = $conn->prepare($sql_detalles);
        $stmt_detalle->bind_param("i", $pedido_id);
        $stmt_detalle->execute();
        $result_detalle = $stmt_detalle->get_result();

        $stock_suficiente = true;

        while ($detalle = $result_detalle->fetch_assoc()) {
            $id_producto = $detalle['id_producto_proveedor'];
            $cantidad = $detalle['cantidad_solicitada'];

            // Verificar el stock disponible en productos_proveedor
            $check = $conn->prepare("SELECT stock FROM productos_proveedor WHERE id = ?");
            $check->bind_param("i", $id_producto);
            $check->execute();
            $res = $check->get_result();
            $row = $res->fetch_assoc();

            if (!$row || $row['stock'] < $cantidad) {
                $stock_suficiente = false;
                break;
            }
        }

        if ($stock_suficiente) {
            // Reiniciar resultado para volver a iterar y agregar a productos_tienda
            $stmt_detalle->execute();
            $result_detalle = $stmt_detalle->get_result();

            while ($detalle = $result_detalle->fetch_assoc()) {
                $id_producto = $detalle['id_producto_proveedor'];
                $cantidad = $detalle['cantidad_solicitada'];

                // Insertar en productos_tienda con id_producto_proveedor
                $insert_producto = $conn->prepare("
                    INSERT INTO productos_tienda (id_producto, id_pedido, cantidad, estado) 
                    VALUES (?, ?, ?, 'aceptado')
                ");
                // Cambiar la referencia a id_producto (que es la columna que usamos en productos_tienda)
                $insert_producto->bind_param("iii", $id_producto, $pedido_id, $cantidad);
                $insert_producto->execute();

                // Actualizar el stock de los productos en productos_proveedor
                $update_stock = $conn->prepare("
                    UPDATE productos_proveedor 
                    SET stock = stock - ? 
                    WHERE id = ?
                ");
                $update_stock->bind_param("ii", $cantidad, $id_producto);
                $update_stock->execute();
            }

            // Actualizar estado del pedido a "aceptado"
            $update = $conn->prepare("UPDATE pedidos_tienda SET estado = 'aceptado' WHERE id = ?");
            $update->bind_param("i", $pedido_id);
            $update->execute();

            echo "<script>alert('Producto aceptado y stock actualizado. Pedido movido a productos_tienda.'); window.location.href='responder_pedido.php';</script>";
        } else {
            echo "<script>alert('No hay stock suficiente para aceptar este pedido.'); window.location.href='responder_pedido.php';</script>";
        }
    } elseif ($accion === "rechazado") {
        // Actualizar estado a "rechazado"
        $update = $conn->prepare("UPDATE pedidos_tienda SET estado = 'rechazado' WHERE id = ?");
        $update->bind_param("i", $pedido_id);
        $update->execute();

        echo "<script>alert('Pedido rechazado.'); window.location.href='responder_pedido.php';</script>";
    }
}

// ID del proveedor (esto debe ser obtenido del sistema de login)
$proveedor_id = 1;  // Cambia esto según el ID del proveedor autenticado

// Consulta de pedidos pendientes para este proveedor
$sql_pedidos = "
    SELECT pt.id AS pedido_id, pp.nombre, pt.cantidad_solicitada, pt.estado, pt.fecha
    FROM pedidos_tienda pt
    INNER JOIN productos_proveedor pp ON pt.id_producto_proveedor = pp.id
    WHERE pt.estado = 'pendiente'
    ORDER BY pt.fecha DESC
";

$stmt = $conn->prepare($sql_pedidos);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Responder Pedidos</title>
    <link rel="stylesheet" href="productos.css">
</head>
<body>
    <h1>Pedidos Recibidos</h1>
    <a href="proveedor.php" class="btn">← Volver al Panel</a>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= $row['cantidad_solicitada'] ?></td>
                        <td><?= $row['fecha'] ?></td>
                        <td><?= ucfirst($row['estado']) ?></td>
                        <td>
                            <?php if ($row['estado'] === 'pendiente'): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="pedido_id" value="<?= $row['pedido_id'] ?>">
                                    <button type="submit" name="accion" value="aceptado">Aceptar</button>
                                    <button type="submit" name="accion" value="rechazado">Rechazar</button>
                                </form>
                            <?php else: ?>
                                <em><?= ucfirst($row['estado']) ?></em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay pedidos pendientes.</p>
    <?php endif; ?>

</body>
</html>
