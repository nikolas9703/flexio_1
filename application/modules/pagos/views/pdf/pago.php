<style type="text/css">

    table {border: none;}

    .titulo1{
        background: #2F75B5;
        color: white;
        border: 1px solid black;
        margin: 0px;
        padding: 5px;
    }

</style>

<?php ?>


<div id="container">

    <table style="width: 100%;" cellspacing="0" cellpadding="0">

        <!--seccion de cabecera-->
        <tr>
            <td rowspan="2" colspan="2">
                <img id="logo" src="<?php $logo = !empty($pago->empresa->logo) ? $pago->empresa->logo : 'default.jpg' ; echo $this->config->item('logo_path').$logo; ?>" height="56.69px" alt="Logo" border="0" />
            </td>
            <td></td>
            <td></td>
            <td colspan="2"><?php echo $pago->empresa->descripcion ?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td colspan="2"><?php echo $pago->empresa->telefono ?></td>
        </tr>

        <tr>
            <td colspan="6"><br><br></td>
        </tr>

        <tr>
            <td colspan="2"><b>Datos del pago</td>
            <td colspan="4"><br></td>
        </tr>

        <tr>
            <td colspan="6"><br><br></td>
        </tr>

        <tr>
            <td colspan="2"><b>Fecha de pago:</b> <?php echo $pago->fecha_pago ?></td>
            <td colspan="4"><br></td>
        </tr>

        <tr>
            <td colspan="2"><b>Proveedor:</b> <?php echo $pago->proveedor->nombre ?></td>
            <td colspan="4"><br></td>
        </tr>

        <tr>
            <td colspan="6"><br><br></td>
        </tr>

        <tr>
            <td class="titulo1">No. Factura</td>
            <td class="titulo1">Fecha de emisi&oacute;n</td>
            <td class="titulo1">Monto</td>
            <td class="titulo1">Pagado</td>
            <td class="titulo1">Saldo pendiente</td>
            <td class="titulo1">Pago</td>
        </tr>

        <?php foreach($pago->facturas as $factura):?>
        <tr>
            <td><?php echo $factura->codigo ?></td>
            <td><?php echo $factura->fecha_desde ?></td>
            <td>$<?php echo number_format($factura->total, 2) ?></td>
            <td>$<?php echo number_format($factura->pagos_aplicados_suma, 2) ?></td>
            <td>$<?php echo number_format($factura->saldo, 2) ?></td>
            <td>$<?php echo number_format($factura->pivot->monto_pagado, 2) ?></td>
        </tr>
        <?php endforeach;?>

        <tr>
            <td colspan="6"><br><br></td>
        </tr>

        <tr>
            <td>Monto:</td>
            <td>$<?php echo number_format($pago->monto_pagado,2) ?></td>
            <td colspan="4"><br></td>
        </tr>

        <tr>
            <td>M&eacute;todo de pago:</td>
            <td><?php echo $pago->metodo_pago->first()->catalogo_metodo_pago->valor ?></td>
            <td colspan="4"><br></td>
        </tr>

        <?php if($pago->metodo_pago->first()->tipo_pago == 'cheque'):?>
        <tr>
            <td>Banco del proveedor:</td>
            <td><?php echo count($pago->proveedor->banco) ? $pago->proveedor->banco->nombre : '' ?></td>
            <td colspan="4"><br></td>
        </tr>
        <tr>
            <td>N&uacute;mero de cuenta:</td>
            <td><?php echo $pago->proveedor->numero_cuenta ?></td>
            <td colspan="4"><br></td>
        </tr>
        <?php endif;?>

        <tr>
            <td colspan="6"><br></td>
        </tr>

        <tr>
            <td><b>Total pagado:</b></td>
            <td><b>$<?php echo number_format($pago->monto_pagado, 2) ?></b></td>
            <td colspan="4"><br></td>
        </tr>

     </table>

</div>
