<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['productos'])) {
    foreach ($_POST['productos'] as $productoInfo) {
        list($id_producto, $cantidad, $nombre) = explode("-", $productoInfo);

        // Verificar si ya existe en tienda
        $check = $conn->prepare("SELECT id, stock FROM productos_tienda WHERE nombre = ?");
        $check->bind_param("s", $nombre);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $nuevo_stock = $row['stock'] + intval($cantidad);

            // Actualizar stock
            $update = $conn->prepare("UPDATE productos_tienda SET stock = ? WHERE id = ?");
            $update->bind_param("ii", $nuevo_stock, $row['id']);
            $update->execute();
        } else {
            // Insertar nuevo producto
            $insert = $conn->prepare("INSERT INTO productos_tienda (nombre, stock) VALUES (?, ?)");
            $insert->bind_param("si", $nombre, $cantidad);
            $insert->execute();
        }

        // 📌 Registrar abastecimiento en la tabla para el reporte
        $log = $conn->prepare("INSERT INTO abastecimientos (nombre_producto, cantidad) VALUES (?, ?)");
        $log->bind_param("si", $nombre, $cantidad);
        $log->execute();
    }

    echo "<script>alert('Productos abastecidos correctamente.'); window.location.href='abastecer_tienda.php';</script>";
} else {
    echo "<script>alert('No seleccionaste ningún producto.'); window.location.href='abastecer_tienda.php';</script>";
}
