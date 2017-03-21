<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content">
                <div class="row">
                    <div id="mensaje">
                    </div>
                </div>
                <div ng-controller="toastController"></div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar Solicitudes</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->

                                    <?php
                                    $formAttr = array(
                                        'method' => 'POST',
                                        'id' => 'buscarSolicitudesForm',
                                        'autocomplete' => 'off'
                                    );
                                    echo form_open(base_url(uri_string()), $formAttr);
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. de solicitud</label>
                                            <input type="text" id="no_solicitud" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Cliente</label>
                                            <select class="form-control chosen-select" id="cliente">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($clientes)) {
                                                    foreach ($clientes AS $cli) {
                                                        echo '<option value="' . $cli->nombre . '">' . $cli->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>

                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Aseguradora</label>
                                            <select class="form-control chosen-select" id="aseguradora">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($aseguradoras)) {
                                                    foreach ($aseguradoras AS $aseg) {
                                                        echo '<option value="' . $aseg->id . '">' . $aseg->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Ramo</label>
                                            <select class="ramo" multiple="multiple">
                                                <?php
                                                $cont = 0;
                                                foreach ($menu_crear AS $menu) {
                                                    foreach ($menu_crear AS $value) {
                                                        if ($menu['id'] == $value['padre_id']) {
                                                            $cont++;
                                                        }
                                                    }
                                                    if ($cont == 0 && $menu['padre_id'] != 0 && in_array($menu['id'], $rolesArray) && in_array($menu['id'], $usuariosArray)) {
                                                        echo '<option value="' . $menu['nombre'] . '">' . $menu['nombre'] . '</option>';
                                                    }
                                                    $cont = 0;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Tipo de solicitud</label>
                                            <select class="form-control chosen-select" id="tipo_solicitud">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($tipo)) {
                                                    foreach ($tipo AS $tip) {
                                                        echo '<option value="' . $tip->id . '">' . $tip->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Fecha de creaci&oacute;n</label>
                                            <div class="input-group">
                                                <input type="text" id="inicio_creacion" class="form-control" value="" placeholder="">
                                                <span class="input-group-addon">a</span>
                                                <input type="text" id="fin_creacion" class="form-control" value="" placeholder="">
                                            </div>
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Usuario</label>
                                            <select class="form-control chosen-select" id="usuario">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($usuarios)) {
                                                    foreach ($usuarios AS $user) {
                                                        echo '<option value="' . $user->usuario_id . '">' . $user->nombre . " " . $user->apellido . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>										
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Estado</label>
                                            <select class="form-control chosen-select" id="estado_id">
                                                <option value="">Seleccione</option>
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="En trámite">En trámite</option>
                                                <option value="Aprobada">Aprobada</option>
                                                <option value="Rechazada">Rechazada</option>
                                                <option value="Anulada">Anulada</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
                                        </div>
                                    </div>
                                    <?php echo form_close(); ?>

                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                            <!-- /BUSCADOR -->


                            <!-- JQGRID -->
                            <?php echo modules::run('solicitudes/ocultotabla'); ?>
                            <!-- /JQGRID -->
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<div id="menu_crear" style="display:none;">
    <?php
	
    foreach ($menu_crear AS $row) {
        $id_solicitudes = $row['id'];
        $cont = 0;
        foreach ($menu_crear AS $value) {
            if ($row['padre_id'] == 0 && $row['id'] == $value['padre_id']) {
                $cont++;
            }
        }
        ?>
        <?php if ($row['padre_id'] == 0 && $row['estado'] == 1 && $row['level'] == 1 && $cont >= 1) { ?>
            <a href="#collapse0000<?php echo $row['id'] ?>" class="btn btn-block btn-outline btn-success" style="margin-bottom:5px;" data-toggle="collapse" data-id="<?php echo $row['id'] ?>"><?php echo $row['nombre'] ?></a>
            <div id="collapse0000<?php echo $row['id'] ?>" class="collapse">
                <ul id="<?php echo $row['nombre'] ?>" class="list-group clear-list">      
                    <?php
                    foreach ($menu_crear AS $info) {
                        $cont2 = 0;
                        foreach ($menu_crear AS $valor) {
                            if ($info['id'] == $valor['padre_id']) {
                                $cont2++;
                            }
                        }

                        if ($info['padre_id'] == $id_solicitudes && $info['estado'] == 1 && $cont2 > 0) {
                            ?>
                            <li class="m-sm">
                                <a href="#collapse0000<?php echo $info['id'] ?>" data-toggle="collapse" data-id="<?php echo $info['id'] ?>"><?php echo $info['nombre'] ?><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
                            </li>
                            <div id="collapse0000<?php echo $info['id'] ?>" class="collapse">
                                <ul id="<?php echo $info['nombre'] ?>" class="list-group clear-list">
                                    <?php
                                    foreach ($menu_crear AS $result) {
                                        $cont3 = 0;
                                        foreach ($menu_crear AS $valor2) {
                                            if ($result['id'] == $valor2['padre_id']) {
                                                $cont3++;
                                            }
                                        }

                                        //Generacion padres hijos
                                        if ($result['padre_id'] == $info['id'] && $result['estado'] == 1 && $result['level'] == 3 && !empty($result['codigo_ramo']) && in_array($result['id'], $rolesArray) && in_array($result['id'], $usuariosArray) && $cont3 == 0) {
                                            ?>    
                                            <li class="m-md" style="margin-left:3em !important;">   
                                                <a id="botonAccionar" href="#" data-formulario="<?php echo $result['id'] ?>"><?php echo $result['nombre'] ?></a>
                                            </li>
                                        <?php } elseif ($result['padre_id'] == $info['id'] && $result['estado'] == 1 && $result['level'] == 3 && in_array($result['id'], $rolesArray) && in_array($result['id'], $usuariosArray)) { ?>
                                            <li class="m-sm" style="margin-left:3em !important;">
                                                <a href="#collapse0000<?php echo $result['id'] ?>" data-toggle="collapse" data-id="<?php echo $result['id'] ?>"><?php echo $result['nombre'] ?><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
                                            </li>
                                            <div id="collapse0000<?php echo $result['id'] ?>" class="collapse">
                                                <ul id="<?php echo $info['nombre'] ?>" class="list-group clear-list" style="margin-left:3em !important;">
                                                    <?php
                                                    foreach ($menu_crear AS $result2) {
                                                        if ($result2['padre_id'] == $result['id'] && $result2['estado'] == 1 && $result2['level'] == 4 && !empty($result2['codigo_ramo']) && in_array($result2['id'], $rolesArray) && in_array($result2['id'], $usuariosArray)) {
                                                            ?>
                                                            <li class="m-md"  style="margin-left:2.5em !important;">     
                                                                <a id="botonAccionar" href="#" data-formulario="<?php echo $result2['id'] ?>"><?php echo $result2['nombre'] ?></a>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php 
						} elseif ($info['padre_id'] == $id_solicitudes && $info['estado'] == 1 && $cont2 == 0 && in_array($info['id'], $rolesArray) && in_array($info['id'], $usuariosArray)) { 	
						?>
                            <li class="m-sm">  
                                <a id="botonAccionar" href="#" data-formulario="<?php echo $info['id'] ?>"><?php echo $info['nombre'] ?></a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
    }
    ?>
</div>




<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarSolicitud', 'autocomplete' => 'off');
echo form_open(base_url('solicitudes/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>

<?php
$formAttr = array('method' => 'POST', 'id' => 'SolicitudesForm', 'autocomplete' => 'off');
echo form_open(base_url('solicitudes/crear'), $formAttr);
echo '<input type="text" id="solicitudes_id" name="solicitud_id" />';
echo form_close();
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
echo Modal::config(array(
    "id" => "moduloOpciones",
    "size" => "sm"
))->html();
echo Modal::config(array(
    "id" => "opcionesModalAnular",
    "size" => "md"
))->html();
echo Modal::config(array(
    "id" => "opcionesModalAprobar",
    "size" => "md"
))->html();
echo Modal::config(array(
    "id" => "opcionesModalRechazar",
    "size" => "md"
))->html();
echo Modal::config(array(
    "id" => "documentosModal",
    //"size" => "md",
    "titulo" => "Subir Documentos",
    "contenido" => modules::run("solicitudes/formularioModal")
))->html();
?>
