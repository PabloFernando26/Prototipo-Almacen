<?php
session_start();
include('conexion.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $clave = $_POST['clave'];
    $rol = $_POST['rol'];

    $query = "SELECT * FROM usuario WHERE email = '$email' AND clave = '$clave' AND rol = '$rol'";
    $resultado = $conexion->query($query);
    $usuario = $resultado->fetch_assoc();

    if ($usuario) {
        $_SESSION['usuario'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        if ($rol === 'administrador') {
            header("Location: index.php");
        } else if ($rol === 'empleado') {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Credenciales incorrectas. Por favor, intente nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Almacén Benjamín</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Ingrese su email" required>
            </div>
            <div class="form-group">
                <label for="clave">Contraseña</label>
                <input type="password" class="form-control" name="clave" id="clave" placeholder="Ingrese su contraseña" required>
            </div>
            <div class="form-group">
                <label for="rol">Rol</label>
                <select class="form-control select-role" name="rol" id="rol" required>
                    <option value="" disabled selected>Seleccione su rol</option>
                    <option value="administrador">Administrador</option>
                    <option value="empleado">Empleado</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>