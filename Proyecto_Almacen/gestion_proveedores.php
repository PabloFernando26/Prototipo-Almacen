<?php
session_start();
include('conexion.php');
include('header.php');

// Verifica que el usuario esté logeado y sea administrador
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: login.php");
    exit();
}

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Manejo de agregar, editar o eliminar proveedores
    if (isset($_POST['add'])) {
        // Agregar proveedor
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $contacto = $_POST['contacto'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $productos_ofrecidos = $_POST['productos_ofrecidos'];
        
        $query = "INSERT INTO proveedor (nombre, apellido, contacto, email, telefono, direccion, productos_ofrecidos)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        if ($stmt) {
            $stmt->bind_param("sssssss", $nombre, $apellido, $contacto, $email, $telefono, $direccion, $productos_ofrecidos);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['edit'])) {
        // Editar proveedor
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $contacto = $_POST['contacto'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $productos_ofrecidos = $_POST['productos_ofrecidos'];

        $query = "UPDATE proveedor SET nombre = ?, apellido = ?, contacto = ?, email = ?, telefono = ?, direccion = ?, productos_ofrecidos = ?
                  WHERE id = ?";
        $stmt = $conexion->prepare($query);
        if ($stmt) {
            $stmt->bind_param("sssssssi", $nombre, $apellido, $contacto, $email, $telefono, $direccion, $productos_ofrecidos, $id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['delete'])) {
        // Eliminar proveedor
        $id = $_POST['id'];
        $query = "DELETE FROM proveedor WHERE id = ?";
        $stmt = $conexion->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Consulta para mostrar proveedores
$query = "SELECT * FROM proveedor";
$resultado = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores - Almacén Benjamín</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Agregar Proveedor</h2>
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" class="form-control" name="apellido">
            </div>
            <div class="form-group">
                <label for="contacto">Contacto</label>
                <input type="text" class="form-control" name="contacto" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" class="form-control" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <textarea class="form-control" name="direccion"></textarea required>
            </div>
            <div class="form-group">
                <label for="productos_ofrecidos">Productos Ofrecidos</label required>
                <textarea class="form-control" name="productos_ofrecidos"></textarea>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Agregar Proveedor</button>
        </form>

        <h2 class="text-center mt-5">Lista de Proveedores</h2>
        <table class="table table-light table-striped table-hover shadow p-3 mb-5 bg-body-tertiary rounded">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Productos Ofrecidos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($proveedor = $resultado->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $proveedor['id']; ?></td>
                    <td><?php echo $proveedor['nombre'] . ' ' . $proveedor['apellido']; ?></td>
                    <td><?php echo $proveedor['contacto']; ?></td>
                    <td><?php echo $proveedor['email']; ?></td>
                    <td><?php echo $proveedor['telefono']; ?></td>
                    <td><?php echo $proveedor['productos_ofrecidos']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editarProveedor(<?php echo htmlspecialchars(json_encode($proveedor)); ?>)">Editar</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $proveedor['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Editar proveedor -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Proveedor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST">
                        <input type="hidden" name="id" id="editId">
                        <div class="form-group">
                            <label for="editNombre">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="editNombre" required>
                        </div>
                        <div class="form-group">
                            <label for="editApellido">Apellido</label>
                            <input type="text" class="form-control" name="apellido" id="editApellido">
                        </div>
                        <div class="form-group">
                            <label for="editContacto">Contacto</label>
                            <input type="text" class="form-control" name="contacto" id="editContacto" required>
                        </div>
                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" class="form-control" name="email" id="editEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="editTelefono">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="editTelefono">
                        </div>
                        <div class="form-group">
                            <label for="editDireccion">Dirección</label>
                            <textarea class="form-control" name="direccion" id="editDireccion"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editProductos">Productos Ofrecidos</label>
                            <textarea class="form-control" name="productos_ofrecidos" id="editProductos"></textarea>
                        </div>
                        <button type="submit" name="edit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function editarProveedor(proveedor) {
            document.getElementById('editId').value = proveedor.id;
            document.getElementById('editNombre').value = proveedor.nombre;
            document.getElementById('editApellido').value = proveedor.apellido;
            document.getElementById('editContacto').value = proveedor.contacto;
            document.getElementById('editEmail').value = proveedor.email;
            document.getElementById('editTelefono').value = proveedor.telefono;
            document.getElementById('editDireccion').value = proveedor.direccion;
            document.getElementById('editProductos').value = proveedor.productos_ofrecidos;

            $('#editModal').modal('show'); // Mostrar
        }
    </script>
</body>
</html>
