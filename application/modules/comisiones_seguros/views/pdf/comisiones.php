<style type="text/css">
    .titulo1{
        font-weight:bold;
        font-size: 18px;
        text-align: center;
    }
	.columnasnombres{
		   width: 25%;
		   text-align:left;
	}
</style>
<div id="container">
	<table style="width: 100%;margin-left:20px">
		<tr>
            <td colspan=2> <img id="logo" src="<?php !empty($logo)?$logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
            <td colspan=3 class="titulo1">Comisión: <?php echo $no_comision?></td>
        </tr>                                  
         <tr style='height: 20px !important;'>
            <td colspan=5 class="titulo1"><br><br></td>
        </tr>
		<tr>
			<th class='columnasnombres'>N. Poliza: </th>
			<th class='columnasnombres'>Cliente: </th>
			<th class='columnasnombres'>Ramo: </th>
			<th class='columnasnombres'>N. Factura: </th>
			<th class='columnasnombres'>Aseguradora: </th>
		</tr>
		<tr>
			<td><?php echo $poliza ?></td>
			<td><?php echo $cliente ?></td>
			<td><?php echo $ramo ?></td>
			<td><?php echo $aseguradora ?></td>
			<td><?php echo $no_factura ?></td>
		</tr>
		 <tr style='height: 20px !important;'>
			<th class='columnasnombres' colspan=5> </th>
		</tr>
		<tr>
			<th class='columnasnombres'>N. Recibo: </th>
			<th class='columnasnombres'>Fecha: </th>
			<th class='columnasnombres'>Monto del recibo: </th>
			<th class='columnasnombres'>Impuesto: </th>
			<th class='columnasnombres'>Pago a prima: </th>
		</tr>
		<tr>
			<td><?php echo $no_comision ?></td>
			<td><?php echo $fecha ?></td>
			<td>$<?php echo $monto_recibo ?></td>
			<td>$<?php echo $impuesto ?></td>
			<td><?php echo $pago_sobre_prima ?> </td>
		</tr>
		 <tr style='height: 20px !important;'>
			<th class='columnasnombres' colspan=5> </th>
		</tr>
		<tr>
			<th class='columnasnombres'>% Comisión: </th>
			<th class='columnasnombres'>Monto de comisión: </th>
			<th class='columnasnombres'>% Sobre comisión: </th>
			<th class='columnasnombres'>Monto de sobre comisión: </th>
			<th class='columnasnombres'>Comisión pendiente: </th>
		</tr>
		<tr>
			<td><?php echo $comision ?> %</td>
			<td>$<?php echo $monto_comision ?></td>
			<td><?php echo $sobre_comision ?> %</td>
			<td>$<?php echo $monto_scomision ?></td>
			<td><?php echo $comision_pendiente ?></td>
		</tr>
		 <tr style='height: 20px !important;'>
			<th class='columnasnombres' colspan=5> </th>
		</tr>
		<tr>
			<th class='columnasnombres'>N. Remesa entrante: </th>
			<th class='columnasnombres'>Lugar de pago: </th>
			<th class='columnasnombres'>Estado: </th>
			<td colspan=2></td>
		</tr>
		<tr>
			<td><?php echo $no_remesa ?></td>
			<td><?php echo $lugar_pago ?></td>
			<td><?php echo $estado ?></td>
			<td colspan=2></td>
		</tr>

	</table>
</div>