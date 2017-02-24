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
            <th class='columnasnombres'>N°. de Serie :</th>
			<td><?php echo strtoupper($datos->casco_aereo->serie);?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Marca: </th>
             <td><?php echo $datos->casco_aereo->marca;?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Modelo:</th>
            <td><?php echo $datos->casco_aereo->modelo;?></td>
        </tr>
        <tr>
             <th class='columnasnombres'>Matricula:</th>
            <td><?php echo $datos->casco_aereo->matricula;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Valor:</th>
            <td><?php echo $datos->casco_aereo->valor;?></td>
        </tr>
        <tr>
           <th class='columnasnombres'>Pasajeros:</th>
            <td><?php echo $datos->casco_aereo->pasajeros;?></td>            
        </tr>
		<tr>
           <th class='columnasnombres'>Tripulacion:</th>
            <td><?php echo $datos->casco_aereo->tripulacion;?></td>            
        </tr>		     
		<tr>
           <th class='columnasnombres'>Observaciones:</th>
            <td><?php echo $datos->casco_aereo->observaciones;?></td>
        </tr>
		<tr>
           <th class='columnasnombres'>Estado:</th>
            <td><?php echo $datos->estado;?></td>
        </tr>         
    </table>
</div>
