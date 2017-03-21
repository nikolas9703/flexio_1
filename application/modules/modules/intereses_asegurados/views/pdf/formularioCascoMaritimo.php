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
            <td class="titulo1">Nombre embarcación: <br><br><?php echo $datos->casco_maritimo->nombre_embarcacion ?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>

        <!--datos de la empresa-->
        <tr>
            <th class="columnasnombres">N°. de serie del casco:</th>
            <td><?php echo strtoupper($datos->casco_maritimo->serie); ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Nombre de la embarcación: </th>
            <td><?php echo $datos->casco_maritimo->nombre_embarcacion; ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Tipo:</th>
            <td><?php echo $datos->casco_maritimo->tipo ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Marca:</th>
            <td><?php echo $datos->casco_maritimo->marca ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Valor:</th>
            <td><?php echo $datos->casco_maritimo->valor ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Pasajeros:</th>
            <td><?php echo $datos->casco_maritimo->pasajeros ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Acreedor:</th>
            <td><?php if($datos->casco_maritimo->acreedor !="") { if ($datos->casco_maritimo->acreedor !="otro") { echo $datos->casco_maritimo->datosAcreedor->nombre; } else { echo "Otro"; }}?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">% Asignado al acreedor:</th>
           <td><?php echo $datos->casco_maritimo->porcentaje_acreedor ?></td>
        </tr>

        <tr>
            <th  class="columnasnombres">Observaciones:</th>
            <td><?php echo $datos->casco_maritimo->observaciones ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Estado:</th>
            <td><?php echo  $datos->estado; ?></td>
        </tr>
    </table>
</div>
