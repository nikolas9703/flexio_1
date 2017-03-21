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
                <div class="row" ng-controller="toastController">
                    <?php //$mensaje = self::$ci->session->flashdata('mensaje'); ?>
                    <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo!empty($mensaje) ? $mensaje["contenido"] : '' ?>
                    </div>
                </div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <?php
                            $formAttr = array(
                                'method' => 'POST',
                                'id' => 'buscarCotizacionesForm',
                                'autocomplete' => 'off'
                            );

                            echo form_open_multipart("", $formAttr);
                            ?>
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar Cotización</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. Cotizaci&oacute;n</label>
                                            <input type="text" class="form-control" id="no_cotizacion">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Nombre de Cliente</label>
                                            <select name="cliente" class="form-control chosen-select" id="cliente">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($clientes as $cliente) { ?>
                                                    <option value="<?php echo $cliente->id ?>"><?php echo $cliente->nombre ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Rango de fecha</label>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input type="text" name="desde" id="fecha1" class="form-control">
                                                        <span class="input-group-addon">a</span>
                                                        <input type="text" class="form-control" name="hasta" id="fecha2">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Estado</label>
                                            <select name="estados" class="form-control" id="etapa">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($etapas as $etapa) { ?>
                                                    <option value="<?php echo $etapa->etiqueta ?>"><?php echo $etapa->valor ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Vendedor</label>
                                            <select name="vendedor" class="form-control chosen-select" id="vendedor">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($vendedores as $vendedor) { ?>
                                                    <option value="<?php echo $vendedor->id ?>"><?php echo $vendedor->nombre . ' ' . $vendedor->apellido ?></option>
                                                <?php } ?>
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
                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                            <!-- /BUSCADOR -->

                            <!-- JQGRID -->
                            <?php echo modules::run('cotizaciones/ocultotabla'); ?>

                            <!-- /JQGRID -->
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarCotizaciones','autocomplete'  => 'off');
echo form_open(base_url('cotizaciones/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
<?php echo Modal::config(array("id" => "optionsModal", "size" => "sm"))->html(); ?> <!-- modal opciones -->
<?php echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html(); ?>  <!-- modal subir documentos -->
