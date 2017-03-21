<style type="text/css">
    .titulo1{
        font-weight:bold;
        font-size: 18px;
        text-align: center;
    }
	.columnasnombres{
		   width: 25%;
		   text-align: left !important;
	}

</style>
<div id="container">
  <table style="width: 100%;margin-left: 40%;">
        <!--seccion de cabecera-->
        <tr>
            <td> <img id="logo" src="<?php $logo = !empty($datos->datosEmpresa->logo)?$datos->datosEmpresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1">Interés Asegurado: <?php echo $datos->numero?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>
        
        <!--datos de la empresa-->
        <tr>
            <th class='columnasnombres'>N°. Chasis o serie :</th>
			<td><?php echo strtoupper($datos->vehiculo->chasis);?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>N°. Unidad: </th>
             <td><?php echo $datos->vehiculo->unidad;?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Marca:</th>
            <td><?php echo $datos->vehiculo->marca?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Modelo:</th>
            <td><?php echo $datos->vehiculo->modelo?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Placa:</th>
            <td><?php echo $datos->vehiculo->placa?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Año:</th>
            <td><?php echo $datos->vehiculo->ano?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Motor:</th>
            <td><?php echo $datos->vehiculo->motor?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Color:</th>
            <td><?php echo $datos->vehiculo->color?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Capacidad:</th>
            <td><?php echo $datos->vehiculo->capacidad?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Uso:</th>
            <td><?php if($datos->vehiculo->uso !="")echo $datos->vehiculo->datosUso->etiqueta?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Condición:</th>
            <td><?php if($datos->vehiculo->condicion !="")echo $datos->vehiculo->datosCondicion->etiqueta?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Operador:</th>
            <td><?php echo $datos->vehiculo->operador?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Extras:</th>
            <td><?php echo $datos->vehiculo->extras?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Valor extras:</th>
            <td><?php echo $datos->vehiculo->valor_extras?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Acreedor:</th>
            <td><?php if($datos->vehiculo->acreedor !="") echo $datos->vehiculo->datosAcreedor->nombre?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>% Asignado al acreedor:</th>
            <td><?php echo $datos->vehiculo->porcentaje_acreedor?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Observaciones:</th>
            <td><?php echo $datos->vehiculo->observaciones?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Estado:</th>
            <td><?php echo $datos->vehiculo->estado?></td>
        </tr>
         
    </table>
</div>
