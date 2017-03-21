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
  <table style="width: 100%;margin-left: 40%;">
        <!--seccion de cabecera-->
        <tr>
            <td> <img id="logo" src="<?php $logo = !empty($datos->datosEmpresa->logo)?$datos->datosEmpresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1">Nombre del artículo <?php echo $datos->articulo->nombre?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>
        
        <!--datos de la empresa-->
        <tr>
            <th class='columnasnombres'>Clase de equipo :</th>
			<td><?php echo strtoupper($datos->articulo->clase_equipo);?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Marca :</th>
             <td><?php echo $datos->articulo->marca; ?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Modelo :</th>
            <td><?php echo $datos->articulo->modelo; ?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Año :</th>
            <td><?php echo $datos->articulo->anio; ?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>N° de serie :</th>
            <td><?php echo $datos->articulo->numero_serie; ?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Condición :</th>
            <td><?php echo $datos->articulo->datosCondicion->etiqueta; ?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Valor % :</th>
            <td><?php echo $datos->articulo->valor; ?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Observaciones :</th>
            <td><?php echo $datos->articulo->observaciones; ?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Estado :</th>
            <td><?php echo $datos->estado; ?></td>
        </tr>
         
    </table>
</div>
