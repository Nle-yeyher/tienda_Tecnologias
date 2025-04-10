<?php
session_start();
require 'conexion.php';

// Verificar que el formulario se haya enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $metodo_pago = $_POST['metodo_pago'];

    // Verificar si el carrito tiene productos
    if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) == 0) {
        header("Location: pclientes.php");
        exit();
    }

    // Procesar la compra según el método de pago seleccionado
    if ($metodo_pago == 'tarjeta_credito') {
        // Aquí iría la lógica para procesar pago con tarjeta
        $mensaje_pago = "Pago procesado con tarjeta de crédito.";
    } elseif ($metodo_pago == 'paypal') {
        // Aquí iría la lógica para procesar pago con PayPal
        $mensaje_pago = "Pago procesado con PayPal.";
    } elseif ($metodo_pago == 'transferencia_bancaria') {
        // Aquí iría la lógica para procesar pago con transferencia bancaria
        $mensaje_pago = "Pago procesado mediante transferencia bancaria.";
    }

    // Redirigir al usuario a la página de confirmación de pago
    // Primero, guardamos el detalle del pedido (esto depende de cómo guardes los pedidos en tu base de datos)
    $user_id = $_SESSION['user_id']; // Suponiendo que tienes el ID del usuario en la sesión
    $total = 0;
    
    foreach ($_SESSION['carrito'] as $producto) {
        $total += $producto['precio'] * $producto['cantidad'];
    }

    $sql = "INSERT INTO pedidos (id_usuario, total, metodo_pago, estado) 
            VALUES ('$user_id', '$total', '$metodo_pago', 'pendiente')";
    $conn->query($sql);

    // Aquí puedes guardar los detalles de los productos que el usuario compró
    $id_pedido = $conn->insert_id; // ID del último pedido insertado
    foreach ($_SESSION['carrito'] as $producto) {
        $id_producto = $producto['id_producto'];
        $cantidad = $producto['cantidad'];

        $sql_detalle = "INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad) 
                        VALUES ('$id_pedido', '$id_producto', '$cantidad')";
        $conn->query($sql_detalle);
    }

    // Limpiar el carrito después de procesar el pago
    unset($_SESSION['carrito']);
    
    // Mostrar mensaje de confirmación
    echo "<h1>Compra realizada con éxito</h1>";
    echo "<p>$mensaje_pago</p>";
    echo "<a href='pclientes.php' class='btn-volver'>Volver al Panel</a>";
}
?>
