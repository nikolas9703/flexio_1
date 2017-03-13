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
			<th class='columnasnombres' width="11%">No. Factura</th>
            <th class='columnasnombres' width="11%">No. Póliza</th>
            <th class='columnasnombres' width="11%">Ramo</th>
            <th class='columnasnombres' width="11%">Inicio vigencia</th>
            <th class='columnasnombres' width="11%">Fin vigencia</th>
            <th class='columnasnombres' width="11%">Cliente</th>
            <th class='columnasnombres' width="11%">Fecha factura</th>
            <th class='columnasnombres' width="11%">Monto pagado a la factura</th>
            <th class='columnasnombres' width="11%">Estado</th>
        </tr> 
        <?php
			$tabla= '';
            foreach ($datosRemesa as $value) {
                $tabla.= '
                <tr>
                    <td style="'.$value['estilos'].'">'.$value['numero_factura'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['numero_poliza'].'</td>
					<td style="'.$value['estilos'].'">'.$value['nombre_ramo'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['inicio_vigencia'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['fin_vigencia'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['nombre_cliente'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['fecha_factura'].'</td>
					<td style="'.$value['estilos'].'">';
					if($value['estado']=='cobrado_completo')
					{
						$monto=$value['monto'];
					}
					else if($value['estado']=='por_cobrar' || $value['estado']=='cobrado_parcial')
					{
						if($value['mont_pag_factura']=='no')
						{
							$monto=0;
						}
						else
						{
							$monto=$value['monto'];
						}
					}
					else
					{
						$monto=$value['monto_total_final'];
					}
					if($monto!='')
						$tabla.='$';
                    $tabla.=$monto.'</td>
                    <td style="'.$value['estilos'].'">';
					if($value['estado']=='por_cobrar')
						$estado='Por cobrar';
					else if($value['estado']=='cobrado_parcial')
						$estado='Cobrado parcial';
					else if($value['estado']=='cobrado_completo')
						$estado='Cobrado completo';
					else
						$estado='';
					$tabla.=$estado.'</td>
                </tr>
                ';
            }
			
			echo $tabla;
        ?>       
    </table>

</div>