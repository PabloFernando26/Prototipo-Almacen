<?php
include('conexion.php');
include('header.php');

// Consulta para obtener productos y sus fechas de caducidad
$sql = "SELECT * FROM producto WHERE fechaCaducidad > NOW()";
$result = mysqli_query($conexion, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

// Función para verificar si el stock es bajo
function alertaStockBajo($cantidad) {
    return $cantidad < 50; // Cambia el número según el umbral de stock bajo deseado
}

// Array para guardar los productos con stock bajo
$productosStockBajo = [];

// Procesar la eliminación de un producto
if (isset($_POST['eliminar'])) {
    $idEliminar = $_POST['id'];

    // Consulta para eliminar el producto
    $sqlEliminar = "DELETE FROM producto WHERE id = ?";
    $stmt = $conexion->prepare($sqlEliminar);
    $stmt->bind_param("i", $idEliminar);
    
    if ($stmt->execute()) {
        echo "<script>alert('Producto eliminado exitosamente'); window.location.href = 'control_caducidad.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el producto');</script>";
    }
}

// Procesar la edición de un producto
if (isset($_POST['editar'])) {
    $idEditar = $_POST['id'];
    $nombreEditar = $_POST['nombre'];
    $fechaCaducidadEditar = $_POST['fechaCaducidad'];
    $stockEditar = $_POST['stock'];

    // Consulta para actualizar el producto
    $sqlEditar = "UPDATE producto SET nombre = ?, fechaCaducidad = ?, stock = ? WHERE id = ?";
    $stmt = $conexion->prepare($sqlEditar);
    $stmt->bind_param("ssii", $nombreEditar, $fechaCaducidadEditar, $stockEditar, $idEditar);

    if ($stmt->execute()) {
        echo "<script>alert('Producto actualizado exitosamente'); window.location.href = 'control_caducidad.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el producto');</script>";
    }
}
?>

<div class="container">
    <h2 class="text-center">Control de Caducidad</h2>
    <table class="table table-light table-striped table-hover shadow p-3 mb-5 bg-body-tertiary rounded">
        <thead>
            <tr>
                <th>Nombre del Producto</th>
                <th>Fecha de Caducidad</th>
                <th>Cantidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($fila = mysqli_fetch_assoc($result)) {
                    $nombre = htmlspecialchars($fila['nombre']);
                    $fechaCaducidad = htmlspecialchars($fila['fechaCaducidad']);
                    $stock = htmlspecialchars($fila['stock']);
                    $id = $fila['id'];

                    // Verificar si el stock es bajo
                    $alerta = alertaStockBajo($stock) ? "style='color: red; font-weight: bold;'" : "";

                    // Agregar a productos de stock bajo
                    if (alertaStockBajo($stock)) {
                        $productosStockBajo[] = $nombre;
                    }

                    echo "<tr $alerta>";
                    echo "<td>$nombre</td>";
                    echo "<td>$fechaCaducidad</td>";
                    echo "<td>$stock</td>";
                    echo "<td>
                        <!-- Botón de edición con formulario modal -->
                        <button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editarModal$id'>Editar</button>
                        <!-- Botón de eliminación con formulario -->
                        <form action='' method='post' style='display:inline;'>
                            <input type='hidden' name='id' value='$id'>
                            <button type='submit' name='eliminar' class='btn btn-danger btn-sm'>Eliminar</button>
                        </form>
                    </td>";
                    echo "</tr>";
                    ?>

                    <!-- Modal para edición -->
                    <div class="modal fade" id="editarModal<?php echo $id; ?>" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editarModalLabel">Editar Producto</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <div class="form-group">
                                            <label>Nombre del Producto</label>
                                            <input type="text" name="nombre" class="form-control" value="<?php echo $nombre; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Fecha de Caducidad</label>
                                            <input type="date" name="fechaCaducidad" class="form-control" value="<?php echo $fechaCaducidad; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Cantidad</label>
                                            <input type="number" name="stock" class="form-control" value="<?php echo $stock; ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" name="editar" class="btn btn-primary">Guardar cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>No hay productos caducados.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Mostrar alerta de stock bajo -->
<?php if (!empty($productosStockBajo)) : ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            alert("¡Atención! Los siguientes productos tienen stock bajo y necesitan ser actualizados:\n\n<?php echo implode(', ', $productosStockBajo); ?>");
        });
    </script>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
