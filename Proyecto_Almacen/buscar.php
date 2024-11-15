<?php
include('conexion.php');

if (isset($_POST['busqueda'])) {
    $busqueda = $conexion->real_escape_string($_POST['busqueda']);
    $query = "SELECT * FROM producto WHERE nombre LIKE '%$busqueda%' LIMIT 10";
    $resultado = $conexion->query($query);

    if ($resultado->num_rows > 0) {
        while ($producto = $resultado->fetch_assoc()) {
            echo "<p>" . htmlspecialchars($producto['nombre']) . 
                 " <button class='agregar-producto' data-id='" . $producto['id'] . "'>Agregar</button></p>";
        }
    } else {
        echo "<p>No se encontraron productos.</p>";
    }
}
?>