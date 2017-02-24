<div ng-controller="IncapacidadesController" flow-init="" flow-file-added="archivoSeleccionado($file, $event, $flow)">
    <?php
    $info = !empty($info) ? array("info" => $info) : array();
    echo modules::run('incapacidades/formulario', $info);
    ?>
</div>

        	