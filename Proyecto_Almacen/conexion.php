<?php
$host = "34.176.116.156";  // IP del servidor
$usuario = "root";  // Usuario actualizado
$password = "Canela1606+";  // Contraseña actualizada
$base_datos = "almacen"; 

$conexion = @mysqli_connect($host, $usuario, $password, $base_datos);
// Verificar si la conexión fue exitosa
if (!$conexion) {
    echo "<div class='alert alert-danger'>Error: ".mysqli_connect_error()."</div>";
    exit();
}
?>
