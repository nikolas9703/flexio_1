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
            <td><?php if ($datos->proyecto_actividad->tipo_fianza !=""){ if($datos->proyecto_actividad->tipo_fianza =="propuesta") { echo "Propuesta"; } else if  ($datos->proyecto_actividad->tipo_fianza =="cumplimiento") { echo "Cumplimiento"; } else if  ($datos->proyecto_actividad->tipo_fianza =="anticipo") { echo "Anticipo"; } else if ($datos->proyecto_actividad->tipo_fianza =="otro") { echo "Otro"; } } ?></td>
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
           <td> <?php if ($datos->proyecto_actividad->tipo_propuesta !=""){ if($datos->proyecto_actividad->tipo_propuesta =="111") { echo "Licitación pública"; } else if  ($datos->proyecto_actividad->tipo_propuesta =="112") { echo "Solicitud de precios"; } else if  ($datos->proyecto_actividad->tipo_propuesta =="113") { echo "Concurso precios"; } else if  ($datos->proyecto_actividad->tipo_propuesta =="114") { echo "Acto público"; } else if ($datos->proyecto_actividad->tipo_propuesta =="115") { echo "Compra menor"; } else if ($datos->proyecto_actividad->tipo_propuesta =="otro") { echo "Otro"; } } ?></td>
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
            <td><?php echo ucwords($datos->proyecto_actividad->acreedor) ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Validez de la fianza:</th>
            <td><?php if ($datos->proyecto_actividad->validez_fianza_pr !=""){ if($datos->proyecto_actividad->validez_fianza_pr =="116") { echo "60 días"; } else if  ($datos->proyecto_actividad->validez_fianza_pr =="117") { echo "90 días"; } else if  ($datos->proyecto_actividad->validez_fianza_pr =="118") { echo "120 días"; } else if  ($datos->proyecto_actividad->validez_fianza_pr =="119") { echo "120 días"; } else if ($datos->proyecto_actividad->validez_fianza_pr =="otro") { echo "Otro"; } } ?></td>
        </tr>
        
        <tr>
            <th  class="columnasnombres">Observaciones:</th>
            <td><?php echo $datos->proyecto_actividad->observaciones ?></td>
        </tr>
        <tr>
            <th  class="columnasnombres">Estado:</th>
            <td><?php echo $datos->proyecto_actividad->estado ?></td>
        </tr>
    </table>
</div>
