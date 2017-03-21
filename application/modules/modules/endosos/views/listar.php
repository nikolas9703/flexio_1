<style type="text/css">
    body {
        padding-right: 0px !important;
    }
</style>
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
                                    <h5>Buscar endosos</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <?php
                                        $formAttr = array(
                                            'method' => 'POST',
                                            'id' => 'buscarEndososForm',
                                            'autocomplete' => 'off'
                                        );
                                    echo form_open(base_url(uri_string()), $formAttr);
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. de endoso</label>
                                            <input type="text" id="no_endoso" class="form-control" value="" placeholder="">
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Cliente</label>
                                            <select class="form-control chosen-select" id="cliente" > <!-- multiple="" data-placeholder="Seleccione" -->
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($clientes)) {
                                                    foreach ($clientes AS $cli) {
                                                        echo '<option value="' . $cli->id . '">' . $cli->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Aseguradora</label>
                                            <select class="form-control chosen-select" id="aseguradora" ><!-- multiple="" data-placeholder="Seleccione" --> 
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($aseguradoras)) {
                                                    foreach ($aseguradoras AS $ase) {
                                                        echo '<option value="' . $ase->id . '">' . $ase->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Ramo</label>
                                            <select class="form-control chosen-select" id="ramo" multiple="" data-placeholder="Seleccione">
                                                <?php
                                                $cont = 0;
                                                if (!empty($menu_crear)) {
                                                    foreach ($menu_crear as  $value) {
                                                        foreach ($menu_crear AS $menu) {
                                                            if ($value['id'] == $menu['padre_id']) {
                                                                $cont++;
                                                            }
                                                        }
                                                        if($cont == 0 && $value['padre_id'] != 0 && $value['estado'] && in_array($value['id'], $rolesArray) && in_array($value['id'], $usuariosArray) ){
                                                            echo '
                                                            <option value="'.$value['id'].'">'.$value['nombre'].'</option>
                                                            ';
                                                        }
                                                        $cont = 0;
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Tipo de Endoso</label>
                                            <select class="form-control chosen-select" id="tipo_endoso">
                                                <option value="">Seleccione</option>
                                                <option value="Activación">Activación</option>
                                                <option value="Cancelación">Cancelación</option>
                                                <option value="Regular">Regular</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Motivo de Endoso</label>
                                            <select class="form-control chosen-select" id="motivos_endosos">
                                                <option value="">Seleccione</option>
                                                <?php 
                                                    foreach ($motivo_endoso as $key => $value) {
                                                        echo '
                                                            <option value="'.$value['id'].'">'.$value['valor'].'</option>
                                                        ';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label>Rango de fechas</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
                                                <input type="input" id="fecha_desde" name="fecha_desde" readonly="readonly" class="form-control" value="">
                                                <span class="input-group-addon">a</span>
                                                <input type="input" id="fecha_hasta" name="fecha_hasta" readonly="readonly" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Estado</label>
                                            <select class="form-control chosen-select" id="estado">
                                                <option value="">Seleccione</option>
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="En Trámite">En Trámite</option>
                                                <option value="Aprobado">Aprobado</option>
                                                <option value="Rechazado">Rechazado</option>
                                                <option value="Cancelado">Cancelado</option>
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
                            <?php echo modules::run('endosos/ocultotabla'); ?>
                            <!-- /JQGRID -->
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();

echo Modal::config(array(
    "id" => "cambioEstado",
    "size" => "sm"
))->html();

echo Modal::config(array(
    "id" => "documentosModal",
    //"size" => "md",
    "titulo" => "Subir Documentos",
    "contenido" => modules::run("endosos/formularioModal")
))->html();

$formAttr = array('method' => 'POST', 'id' => 'exportarEndosos', 'autocomplete' => 'off');
    echo form_open(base_url('endosos/exportar'), $formAttr);
?>
    <input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>