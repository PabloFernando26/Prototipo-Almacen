<?php
include('conexion.php');
include('header.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener lista de productos para el menú desplegable
$query = "SELECT id, nombre FROM producto";
$resultado = $conexion->query($query);
$productos = $resultado->fetch_all(MYSQLI_ASSOC);

$producto = null;

if (isset($_POST['nombre_producto'])) {
    $nombre_producto = $_POST['nombre_producto'];
    $cantidad = $_POST['cantidad'];

    // Buscar el producto seleccionado
    $query = "SELECT * FROM producto WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $nombre_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();

    if ($producto) {
        $precio_unitario = $producto['precio'];
        $total_producto = $precio_unitario * $cantidad;

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        $_SESSION['carrito'][] = [
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'total' => $total_producto
        ];

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<p>Producto no encontrado.</p>";
    }
}

if (isset($_POST['eliminar_producto'])) {
    $index = $_POST['eliminar_producto'];
    unset($_SESSION['carrito'][$index]);
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
}

if (isset($_POST['cancelar_venta'])) {
    unset($_SESSION['carrito']);
}

if (isset($_POST['confirmar_venta']) && isset($_POST['metodo_pago'])) {
    $metodo_pago = $_POST['metodo_pago'];

    if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0) {
        $total_boleta = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total_boleta += $item['total'];
        }

        $iva = $total_boleta * 0.19;
        $precio_final = $total_boleta + $iva;

        $queryBoleta = "INSERT INTO boleta (fecha, total, iva, p_final, metodoPago) VALUES (NOW(), $total_boleta, $iva, $precio_final, '$metodo_pago')";
        if ($conexion->query($queryBoleta) === TRUE) {
            $boleta_id = $conexion->insert_id;

            foreach ($_SESSION['carrito'] as $item) {
                $producto_id = $item['id'];
                $cantidad = $item['cantidad'];
                $total = $item['total'];

                $query = "INSERT INTO venta (producto_id, fecha, cantidad, total, metodoPago, boleta_id) VALUES ($producto_id, NOW(), $cantidad, $total, '$metodo_pago', $boleta_id)";
                if (!$conexion->query($query)) {
                    echo "<p>Error al insertar la venta: " . $conexion->error . "</p>";
                }

                $query = "UPDATE producto SET stock = stock - $cantidad WHERE id = $producto_id";
                $conexion->query($query);
            }

            $detalles = "Venta de productos";
            $f_neto = $total_boleta;
            $total_factura = $precio_final;
            $queryFactura = "INSERT INTO factura (detalles, f_neto, f_iva, total, m_pago, venta_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($queryFactura);

            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }

            $stmt->bind_param("sddssi", $detalles, $f_neto, $iva, $total_factura, $metodo_pago, $boleta_id);

            if ($stmt->execute()) {
                $factura_id = $conexion->insert_id;
                echo "<script>alert('Venta realizada con éxito. Número de Boleta: $boleta_id y Factura ID: $factura_id');</script>";
            } else {
                echo "<p>Error al crear la factura: " . $stmt->error . "</p>";
            }

            unset($_SESSION['carrito']);
        } else {
            echo "<p>Error al crear la boleta: " . $conexion->error . "</p>";
        }
    } else {
        echo "<script>alert('No hay productos en el carrito para realizar la venta.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ventas - Almacén Benjamín</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <style>
        .payment-option {
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            padding: 15px;
            cursor: pointer;
            transition: 0.3s;
            width: 120px;
            margin: 10px;
            display: inline-block;
        }

        .payment-option img {
            width: 50px;
            height: auto;
            margin-bottom: 8px;
        }

        .payment-option.selected {
            border-color: #007bff;
            background-color: #e9f7ff;
        }

        .payment-options-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>

    <div class="container">
        <h2 class="text-center">Registro de Ventas</h2>
        <form method="POST" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="nombre_producto">Nombre del Producto</label>
                    <select class="form-control" name="nombre_producto" id="nombre_producto" required>
                        <option value="">Seleccione un producto</option>
                        <?php
                        foreach ($productos as $producto) {
                            echo "<option value='" . $producto['id'] . "'>" . $producto['nombre'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" class="form-control" name="cantidad" id="cantidad" value="1" min="1" required>
                </div>
                <div class="form-group col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">Agregar Producto</button>
                </div>
            </div>
        </form>

        <h3>Productos en la Venta</h3>
        <table class="table table-light table-striped table-hover shadow p-3 mb-5 bg-body-tertiary rounded">
            <thead>
                <tr>
                    <th>N°Producto</th>
                    <th>ID Producto</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_venta = 0;
                if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
                    foreach ($_SESSION['carrito'] as $index => $item) {
                        echo "<tr>";
                        echo "<td>" . ($index + 1) . "</td>";
                        echo "<td>" . $item['id'] . "</td>";
                        echo "<td>" . $item['nombre'] . "</td>";
                        echo "<td>" . $item['cantidad'] . "</td>";
                        echo "<td>$" . $item['precio_unitario'] . "</td>";
                        echo "<td>$" . $item['total'] . "</td>";
                        echo "<td><form method='POST'><button type='submit' name='eliminar_producto' value='$index' class='btn btn-danger btn-sm'>Eliminar</button></form></td>";
                        echo "</tr>";
                        $total_venta += $item['total'];
                    }
                }
                ?>
                <tr>
                    <td colspan="5" class="text-right font-weight-bold">Total Venta</td>
                    <td>$<?php echo $total_venta; ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#metodoPagoModal">Seleccionar Método de Pago</button>

        <!-- Modal para seleccionar el método de pago -->
        <div class="modal fade" id="metodoPagoModal" tabindex="-1" role="dialog" aria-labelledby="metodoPagoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="metodoPagoModalLabel">Seleccionar Método de Pago</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="confirmar_venta" value="1">
                            <div class="payment-options-container">
                                <label class="payment-option">
                                    <input type="radio" name="metodo_pago" value="efectivo" required hidden>
                                    <img src="images/efectivo.png" alt="Efectivo">
                                    <p>Efectivo</p>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="metodo_pago" value="debito" required hidden>
                                    <img src="images/debito.png" alt="Débito">
                                    <p>Débito</p>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="metodo_pago" value="credito" required hidden>
                                    <img src="images/credito.png" alt="Crédito">
                                    <p>Crédito</p>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block mt-4">Confirmar Venta</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" class="mt-3">
            <button type="submit" name="cancelar_venta" class="btn btn-secondary">Cancelar Venta</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".payment-option").click(function () {
                $(".payment-option").removeClass("selected");
                $(this).addClass("selected");
                $(this).find("input[type='radio']").prop("checked", true);
            });
        });
    </script>

</body>
</html>
