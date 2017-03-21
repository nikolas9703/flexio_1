<style type="text/css">
    .titulo1{
        font-weight:bold;
        font-size: 18px;
        text-align: center;
    }
    .columnasnombres{
           width: 30%;
           text-align: left !important;
    }

</style>
<div id="container">
  <table style="width: 100%; margin-left: 40%;">
        <!--seccion de cabecera-->
        <tr>
            <td> <img id="logo" src="<?php $logo = !empty($honorario->datosEmpresa->logo)?$honorario->datosEmpresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1">Código del honorario <?php echo "<br>".$honorario->no_honorario?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>
        
        <!--datos de la empresa  titulo1 -->
        <tr colspan="5" class="text-center">
            <th>Datos generales</th>
        </tr>
        
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr>
            <th class='columnasnombres'>N° honorario :</th>
            <td><?php echo strtoupper($honorario->no_honorario);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Agente :</th>
            <td><?php echo strtoupper($datosagente->nombre);?></td>
        </tr>
		
		<tr>
            <th class='columnasnombres'>Identificación agente :</th>
            <td><?php echo strtoupper($datosagente->identificacion);?></td>
        </tr>
        
        <tr>
            <th class='columnasnombres'>Fecha inicial : </th>
            <td><?php echo strtoupper($fecha_inicial);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Fecha final : </th>
            <td><?php echo strtoupper($fecha_final);?></td>
        </tr>
		<tr>
            <th class='columnasnombres'>Estado : </th>
            <td><?php 
			if($honorario->estado=='por_pagar')
				echo strtoupper('Por pagar');
			else
				echo strtoupper('Pagada');
			?>
			</td>
        </tr>
         
    </table>
    <br>
    <table style="width: 100%;">
        <!--datos de la remesa  titulo1 -->
        <tr class="text-center">
            <th colspan="12">Datos del honorario</th>
        </tr>
        <tr>
			<th class='columnasnombres' width="11%">No. Comisión</th>
            <th class='columnasnombres' width="11%">Fecha comisión</th>
            <th class='columnasnombres' width="11%">No. Recibo</th>
            <th class='columnasnombres' width="11%">Cliente</th>
            <th class='columnasnombres' width="11%">Aseguradora</th>
            <th class='columnasnombres' width="11%">Ramo/Riesgo</th>
            <th class='columnasnombres' width="11%">No. Póliza</th>
            <th class='columnasnombres' width="11%">Prima neta</th>
            <th class='columnasnombres' width="11%">Pago</th>
			<th class='columnasnombres' width="11%">% Comisión</th>
			<th class='columnasnombres' width="11%">Comisión</th>
        </tr> 
        <?php
			$tabla= '';
            foreach ($datosHonorario as $value) {
				if($value['id']!=="")
				{
					$tabla.= '
					<tr>
						<td style="'.$value['estilos'].'">'.$value['no_comision'].'</td>
						<td style="'.$value['estilos'].'">'.$value['fecha_comision'].'</td>
						<td style="'.$value['estilos'].'">'.$value['no_recibo'].'</td>
						<td style="'.$value['estilos'].'">'.$value['cliente'].'</td>
						<td style="'.$value['estilos'].'">'.$value['aseguradora'].'</td>
						<td style="'.$value['estilos'].'">'.$value['ramo'].'</td>
						<td style="'.$value['estilos'].'">'.$value['poliza'].'</td>
						<td style="'.$value['estilos'].'">$'.number_format($value['prima_neta'],2).'</td>
						<td style="'.$value['estilos'].'">$'.number_format($value['pago'],2).'</td>
						<td style="'.$value['estilos'].'">'.$value['porcentaje_comision'].'%</td>
						<td style="'.$value['estilos'].'">$'.number_format($value['monto_comision'],2).'</td>
						
					</tr>
					';
				}
				else
				{
					$tabla.= '
					<tr>
						<td style="'.$value['estilos'].'"></td>
						<td style="'.$value['estilos'].'"></td>
						<td style="'.$value['estilos'].'"></td>
						<td style="'.$value['estilos'].'"></td>
						<td style="'.$value['estilos'].'"></td>
						<td style="'.$value['estilos'].'"></td>
						<td style="'.$value['estilos'].'"></td>
						<td style="'.$value['estilos'].'"></td>
						<td style="'.$value['estilos'].'"></td>
						<td style="'.$value['estilos'].'">Total</td>
						<td style="'.$value['estilos'].'">$'.number_format($value['total'],2).'</td>
						
					</tr>
					';
				}
                
            }
			
			echo $tabla;
        ?>       
    </table>
</div>