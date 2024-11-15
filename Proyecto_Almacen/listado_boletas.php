<?php
include 'conexion.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si se ha recibido el número de boleta para eliminar
if (isset($_POST['numBoleta'])) {
    $numBoleta = $_POST['numBoleta'];

    // Eliminar la boleta
    $sql = "DELETE FROM boleta WHERE numBoleta = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $numBoleta);

    if ($stmt->execute()) {
        // Establecer un mensaje de éxito en la sesión
        $_SESSION['mensaje'] = "Boleta eliminada exitosamente.";
    } else {
        // Establecer un mensaje de error en la sesión
        $_SESSION['mensaje'] = "Error al eliminar la boleta.";
    }
    
    $stmt->close();

    // Redirigir a la lista de boletas
    header("Location: listado_boletas.php");
    exit();
}

include 'header.php'; 
?>

<div class="container">
    <h3 class="text-center">Listado de Boletas Generadas</h3>
    
    <?php
    // Mostrar mensaje de éxito o error
    if (isset($_SESSION['mensaje'])) {
        echo '<div class="alert alert-info">'. $_SESSION['mensaje'] .'</div>';
        unset($_SESSION['mensaje']);
    }
    ?>

    <table class="table table-light table-striped table-hover shadow p-3 mb-5 bg-body-tertiary rounded">
        <thead class="thead-light">
            <tr>
                <th>Número de Boleta</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Método de Pago</th>
                <th>Articulo</th>
                <th>Cantidad</th>
                <th>Generar XML</th>
                <th>Eliminar</th> 
            </tr>
        </thead>
        <tbody>
            <?php
            // Obtener todas las boletas de la base de datos
            $sql = "SELECT 
                    boleta.numBoleta, 
                    boleta.fecha, 
                    boleta.total, 
                    boleta.metodoPago, 
                    GROUP_CONCAT(producto.nombre SEPARATOR ', ') AS productos, 
                    SUM(venta.cantidad) AS cantidad_total
                    FROM boleta
                    JOIN venta ON boleta.numBoleta = venta.boleta_id
                    JOIN producto ON venta.producto_id = producto.id
                    GROUP BY boleta.numBoleta, boleta.fecha, boleta.total, boleta.metodoPago";
            $resultado = $conexion->query($sql);

            while ($boleta = $resultado->fetch_assoc()) :
                ?>
                <tr>
                    <td><?php echo $boleta['numBoleta']; ?></td>
                    <td><?php echo $boleta['fecha']; ?></td>
                    <td><?php echo $boleta['total']; ?></td>
                    <td><?php echo $boleta['metodoPago']; ?></td>
                    <td><?php echo $boleta['productos']; ?></td>
                    <td><?php echo $boleta['cantidad_total']; ?></td>
                    <td>
                        <a href="generar_boleta.php?numBoleta=<?php echo $boleta['numBoleta']; ?>" class="btn btn-primary btn-sm">
                            Generar XML
                        </a>
                    </td>
                    <td>
                        <!-- Acción en blanco para el mismo archivo -->
                        <form action="" method="post" style="display:inline;"> 
                            <input type="hidden" name="numBoleta" value="<?php echo $boleta['numBoleta']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta boleta?');">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>


