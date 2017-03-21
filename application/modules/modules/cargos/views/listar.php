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
				        <div ng-controller="toastController"></div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar cargos</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <?php
      			                        $formAttr = array(
      			                            'method'        => 'POST',
      			                            'id'            => 'buscarCargosForm',
      			                            'autocomplete'  => 'off'
      			                          );
      			                         echo form_open(base_url(uri_string()), $formAttr);
      			                        ?>
                                    <div class="row ">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="nombre">No. Cargo</label>
                                            <input type="text" id="numero" class="form-control" value="">
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label>Item</label>
                                            <input type="text" value="" id="item" class="form-control" data-inputmask="'mask':'9{0,13}[.99]','greedy':false" />
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    											<label for="">Rango de fechas</label>
                    											<div class="input-group">
                    								    			<input type="text" id="fecha_desde" readonly="readonly" class="form-control" value="" />
                    												<span class="input-group-addon">a</span>
                    												<input type="text" id="fecha_hasta" readonly="readonly" class="form-control" value="" />
                    								    		</div>
                    										</div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label>Contrato</label>
                                            <input type="text" value="" id="contrato" class="form-control" />
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="centro">Periodo tarifario</label>
                                            <select id="periodo" class="form-control">
                                                <option value="">Seleccione</option>
                                                <option value="4_horas">4 horas</option>
                                                <option value="diario">Diario</option>
                                                <option value="semanal">Semanal</option>
                                                <option value="mensual">Mensual</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="responsable_id">Estado</label>
                                            <select id="estado" class="form-control">
                                                <option value="">Seleccione</option>
                                                <option value="por_facturar">Por facturar</option>
                                                <option value="facturado">Facturado</option>
                                                <option value="anulado">Anulado</option>
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
                                    <?php echo form_close(); ?>

                                </div>
                            </div>
                            <!-- /BUSCADOR -->

                            <!-- JQGRID -->
                            <?php echo modules::run('cargos/ocultotabla'); ?>
                            <!-- /JQGRID -->
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'cambiarEstadoEnGrupo','autocomplete'  => 'off');
echo form_open(base_url('cargos/cambiar_estado_grupal'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<input type="hidden" name="estado" id="estadoGrupal" value="" />
<?php echo form_close(); ?>
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

echo Modal::config(array(
	"id" => "opcionesModalGrupal",
	"size" => "sm"
))->html();
?>
