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
echo $this->config->item('logo_path') . $logo; ?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1">Nombre del proyecto o actividad: <br><br><?php echo $datos->proyecto_actividad->nombre_proyecto ?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>

        <!--datos de la empresa-->
        <tr>
            <th class="columnasnombres">Contratista :</th>
            <td><?php echo strtoupper($datos->proyecto_actividad->contratista); ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Representante legal: </th>
            <td><?php echo $datos->proyecto_actividad->representante_legal; ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Fecha de concurso:</th>
            <td><?php echo $datos->proyecto_actividad->fecha_concurso ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">No. de orden o contrato:</th>
            <td><?php echo $datos->proyecto_actividad->no_orden ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Duración del contrato:</th>
            <td><?php echo $datos->proyecto_actividad->duracion ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Fecha de inicio:</th>
            <td><?php echo $datos->proyecto_actividad->fecha ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Tipo de fianza:</th>
            <td><?php if($datos->proyecto_actividad->tipo_fianza !="") echo $datos->proyecto_actividad->tipodeFianza->etiqueta?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Monto del contratado:</th>
            <td><?php echo "$ ".$datos->proyecto_actividad->monto ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Monto afianzado %:</th>
            <td><?php echo $datos->proyecto_actividad->monto_afianzado ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Tipo de propuesta:</th>
            <td><?php if($datos->proyecto_actividad->tipo_propuesta !="") { if ($datos->proyecto_actividad->validez_fianza_pr !="otro") { echo $datos->proyecto_actividad->tipodePropuesta->etiqueta; } else { echo "Otro";}}?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Asignado al acreedor %:</th>
            <td><?php echo $datos->proyecto_actividad->asignado_acreedor ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Ubicación:</th>
            <td><?php echo $datos->proyecto_actividad->ubicacion ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Acreedor:</th>
            <td><?php if($datos->proyecto_actividad->acreedor !="") { if ($datos->proyecto_actividad->acreedor !="otro") { echo $datos->proyecto_actividad->datosAcreedor->nombre; } else { echo "Otro"; }}?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Validez de la fianza:</th>
            <td><?php if ($datos->proyecto_actividad->validez_fianza_pr !="") { if ($datos->proyecto_actividad->validez_fianza_pr !="otro") { echo $datos->proyecto_actividad->tipodeFianzapr->etiqueta; } else { echo "Otro"; }}?></td>
        </tr>
        
        <tr>
            <th  class="columnasnombres">Observaciones:</th>
            <td><?php echo $datos->proyecto_actividad->observaciones ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Estado:</th>
            <td><?php echo $datos->estado; ?></td>
        </tr>
    </table>
</div>
