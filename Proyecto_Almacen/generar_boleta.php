<?php

include 'conexion.php';

// ID de la boleta a generar
$numBoleta = isset($_GET['numBoleta']) ? (int)$_GET['numBoleta'] : 1;

// Consulta para obtener la boleta y sus productos relacionados
$sql = "SELECT b.numBoleta, b.fecha, b.total, b.metodoPago, v.producto_id, v.cantidad, p.nombre AS nombre_producto, p.precio 
        FROM boleta b
        JOIN venta v ON b.numBoleta = v.boleta_id
        JOIN producto p ON v.producto_id = p.id
        WHERE b.numBoleta = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $numBoleta);
$stmt->execute();
$resultado = $stmt->get_result();


// Verificar si se encontraron resultados
if ($resultado->num_rows > 0) {
    // Obtener la primera fila para realizar los cálculos
    $boletaInfo = $resultado->fetch_assoc();

    // Total sin IVA es el valor de 'total' en la base de datos
    $totalSinIVA = $boletaInfo['total'];
    $iva = round($totalSinIVA * 0.19); 
    $p_final = $totalSinIVA + $iva;

    // Crear XML
    $xml = new DOMDocument("1.0", "UTF-8");
    $xml->formatOutput = true;

    // Agregar referencia al archivo XSL
    $xml->appendChild($xml->createProcessingInstruction("xml-stylesheet", "type=\"text/xsl\" href=\"estilo.xsl\""));

    $dte = $xml->createElement("DTE");
    $dte->setAttribute("version", "1.0");
    $xml->appendChild($dte);

    $documento = $xml->createElement("Documento");
    $dte->appendChild($documento);

    // Encabezado
    $encabezado = $xml->createElement("Encabezado");
    $documento->appendChild($encabezado);

    $idDoc = $xml->createElement("IdDoc");
    $encabezado->appendChild($idDoc);
    $tipoDTE = $xml->createElement("TipoDTE", "39"); 
    $idDoc->appendChild($tipoDTE);
    $folio = $xml->createElement("Folio", $numBoleta);
    $idDoc->appendChild($folio);

    // Fecha de emisión
    $fchEmis = $xml->createElement("FchEmis", $boletaInfo['fecha']);
    $idDoc->appendChild($fchEmis);

    // Receptor
    $receptor = $xml->createElement("Receptor");
    $encabezado->appendChild($receptor);
    $nombreRecep = $xml->createElement("RznSocRecep", "Cliente");
    $receptor->appendChild($nombreRecep);
    $rutRecep = $xml->createElement("RUTRecep", "12345678-9"); 
    $receptor->appendChild($rutRecep);

    // Emisor
    $emisor = $xml->createElement("Emisor");
    $encabezado->appendChild($emisor);
    $nombreEmisor = $xml->createElement("RznSocEmisor", "Nombre de la Empresa");
    $emisor->appendChild($nombreEmisor);
    $rutEmisor = $xml->createElement("RUTEmisor", "98765432-1"); 
    $emisor->appendChild($rutEmisor);

    // Totales
    $totales = $xml->createElement("Totales");
    $encabezado->appendChild($totales);
    $montoTotal = $xml->createElement("MntTotal", $totalSinIVA); 
    $totales->appendChild($montoTotal);

    // Agregar IVA y precio final (p_final)
    $ivaXML = $xml->createElement("IVA", $iva);
    $totales->appendChild($ivaXML);
    $pFinalXML = $xml->createElement("PFinal", $p_final);
    $totales->appendChild($pFinalXML);

    // Método de pago
    $metodoPago = $xml->createElement("MtoPago", $boletaInfo['metodoPago']);
    $totales->appendChild($metodoPago);

    // Agregar detalles de cada producto en la boleta
    do {
        $detalle = $xml->createElement("Detalle");
        $documento->appendChild($detalle);

        $nmbItem = $xml->createElement("NmbItem", $boletaInfo['nombre_producto']);
        $detalle->appendChild($nmbItem);
        $qtyItem = $xml->createElement("QtyItem", $boletaInfo['cantidad']);
        $detalle->appendChild($qtyItem);
        $prcItem = $xml->createElement("PrcItem", $boletaInfo['precio']);
        $detalle->appendChild($prcItem);
    } while ($boletaInfo = $resultado->fetch_assoc());

    // Guardar XML en la carpeta "boletas"
    $rutaXML = "boletas/boleta_{$numBoleta}.xml";
    $xml->save($rutaXML);

    echo "Boleta generada correctamente: <a href='$rutaXML'>Descargar XML</a>";
} else {
    echo "No se encontró la boleta.";
}

$stmt->close();
$conexion->close();
?>


