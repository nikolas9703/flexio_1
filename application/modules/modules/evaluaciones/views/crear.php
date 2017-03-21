<div ng-controller="EvaluacionesController" flow-init="" flow-file-added="archivoSeleccionado($file, $event, $flow)">
<?php 
$info = !empty($info) ? array("info" => $info) : array();
echo modules::run('evaluaciones/formulario', $info);
?>
</div>
        	