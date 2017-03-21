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
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
                    </div>
                </div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div class="panel-heading white-bg">
                            <span class="panel-title"></span>
                            <ul class="nav nav-tabs nav-tabs-xs formTabs">
                                <li class="dropdown pull-right tabdrop hide">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-align-justify"></i>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu"></ul>
                                </li>
                                <li class="active">
                                    <a data-toggle="tab" href="#catalogos">Cat&aacute;logos</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#terminos_condiciones">T&eacute;rminos &amp; Condiciones</a>
                                </li>
                            </ul>
                        </div>
                        <div role="tabpanel" class="tab-pane active" id="catalogos">
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Crear tipo clientes</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="categoria">Tipo<span style="color:red;"> *</span></label>
                                            <input type="text" id="tipo" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-9 col-lg-9">
                                            <label for="descripcion">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                            <input type="text" id="descripcion" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarTipoBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarTipoBtn" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoTipo" value="crear" data-uuid="">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('configuracion_ventas/ocultotablaTipo'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane active" id="catalogos">
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Crear categor&iacute;a clientes</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="categoria">Nombre<span style="color:red;"> *</span></label>
                                            <input type="text" id="nombre" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-9 col-lg-9">
                                            <label for="descripcion">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                            <input type="text" id="descripcionCat" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarCategoriaBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarCategoriaBtn" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoCategoria" value="crear" data-uuid="">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('configuracion_ventas/ocultotablaCategoria'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane active" id="terminos_condiciones">
                            <?php echo modules::run('configuracion_compras/terminos_condiciones'); ?>
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarTablasConfig','autocomplete'  => 'off');
echo form_open(base_url('configuracion_compras/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<input type="hidden" name="tabla" id="tabla" value="" />
<?php echo form_close(); ?>
<?php

echo    Modal::config(array(
    "id"    => "optionsModal",
    "size"  => "sm"
))->html();

?>
