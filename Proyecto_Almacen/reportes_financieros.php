<?php
session_start();
include('conexion.php');
include('header.php');

// Verifica que el usuario esté logeado y sea administrador
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: login.php");
    exit();
}

// Obtener datos de ingresos mensuales, gastos mensuales y ganancias netas de la base de datos
$ingresosMensuales = array_fill(0, 12, 0); // Inicializar con ceros
$gastosMensuales = array_fill(0, 12, 0); // Inicializar con ceros
$gananciasNetasMensuales = [];
$ventasPorCategoria = [];

// Consulta de ingresos mensuales
$query = "SELECT MONTH(fecha) AS mes, SUM(total) AS total FROM venta WHERE YEAR(fecha) = 2024 GROUP BY mes ORDER BY mes";
$result = $conexion->query($query);
while ($row = $result->fetch_assoc()) {
    $ingresosMensuales[(int)$row['mes'] - 1] = (int)$row['total'];
}

// Consulta de gastos mensuales
$query = "SELECT MONTH(fecha) AS mes, SUM(monto) AS total FROM gastos WHERE YEAR(fecha) = 2024 GROUP BY mes ORDER BY mes";
$result = $conexion->query($query);
while ($row = $result->fetch_assoc()) {
    $gastosMensuales[(int)$row['mes'] - 1] = (int)$row['total'];
}

// Calcular ganancias netas mensuales
for ($i = 0; $i < 12; $i++) {
    $ingresos = $ingresosMensuales[$i];
    $gastos = $gastosMensuales[$i];
    $gananciasNetasMensuales[$i] = $ingresos - $gastos;
}

// Consulta de ventas por categoría
$query = "SELECT c.nombre AS categoria, SUM(v.total) AS total_venta
          FROM venta v
          JOIN producto p ON v.id = p.id
          JOIN categoria c ON p.categoria_id = c.id
          GROUP BY c.nombre";
$result = $conexion->query($query);
while ($row = $result->fetch_assoc()) {
    $ventasPorCategoria[] = [
        'categoria' => $row['categoria'],
        'total_venta' => (int)$row['total_venta']
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Financieros - Almacén Benjamín</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; }
        header { background-color: #4CAF50; color: white; padding: 1em 0; }
        .container { margin-top: 20px; }
        .card { height: 150px; } /* Altura fija para los cuadros */
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Reportes Financieros</h2>

        <!-- Cuadros para Totales Financieros -->
        <div class="row mt-4">
            <!-- Cuadro de Total Ingresos Mensuales -->
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Ingresos Mensuales</div>
                    <div class="card-body">
                        <h5 class="card-title">$<?php echo number_format(array_sum($ingresosMensuales), 0, ',', '.'); ?></h5>
                        <p class="card-text">Total de ingresos acumulados en el año 2024.</p>
                    </div>
                </div>
            </div>

            <!-- Cuadro de Total Gastos Mensuales -->
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Gastos Mensuales</div>
                    <div class="card-body">
                        <h5 class="card-title">$<?php echo number_format(array_sum($gastosMensuales), 0, ',', '.'); ?></h5>
                        <p class="card-text">Total de gastos acumulados en el año 2024.</p>
                    </div>
                </div>
            </div>

            <!-- Cuadro de Ganancias Netas Mensuales -->
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Ganancias Netas Mensuales</div>
                    <div class="card-body">
                        <h5 class="card-title">$<?php echo number_format(array_sum($gananciasNetasMensuales), 0, ',', '.'); ?></h5>
                        <p class="card-text">Ganancias netas acumuladas en el año 2024.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Financieros -->
        <div class="row mt-4">
            <div class="col-md-6">
                <h4>Ingresos Mensuales</h4>
                <canvas id="ingresosMensuales"></canvas>
            </div>
            <div class="col-md-6">
                <h4>Gastos Mensuales</h4>
                <canvas id="gastosMensuales"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <h4>Ganancias Netas Mensuales</h4>
                <canvas id="gananciasNetasMensuales"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos para los gráficos
        const ingresosMensualesData = <?php echo json_encode(array_values($ingresosMensuales)); ?>;
        const gastosMensualesData = <?php echo json_encode(array_values($gastosMensuales)); ?>;
        const gananciasNetasMensualesData = <?php echo json_encode(array_values($gananciasNetasMensuales)); ?>;
        const ventasPorCategoriaData = <?php echo json_encode(array_column($ventasPorCategoria, 'total_venta')); ?>;
        const ventasPorCategoriaLabels = <?php echo json_encode(array_column($ventasPorCategoria, 'categoria')); ?>;

        // Gráfico de Ingresos Mensuales
        const ctxIngresos = document.getElementById('ingresosMensuales').getContext('2d');
        new Chart(ctxIngresos, {
            type: 'line',
            data: {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                datasets: [{
                    label: 'Ingresos Mensuales ($CLP)',
                    data: ingresosMensualesData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true
                }]
            }
        });

        // Gráfico de Gastos Mensuales
        const ctxGastos = document.getElementById('gastosMensuales').getContext('2d');
        new Chart(ctxGastos, {
            type: 'line',
            data: {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                datasets: [{
                    label: 'Gastos Mensuales ($CLP)',
                    data: gastosMensualesData,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true
                }]
            }
        });

        // Gráfico de Ganancias Netas Mensuales
        const ctxGananciasNetas = document.getElementById('gananciasNetasMensuales').getContext('2d');
        new Chart(ctxGananciasNetas, {
            type: 'bar',
            data: {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                datasets: [{
                    label: 'Ganancias Netas Mensuales ($CLP)',
                    data: gananciasNetasMensualesData,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true
                }]
            }
        });
    </script>
</body>
</html>
