<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>

    <xsl:template match="/DTE">
        <html>
            <head>
                <title>Factura Electrónica</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 20px;
                        background-color: #f5f5f5;
                    }
                    .carta {
                        width: 80%;
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        background-color: white;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    }
                    h1, h2 {
                        color: #4CAF50;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #4CAF50;
                        color: white;
                    }
                    tr:nth-child(even) {
                        background-color: #f2f2f2;
                    }
                    .totales {
                        font-weight: bold;
                        margin-top: 20px;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="carta">
                    <div class="header">
                        <h1>Factura Electrónica</h1>
                        <h3>Almacén Benjamín</h3>
                    </div>
                    <xsl:apply-templates select="Documento"/>
                </div>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="Documento">
        <!-- Encabezado de la factura -->
        <h2>Encabezado</h2>
        <p><strong>Cliente:</strong> <xsl:value-of select="Encabezado/Receptor/RznSocRecep"/> (RUT: <xsl:value-of select="Encabezado/Receptor/RUTRecep"/>)</p>
        <p><strong>Emisor:</strong> <xsl:value-of select="Encabezado/Emisor/RznSocEmisor"/> (RUT: <xsl:value-of select="Encabezado/Emisor/RUTEmisor"/>)</p>

        <!-- Detalle de productos -->
        <h2>Detalle de Productos</h2>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Precio</th>
            </tr>
            <xsl:for-each select="Detalle/Producto">
                <tr>
                    <td><xsl:value-of select="Nombre"/></td>
                    <td><xsl:value-of select="Cantidad"/></td>
                    <td><xsl:value-of select="Precio"/></td>
                </tr>
            </xsl:for-each>
        </table>

        <!-- Totales de la factura -->
        <h2>Totales</h2>
        <div class="totales">
            <p>Monto Neto: <xsl:value-of select="Encabezado/Totales/MntTotal"/></p>
            <p>IVA: <xsl:value-of select="Encabezado/Totales/IVA"/></p>
            <p>Precio Final: <xsl:value-of select="Encabezado/Totales/PFinal"/></p>
            <p>Método de Pago: <xsl:value-of select="Encabezado/Totales/MtoPago"/></p>
        </div>
    </xsl:template>
</xsl:stylesheet>
