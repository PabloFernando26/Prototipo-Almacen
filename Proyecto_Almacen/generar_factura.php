<?php
include 'conexion.php'; 

if (isset($_GET['idFactura'])) {
    $idFactura = $_GET['idFactura'];

    // Obtener los detalles de la factura
    $sqlFactura = "SELECT * FROM factura WHERE id = ?";
    $stmt = $conexion->prepare($sqlFactura);
    $stmt->bind_param("i", $idFactura);
    $stmt->execute();
    $resultFactura = $stmt->get_result();
    $factura = $resultFactura->fetch_assoc();

    if ($factura) {
        // Obtener los productos de la venta
        $sqlProductos = "SELECT v.cantidad, p.nombre AS producto, p.precio, (v.cantidad * p.precio) AS total_producto
                         FROM venta v
                         JOIN producto p ON v.producto_id = p.id
                         WHERE v.boleta_id = ?";
                         
        $stmtProductos = $conexion->prepare($sqlProductos);
        $stmtProductos->bind_param("i", $factura['venta_id']);
        $stmtProductos->execute();
        $resultProductos = $stmtProductos->get_result();

        // Crear el contenido HTML de la factura
        $html = "
        <html>
        <head>
            <title>Factura #" . $factura['id'] . "</title>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f4f4f4;
                }
                .container {
                    max-width: 800px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .header h1 {
                    font-size: 32px;
                    margin-bottom: 10px;
                    color: #333;
                }
                .header p {
                    font-size: 16px;
                    color: #555;
                }
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                .table th, .table td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                .table th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                .table td {
                    background-color: #fafafa;
                }
                .table .total-row {
                    font-weight: bold;
                }
                .footer {
                    text-align: center;
                    margin-top: 40px;
                    font-size: 14px;
                    color: #888;
                }
                .footer p {
                    margin: 10px 0;
                }
                .footer .thank-you {
                    font-weight: bold;
                    color: #333;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Factura #" . $factura['id'] . "</h1>
                    <p><strong>Método de Pago:</strong> " . $factura['m_pago'] . "</p>
                    <p><strong>Detalles:</strong> " . $factura['detalles'] . "</p>
                </div>

                <table class='table'>
                    <tr>
                        <th>Total Neto</th>
                        <td>" . number_format($factura['f_neto'], 0, ',', '.') . " CLP</td>
                    </tr>
                    <tr>
                        <th>IVA</th>
                        <td>" . number_format($factura['f_iva'], 0, ',', '.') . " CLP</td>
                    </tr>
                    <tr class='total-row'>
                        <th>Total</th>
                        <td>" . number_format($factura['total'], 0, ',', '.') . " CLP</td>
                    </tr>
                </table>

                <h3>Productos</h3>
                <table class='table'>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>";

                    // Mostrar los productos de la factura
                    while ($producto = $resultProductos->fetch_assoc()) {
                        $html .= "
                        <tr>
                            <td>" . $producto['producto'] . "</td>
                            <td>" . $producto['cantidad'] . "</td>
                            <td>" . number_format($producto['precio'], 0, ',', '.') . " CLP</td>
                            <td>" . number_format($producto['total_producto'], 0, ',', '.') . " CLP</td>
                        </tr>";
                    }
                    
        $html .= "</tbody>
                </table>

                <div class='footer'>
                    <p class='thank-you'>Gracias por su compra.</p>
                    <p>Este es un documento electrónico, no es necesario imprimirlo.</p>
                </div>
            </div>
        </body>
        </html>";

        // Definir el nombre del archivo HTML
        $filename = "factura_" . $factura['id'] . ".html";

        // Establecer las cabeceras para que el archivo sea descargado como un HTML
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Imprimir el contenido HTML
        echo $html;
        exit;
    } else {
        echo "Factura no encontrada.";
    }
} else {
    echo "ID de factura no proporcionado.";
}
?>
