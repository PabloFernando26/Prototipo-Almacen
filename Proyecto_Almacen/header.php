<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacén Benjamín</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>
    <header class="text-center">
        <h1>Almacén Benjamín</h1>
        <p>Gestión Operativa</p>
    </header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Inicio</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="registro_productos.php">Registro de Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="control_caducidad.php">Control de Caducidad</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="registro_ventas.php">Registro de Ventas</a> 
                </li>
                
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_proveedores.php">Gestión de Proveedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes_financieros.php">Reportes Financieros</a> 
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="listado_boletas.php">Listado de Boletas</a>
                </li>
                <li>
                    <a class="nav-link" href="listado_facturas.php">Listado de Facturas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cerrar_sesion.php"><i class="bi bi-box-arrow-left"> Cerrar sesión</i></a>
                </li>
            </ul>
        </div>
    </nav>
    <body>
        <style>
        body {
            background-image: url("https://img.freepik.com/premium-vector/white-pattern-background_393744-193.jpg?w=360");
            }
        </style>
    </body>
