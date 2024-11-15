<?php
include 'conexion.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si se ha recibido el número de factura para eliminar
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Verificar si la factura existe
    $checkSql = "SELECT id FROM factura WHERE id = ?";
    $checkStmt = $conexion->prepare($checkSql);
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // La factura existe, proceder a eliminar
        $sql = "DELETE FROM factura WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Establecer un mensaje de éxito en la sesión
            $_SESSION['mensaje'] = "Factura eliminada exitosamente.";
        } else {
            // Establecer un mensaje de error en la sesión
            $_SESSION['mensaje'] = "Error al eliminar la factura: " . $conexion->error;
        }
        
        $stmt->close();
    } else {
        // Establecer un mensaje de error si no se encuentra la factura
        $_SESSION['mensaje'] = "Factura no encontrada.";
    }

    $checkStmt->close();

    header("Location: listado_facturas.php");
    exit();
}

include 'header.php'; 
?>

<div class="container">
    <h3 class="text-center">Listado de Facturas Generadas</h3>
    
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
                <th>ID Factura</th>
                <th>Total Neto</th>
                <th>IVA</th>
                <th>Total</th>
                <th>Método de Pago</th>
                <th>Detalles</th>
                <th>Generar HTML</th>
                <th>Eliminar</th> 
            </tr>
        </thead>
        <tbody>
            <?php
            // Obtener todas las facturas de la base de datos (sin límite de registros)
            $sql = "SELECT 
                    id, 
                    f_neto, 
                    f_iva, 
                    total, 
                    m_pago, 
                    detalles
                    FROM factura";

            // Ejecutar la consulta
            $resultado = $conexion->query($sql);

            // Verificar si la consulta fue exitosa
            if (!$resultado) {
                die("Error en la consulta: " . $conexion->error);
            }

            while ($factura = $resultado->fetch_assoc()) :
                ?>
                <tr>
                    <td><?php echo $factura['id']; ?></td>
                    <td><?php echo number_format($factura['f_neto'], 0, ',', '.'); ?></td> <!-- Mostrando como número entero -->
                    <td><?php echo number_format($factura['f_iva'], 0, ',', '.'); ?></td> <!-- Mostrando como número entero -->
                    <td><?php echo number_format($factura['total'], 0, ',', '.'); ?></td> <!-- Mostrando como número entero -->
                    <td><?php echo $factura['m_pago']; ?></td>
                    <td><?php echo $factura['detalles']; ?></td>
                    <td>
                        <a href="generar_factura.php?idFactura=<?php echo $factura['id']; ?>" class="btn btn-primary btn-sm">
                            Generar HTML
                        </a>
                    </td>
                    <td>
                        <form action="" method="post" style="display:inline;"> 
                            <input type="hidden" name="id" value="<?php echo $factura['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta factura?');">
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
