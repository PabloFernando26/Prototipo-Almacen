<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>

    <xsl:template match="/DTE">
        <html>
        <head>
            <title>Boleta Electrónica - Almacén Benjamín</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f9f9f9;
                }
                .container {
                    max-width: 800px;
                    margin: auto;
                    padding: 20px;
                    background: #fff;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                h1, h2, h3 {
                    text-align: center;
                    margin: 10px 0;
                }
                .header {
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }
                .store-name {
                    font-size: 1.5em;
                    font-weight: bold;
                    text-align: center;
                    margin: 10px 0;
                }
                .item {
                    display: flex;
                    justify-content: space-between;
                    padding: 5px 0;
                    border-bottom: 1px solid #ccc;
                }
                .totals {
                    font-weight: bold;
                    margin-top: 20px;
                }
                .total-line {
                    display: flex;
                    justify-content: space-between;
                    font-size: 1.1em;
                }
                .footer {
                    margin-top: 20px;
                    text-align: center;
                    font-size: 0.9em;
                    color: #555;
                }
            </style>

            
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1 class="store-name">Boleta Electrónica</h1>
                    <h3>Almacén Benjamín</h3>
                    <p>=====================================================================================</p>
                    <p>Local: Almacén Benjamín (Ventas de alimentos)</p>
                    <p>Dirección: Bartolome Blanche &amp; Dr. Ariel Díaz Rojas, Coquimbo</p>
                    <p>=====================================================================================</p>
                    <p>Emitido por: <xsl:value-of select="Documento/Encabezado/Receptor/RznSocRecep"/></p>
                    <p>Folio: <xsl:value-of select="Documento/Encabezado/IdDoc/Folio"/></p>
                    <p>Fecha: <xsl:value-of select="Documento/Encabezado/IdDoc/FchEmis"/></p>
                    <p>Método de Pago: <xsl:value-of select="Documento/Encabezado/Totales/MtoPago"/></p>
                </div>
                
                <div class="items">
                    <h3>Artículos Comprados:</h3>
                    <xsl:for-each select="Documento/Detalle">
                        <div class="item">
                            <span><xsl:value-of select="NmbItem"/> (Cantidad: <xsl:value-of select="QtyItem"/>)</span>
                            <span>(c/u) $<xsl:value-of select="PrcItem"/></span>
                        </div>
                    </xsl:for-each>
                </div>
                
                
                <xsl:variable name="neto">
                    <xsl:for-each select="Documento/Detalle">
                        <xsl:if test="position() = 1">
                            <xsl:value-of select="PrcItem * QtyItem"/>
                        </xsl:if>
                        <xsl:if test="position() > 1">
                            <xsl:value-of select="PrcItem * QtyItem + preceding-sibling::Detalle[position() = last()]/PrcItem * preceding-sibling::Detalle[position() = last()]/QtyItem"/>
                        </xsl:if>
                    </xsl:for-each>
                </xsl:variable>

                <xsl:variable name="iva" select="$neto * 0.19"/>
                <xsl:variable name="total" select="$neto + $iva"/>

                <div class="totals">
                    <h2>Totales</h2>
                    <div class="total-line">
                        <span>Subtotal Neto:</span>
                        <span>$<xsl:value-of select="Documento/Encabezado/Totales/MntTotal"/></span>
                    </div>
                    <div class="total-line">
                        <span>El IVA incluido en esta boletas es de:</span>
                        <span>$<xsl:value-of select="Documento/Encabezado/Totales/IVA"/></span>
                    </div>
                    <div class="total-line">
                        <span>Precio Final:</span>
                        <span>$<xsl:value-of select="Documento/Encabezado/Totales/PFinal"/></span>
                    </div>
                </div>

                <img src="https://www.simpleapi.cl/images/cs/articulos/TimbreElectronico.png" alt="Timbre Electrónico" style="width: 730px; display: block; margin: 20px auto;"/>
                <p>MERA SIMULACIÓN DE TIMBRE</p>

                <div class="footer">
                    <p>Gracias por su compra!</p>
                </div>
            </div>
        </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
