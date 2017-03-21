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
  <table style="width: 100%; margin-left: 10% !important;">
        <!--seccion de cabecera-->
        <tr>
            <td> <img id="logo" src="<?php $logo = !empty($datos->datosEmpresa->logo)?$datos->datosEmpresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1">Nombre de la Remesa <?php echo "<br>".$datos->no_remesa?></td>
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
            <th class='columnasnombres'>N° remesa :</th>
            <td><?php echo strtoupper($datos->no_remesa);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Aseguradora :</th>
            <td><?php echo strtoupper($aseguradora->nombre);?></td>
        </tr>

		<tr>
            <th class='columnasnombres'>No. Recibo :</th>
            <td><?php echo strtoupper($nombre_recibo);?></td>
        </tr>
		
		<tr>
            <th class='columnasnombres'>Monto pago :</th>
            <td><?php echo strtoupper($monto_recibo);?></td>
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
            <th class='columnasnombres'>Ramos : </th>
            <td></td>
        </tr>
        <?php 

            foreach ($nombreRamos as $key => $value) {
                echo '
                    <tr>
                        <th class="columnasnombres"></th>
                        <td>'.$value["nombre"].'</td>
                    </tr>
                ';
            }
        ?>
         
    </table>
    <br>
    <table style="width: 100%;">
        <!--datos de la remesa  titulo1 -->
        <tr class="text-center">
            <th colspan="12">Datos de la Remesa</th>
        </tr>
		<tr>
            <th class='columnasnombres' width="7%">N. Comisión</th>
            <th class='columnasnombres' width="7%">Fecha de operación</th>
			<th class='columnasnombres' width="7%">N. póliza</th>
			<th class='columnasnombres' width="10%">Ramo</th>
            <th class='columnasnombres' width="13%">Cliente</th>
            <th class='columnasnombres' width="6%">Pago a prima</th>
            <th class='columnasnombres' width="6%">% Comisión</th>
            <th class='columnasnombres' width="6%">Comisión esperada</th>
            <th class='columnasnombres' width="6%">Comisión descontada</th>
            <th class='columnasnombres' width="6%">% S.Comisión</th>
			<th class='columnasnombres' width="6%">S.Comisión esperada</th>
			<th class='columnasnombres' width="6%">S.Comisión descontada</th>
			<th class='columnasnombres' width='14%'>Comisión pagada</th>
        </tr> 
        <?php
			$tabla= '';
            foreach ($datosRemesa as $value) {
                $tabla.= '
                <tr>
                    <td style="'.$value['estilos'].'">'.$value['numero_factura'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['fecha_operacion'].'</td>
					<td style="'.$value['estilos'].'">'.$value['numero_poliza'].'</td>
					<td style="'.$value['estilos'].'">';
					
					if($value['id']=='' && $value['final']==0)
					{
						$ramo='Sub-Total: '.$value['nombre_ramo'];
					}
					else if($value['id']!='' && $value['final']==0)
					{
						$ramo=$value['nombre_ramo'];
					}
					else
						$ramo='';
					
					$tabla.=$ramo.'</td>
                    <td style="'.$value['estilos'].'">';
					if($value['final']==0)
						$cliente=$value['nombre_cliente'];
					else
						$cliente='TOTAL';
					
					$tabla.=$cliente.'</td>
                    <td style="'.$value['estilos'].'">';
					$prima='';
					if($value['final'] === 0 && $value['prima_neta']!='')
					{
						$prima=$value['prima_neta'];
					}
					if($value['final'] == 1)
					{
						$prima=$value['prima_neta_final'];
					}
					
					$tabla.=$prima.'</td>
                    <td style="'.$value['estilos'].'">';
					
					$tabla.=$value['porcentaje_comision'];
					
					if($value['id']!="")
						$tabla.='%';
					
					$tabla.='</td>
					<td style="'.$value['estilos'].'">';
					if($value['id']!='' && $value['final']==0)
						$con_esperada=$value['comision_esperada'];
					else if($value['id']=='' && $value['final']==0)
						$con_esperada=$value['total_com_esperada'];
					else
						$con_esperada=$value['com_esp_final'];
					
                    $tabla.=$con_esperada.'</td>
                    <td style="'.$value['estilos'].'">';
					
					if($value['id']!='' && $value['final']==0)
						$con_descontada=$value['comision_descontado'];
					else if($value['id']=='' && $value['final']==0)
						$con_descontada=$value['total_com_descontada'];
					else
						$con_descontada=$value['com_des_final'];
					
					$tabla.=$con_descontada.'</td>
					<td style="'.$value['estilos'].'">';
					
					$tabla.=$value['porcentaje_sobre_comision'];
					
					if($value['id']!="")
						$tabla.='%';
					
					$tabla.='</td>
					<td style="'.$value['estilos'].'">';
					
					if($value['id']!='' && $value['final']==0)
						$sobcomision_esperada=$value['sobcomision_esperada'];
					else if($value['id']=='' && $value['final']==0)
						$sobcomision_esperada=$value['total_sob_esperada'];
					else
						$sobcomision_esperada=$value['scom_esp_final'];
					
					$tabla.=$sobcomision_esperada.'</td>
					<td style="'.$value['estilos'].'">';
					
					if($value['id']!='' && $value['final']==0)
						$sobcomision_descontada=$value['sobcomision_descontada'];
					else if($value['id']=='' && $value['final']==0)
						$sobcomision_descontada=$value['total_sob_descontada'];
					else
						$sobcomision_descontada=$value['scom_des_final'];
					
					$tabla.=$sobcomision_descontada.'</td>
					<td style="'.$value['estilos'].'">';
					
					if($value['id']!='' && $value['final']==0)
						$comision_pagada_total=$value['comision_pagada'];
					else if($value['id']=='' && $value['final']==0)
						$comision_pagada_total=$value['comision_pagada_total'];
					else
						$comision_pagada_total=$value['com_paga_final'];
					
					$tabla.=$comision_pagada_total.'</td>
                </tr>
                ';
            }
			
			echo $tabla;
        ?>       
    </table>

</div>