<?php
include('conexion.php');
include('header.php');

// Consulta para obtener las categorías
$categoriaQuery = "SELECT id, nombre FROM categoria";
$categoriaResult = mysqli_query($conexion, $categoriaQuery);

$mensaje = ''; // Variable para almacenar el mensaje de la alerta

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['nombreProducto'];
    $categoria_id = $_POST['categoriaProducto'];
    $precio = $_POST['precioProducto'];
    $cantidad = $_POST['cantidadProducto'];
    $fechaCaducidad = $_POST['fechaCaducidad'];

    // Validar que los datos no estén vacíos
    if (empty($nombre) || empty($categoria_id) || empty($precio) || empty($cantidad) || empty($fechaCaducidad)) {
        $mensaje = 'Todos los campos son obligatorios.';
    } else {
        // Convertir la fecha al formato MySQL (YYYY-MM-DD)
        $fechaCaducidad = DateTime::createFromFormat('d/m/Y', $fechaCaducidad)->format('Y-m-d');

        // Consulta para insertar el producto en la base de datos
        $sql = "INSERT INTO producto (nombre, categoria_id, precio, stock, fechaCaducidad) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);

        // Verificar si la preparación fue exitosa
        if ($stmt === false) {
            $mensaje = 'Error en la preparación de la consulta: ' . mysqli_error($conexion);
        } else {
            // Vincular los parámetros
            mysqli_stmt_bind_param($stmt, "ssdis", $nombre, $categoria_id, $precio, $cantidad, $fechaCaducidad);

            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                $mensaje = 'Producto registrado exitosamente.';
            } else {
                $mensaje = 'Error al registrar el producto: ' . mysqli_error($conexion);
            }

            // Cerrar la sentencia
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="container">
    <h2 class="text-center">Registro de Productos</h2>
    
    <!-- Mostrar mensaje de alerta -->
    <?php if ($mensaje != ''): ?>
        <div class="alert alert-info" role="alert">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulario de Registro de Productos -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="nombreProducto">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" placeholder="Ingrese el nombre del producto" required>
        </div>
        <div class="form-group">
            <label for="categoriaProducto">Categoría</label>
            <select class="form-control" id="categoriaProducto" name="categoriaProducto" required>
                <option value="">Seleccione una categoría</option>
                <?php
                while ($row = mysqli_fetch_assoc($categoriaResult)) {
                    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nombre']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="precioProducto">Precio</label>
            <input type="number" class="form-control" id="precioProducto" name="precioProducto" placeholder="Ingrese el precio del producto" required>
        </div>
        <div class="form-group">
            <label for="cantidadProducto">Cantidad</label>
            <input type="number" class="form-control" id="cantidadProducto" name="cantidadProducto" placeholder="Ingrese la cantidad del producto" required>
        </div>
        <div class="form-group">
            <label for="fechaCaducidad">Fecha de Caducidad</label>
            <input type="text" class="form-control datepicker" id="fechaCaducidad" name="fechaCaducidad" placeholder="Seleccione la fecha de caducidad" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Producto</button>
    </form>
</div>

<!-- Incluir jQuery y Bootstrap Datepicker -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">

<script>
    // Inicializar el Datepicker con configuración en español y formato dd/mm/yyyy
    $(document).ready(function() {
        $('#fechaCaducidad').datepicker({
            format: 'dd/mm/yyyy', // Formato de fecha Día/Mes/Año
            language: 'es', // Idioma en español
            startDate: 'today', // Iniciar desde el día actual
            autoclose: true // Cierra el calendario después de seleccionar la fecha
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
