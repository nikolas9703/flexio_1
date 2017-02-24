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
            <th class='columnasnombres'>N°. de Liquidación :</th>
			<td><?php echo strtoupper($datos->carga->no_liquidacion);?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Fecha de espacho: </th>
             <td><?php echo $datos->carga->fecha_despacho;?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Fecha de Arribo:</th>
            <td><?php echo $datos->carga->fecha_arribo;?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Detalle Mercancía:</th>
            <td><?php echo $datos->carga->detalle;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Valor Mercancia:</th>
            <td><?php echo $datos->carga->valor;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Tipo de Empaque:</th>
            <td><?php if($datos->carga->tipo_empaque !="")echo $datos->carga->datosTipoEmpaque->etiqueta?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Condición de Envío:</th>
            <td><?php if($datos->carga->condicion_envio !="")echo $datos->carga->datosCondicionEnvio->etiqueta?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Medio de Transporte:</th>
            <td><?php if($datos->carga->medio_transporte !="")echo $datos->carga->datosMedioTransporte->etiqueta?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Origen:</th>
            <td><?php echo $datos->carga->origen;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Destino:</th>
            <td><?php echo $datos->carga->destino;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Acreedor:</th>
            <td><?php if($datos->carga->acreedor !="") echo $datos->carga->datosAcreedor->nombre?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Tipo de Obligación:</th>
            <td><?php if($datos->carga->tipo_obligacion !="")echo $datos->carga->datosTipoObligacion->etiqueta?></td>
        </tr>     
		<tr>
           <th class='columnasnombres'>Observaciones:</th>
            <td><?php echo $datos->carga->observaciones;?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Estado:</th>
            <td><?php echo $datos->estado;?></td>
        </tr>
         
    </table>
</div>
