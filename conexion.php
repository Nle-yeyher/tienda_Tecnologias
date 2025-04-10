<?php
$host = "localhost";
$usuario = "root";
$contrasena = "";
$basededatos = "tienda_tecnologia"; // <-- tu base real

$conn = new mysqli($host, $usuario, $contrasena, $basededatos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
