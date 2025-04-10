<?php
session_start();
require 'conexion.php';

$id_pedido = $_POST['id_pedido'];

// Obtener info del pedido y producto
$sql = "SELECT p.id_producto_proveedor, p.cantidad_solicitada, pr.stock
        FROM pedidos_tienda p
        JOIN productos_proveedor pr ON p.id_producto_proveedor = pr.id
        WHERE p.id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$resultado = $stmt->get_result();

if ($pedido = $resultado->fetch_assoc()) {
    $nuevo_stock = $pedido['stock'] - $pedido['cantidad_solicitada'];

    if ($nuevo_stock >= 0) {
        // Actualizar stock y marcar pedido como enviado
        $conexion->query("UPDATE productos_proveedor SET stock = $nuevo_stock WHERE id = {$pedido['id_producto_proveedor']}");
        $conexion->query("UPDATE pedidos_tienda SET estado = 'enviado' WHERE id = $id_pedido");

        // Aquí puedes también insertar el producto en la tienda si lo deseas
    }
}

header("Location: pedidos.php");
exit();
