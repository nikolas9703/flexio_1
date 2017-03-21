
        <style type="text/css">
            .container {
                font-family: Arial;
                font-size: 11pt;
                white-space: normal;
            }
            .titulo1 {
                font-size: 16pt;
            }
            .titulo2 {
                font-size: 12pt;
            }
            table {
                width: 100%;
                padding-bottom: 15px;
                font-size: 11pt;
            }
            .halfwidth {
                width: 60%;
            }
            .titulotabla{
                background: #2F75B5;
                color: white;
                border: 1px solid black;
                margin: 0px;
                padding: 5px;
                text-align: center;
            }
        </style>

    <div class='container'>
        <div id="header">
            <table>
                <tr>
                    <td class='halfwidth' ><img style='position: absolute' src="<?php $logo = !empty($pago->empresa->logo) ? $pago->empresa->logo : 'default.jpg' ; echo $this->config->item('logo_path').$logo; ?>" height="56.96px" /></td>
                    <td class="titulo1">PAGO</td>
                </tr>
                <tr>
                    <td></td>
                    <td>No. de pago: <b><?php echo $pago->codigo; ?></b></td>
                </tr>
            </table>
        </div>
        <div id="detalle">
            <table>
                <tr>
                    <td class='halfwidth titulo2'><b><?php echo $pago->empresa->nombre; ?></b></td>
                    <td>Fecha de impresión: <?php echo date('d-m-Y', time())?></td>
                </tr>
                <tr>
                    <td>RUC: <?php echo $pago->empresa->getRuc(); ?></td>
                    <td>Aplicado por: <?php 
                    $usuario_logged = $this->session->userdata['nombre']. ' '.$this->session->userdata['apellido'];
                    echo isset($history->nombre) ? $history->nombre : $usuario_logged ?></td>
                </tr>
                <tr>
                    <td>Dirección: <?php echo $pago->empresa->descripcion ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Teléfono: <?php echo $pago->empresa->telefono ?></td>
                </tr>
            </table>
        </div>
                <div id="separator"><table>
                    <tr>
                    <td colspan="6" style="border-bottom: 2px solid black;"></td>
                </tr>
            </table></div>
        <div id="datosdelpago">
            <table style="padding-top: 15px">
                <tr><td class='titulo2' style="text-decoration: underline;"><b>Datos del Pago</b></td></tr>
                <tr><td>Proveedor: <b><?php echo $pago->proveedor->nombre ?></b></td></tr>
                <tr><td>Fecha de pago: <b><?php echo $pago->fecha_pago ?></b></td></tr>
            </table>
            
        </div>
        <div id="tabladeinfo">
            <p><table style='table-layout: fixed;font-size: 9pt;' cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <td class="titulotabla">No. Factura</td>
                        <td class="titulotabla">Fecha de emisi&oacute;n</td>
                        <td class="titulotabla">Monto</td>
                        <td class="titulotabla">Pagado</td>
                        <td class="titulotabla">Saldo pendiente</td>
                        <td class="titulotabla">Pago</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if($pago->empezable_type == 'anticipo'):?>
                    <?php foreach($pago->anticipo as $anticipo):?>
                
                    <tr>

                        <td style="text-align: center"><?php echo $anticipo->codigo ?></td>
                        <td style="text-align: center"><?php echo $anticipo->fecha_anticipo ?></td>
                        <td style="text-align: right">$<?php echo number_format($anticipo->monto, 2) ?></td>
                        <td style="text-align: right">$<?php echo number_format($anticipo->suma_pagos, 2) ?></td>
                        <td style="text-align: right">$<?php echo number_format($anticipo->saldo, 2) ?></td>
                        <td style="text-align: right">$<?php echo number_format($anticipo->pivot->monto_pagado, 2) ?></td>
                    </tr>
                    <?php endforeach;?>
                    <?php else: ?>
                    
                    <?php foreach($pago->facturas as $factura):?>
                
                    <tr>

                        <td style="text-align: center"><?php echo $factura->codigo ?></td>
                        <td style="text-align: center"><?php echo $factura->fecha_desde ?></td>
                        <td style="text-align: right">$<?php echo number_format($factura->total, 2) ?></td>
                        <td style="text-align: right">$<?php echo number_format($factura->pagos_aplicados_suma, 2) ?></td>
                        <td style="text-align: right">$<?php echo number_format($factura->saldo, 2) ?></td>
                        <td style="text-align: right">$<?php echo number_format($factura->pivot->monto_pagado, 2) ?></td>
                    </tr>
                    <?php endforeach;?>
                    <?php endif; ?>
                </tbody>
            </table></p>
        </div>
        <div id="detalledetabla">
            <table>
                <tr>
                    <td style="width: 50%"> </td>
                    <td>
                         <table>
                <tr>
                    <td style="width: 50%">Monto:</td>
                    <td>$<?php echo number_format($pago->monto_pagado,2,'.',',') ?></td>
                </tr>
                <tr>
                    <td>Forma de pago:</td>
                    <td><?php echo $pago->metodo_pago->first()->catalogo_metodo_pago->valor ?></td>
                </tr>
                <?php if($pago->metodo_pago->first()->tipo_pago == 'cheque' or $pago->metodo_pago->first()->tipo_pago == 'ach'):?>
                <tr>
                    
                    <td><?php //si es cheque: Banco del Proveedor y Número de Cheque. Si es ACH ?>Banco del Proveedor:</td>
                    <td><?php echo count($pago->proveedor->banco) ? $pago->proveedor->banco->nombre : '' ?></td>
                </tr>
                <tr>
                    <td>Número de <?php 
                             switch ($pago->metodo_pago->first()->tipo_pago) {
                             case 'cheque': echo 'cheque'; break;
                             case 'ach': echo 'cuenta'; break;
                             }
                    ?>:</td>
                    <td><?php echo $pago->proveedor->numero_cuenta ?></td>
                </tr>
                <tr >
                    
                    <td style="padding-bottom: 20px;"></td>
                    <td></td>
                </tr>
                <?php endif;?>
                
                <tr>
                    <td><b>Total pagado:</b></td>
                    <td><b>$<?php echo number_format($pago->monto_pagado, 2, '.',',') ?></b></td>
                </tr>
                
            </table>
                    </td>
                </tr>
            </table>
           
        </div>
    </div>