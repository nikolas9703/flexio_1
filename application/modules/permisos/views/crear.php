<div ng-controller="PermisosController" flow-init="" flow-file-added="archivoSeleccionado($file, $event, $flow)">
    <?php
    $info = !empty($info) ? array("info" => $info) : array();
    echo modules::run('permisos/formulario', $info);
    ?>
</div>