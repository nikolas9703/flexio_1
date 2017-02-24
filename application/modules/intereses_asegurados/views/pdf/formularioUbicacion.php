<style type="text/css">
    .titulo1{
        font-weight:bold;
        font-size: 18px;
        text-align: center;
    }
    .columnasnombres{
        width: 50%;
        text-align: left;
    }

</style>
<div id="container">
    <table style="width: 100%;margin-left: 40%;">
        <!--seccion de cabecera-->
        <tr>
            <td> <img id="logo" src="<?php $logo = !empty($datos->datosEmpresa->logo) ? $datos->datosEmpresa->logo : 'default.jpg';
            echo $this->config->item('logo_path') . $logo;
            ?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1">Nombre de la ubicación: <br><br><?php echo $datos->ubicacion->nombre ?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>

        <!--datos de la empresa-->
        <tr>
            <th class="columnasnombres">Dirección detallada:</th>
            <td><?php echo strtoupper($datos->ubicacion->direccion); ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Edificio y Mejoras: </th>
            <td><?php echo $datos->ubicacion->edif_mejoras; ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Contenido, mercancía, etc:</th>
            <td><?php echo $datos->ubicacion->contenido ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Maquinaria y equipos:</th>
            <td><?php echo $datos->ubicacion->maquinaria ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Inventario:</th>
            <td><?php echo $datos->ubicacion->inventario ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Acreedor:</th>
            <td><?php if($datos->ubicacion->acreedor !="") { if ($datos->ubicacion->acreedor !="otro") { echo $datos->ubicacion->datosAcreedor->nombre; } else { echo "Otro"; }}?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">% Otro:</th>
            <td><?php echo $datos->ubicacion->inventario ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">% Asignado al acreedor:</th>
           <td><?php echo $datos->ubicacion->porcentaje_acreedor ?></td>
        </tr>

        <tr>
            <th  class="columnasnombres">Observaciones:</th>
            <td><?php echo $datos->ubicacion->observaciones ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Estado:</th>
            <td><?php echo  $datos->estado; ?></td>
        </tr>
    </table>
</div>
