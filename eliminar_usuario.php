<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Preparar la consulta para eliminar al usuario
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Usuario eliminado exitosamente'); window.location.href='gestionar.php';</script>";
    } else {
        echo "Error al eliminar el usuario: " . $stmt->error;
    }
} else {
    echo "No se proporcionó un ID válido.";
}
?>
