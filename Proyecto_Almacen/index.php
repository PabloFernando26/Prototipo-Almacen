<?php
include('conexion.php');
include('header.php');
?>

<div class="container mt-5">
    <h2 class="text-center">Bienvenido al Almacén Benjamín</h2>
    <p class="text-center">Seleccione una opción del menú para comenzar.</p>

    <div class="row text-center mt-4">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm" style="height: 100%;">
                <div class="card-header bg-primary text-white">
                    <h4 class="my-0 font-weight-normal">Registro de Productos</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Administra los productos disponibles en el almacén.</p>
                    <a href="registro_productos.php" class="btn btn-light">Ir al registro</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm" style="height: 100%;">
                <div class="card-header bg-success text-white">
                    <h4 class="my-0 font-weight-normal">Registro de Ventas</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Registra y gestiona las ventas realizadas.</p>
                    <a href="registro_ventas.php" class="btn btn-light">Ir al registro</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm" style="height: 100%;">
                <div class="card-header bg-info text-white">
                    <h4 class="my-0 font-weight-normal">Control de Existencias</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Monitorea los productos cercanos a su fecha de caducidad.</p>
                    <a href="control_caducidad.php" class="btn btn-light">Ir al control</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row text-center mt-4">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm" style="height: 100%;">
                <div class="card-header bg-warning text-white">
                    <h4 class="my-0 font-weight-normal">Gestión de Proveedores</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Administra los proveedores del almacén.</p>
                    <a href="gestion_proveedores.php" class="btn btn-light">Ir a gestión</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm" style="height: 100%;">
                <div class="card-header bg-danger text-white">
                    <h4 class="my-0 font-weight-normal">Reportes Financieros</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Estadísticas financieras.</p>
                    <a href="reportes_financieros.php" class="btn btn-light">Ver reportes financieros</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm" style="height: 100%;">
                <div class="card-header bg-secondary text-white">
                    <h4 class="my-0 font-weight-normal">Listado de Boletas</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Consulta y genera boletas electrónicas.</p>
                    <a href="listado_boletas.php" class="btn btn-light">Ver boletas</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
