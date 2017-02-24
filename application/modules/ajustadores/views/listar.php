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
                    <div class="alert alert-dismissable alert-danger" id="mensaje">
                    </div>
                </div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar Ajustadores</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->

                                    <?php
                                    $formAttr = array(
                                        'method' => 'POST',
                                        'id' => 'buscarAjustadoresForm',
                                        'autocomplete' => 'off'
                                    );
                                    echo form_open(base_url(uri_string()), $formAttr);
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Nombre ajustador</label>
                                            <input type="text" id="nombre" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">RUC</label>
                                            <input type="text" id="ruc" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                            <label>Teléfono</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                <input type="input-left-addon" id="telefono"  class="form-control" data-inputmask="'mask': '', 'greedy':true">
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                            <label>Correo electrónico </label>
                                            <div class="input-group">
                                                <span class="input-group-addon">@</span>
                                                <input type="input-left-addon" name="email" class="form-control" id="email" >
                                            </div>
                                            <label  for="campo[correo]" generated="true" class="error"></label>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
                                            <label>Estado</label>
                                            <select name="estado" id="estado" class="form-control">
                                                <option value=''></option>
                                                <option value='Por aprobar'>Por Aprobar</option>
                                                <option value='Activo'>Activo</option>
                                                <option value='Inactivo'>Inactivo</option>
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
                            <?php echo modules::run('ajustadores/ocultotabla'); ?>
                            <!-- /JQGRID -->
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarAjustadores', 'autocomplete' => 'off');
echo form_open(base_url('ajustadores/exportar'), $formAttr);

//
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php
echo form_close();

$formAttr = array('method' => 'POST', 'id' => 'crearContactoForm', 'autocomplete' => 'off');
echo form_open(base_url('ajustadores/ver'), $formAttr);

echo form_close();
?>

<?php
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();

echo Modal::config(array(
    "id" => "opcionesModalEstado",
    "size" => "sm"
))->html();
?>
