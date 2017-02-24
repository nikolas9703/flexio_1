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
                                    <h5>Buscar comisiones</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->

                                    <?php
                                    $formAttr = array(
                                        'method' => 'POST',
                                        'id' => 'buscarComisionesForm',
                                        'autocomplete' => 'off'
                                    );
                                    echo form_open(base_url(uri_string()), $formAttr);
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. de comision</label>
                                            <input type="text" id="no_comision" class="form-control" value="" placeholder="">
                                        </div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. de recibo</label>
                                            <input type="text" id="no_cobro" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Aseguradora</label>
                                            <select class="form-control chosen-select" id="aseguradora">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (count($aseguradoras)>0) {
                                                    foreach ($aseguradoras AS $aseg) {
                                                        echo '<option value="' . $aseg->id . '">' . $aseg->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Rango de fechas</label>
                                            <div class="input-group">
												<input type="text" id="inicio_fecha" class="form-control" value="" placeholder="">
												<span class="input-group-addon">a</span>
												<input type="text" id="fin_fecha" class="form-control" value="" placeholder="">
											</div>
                                        </div>
                                    </div>
									<div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Estado</label>
                                            <select class="form-control chosen-select" id="estado">
                                                <option value="">Seleccione</option>
                                                <option value="liquidada">Liquidada</option>
                                                <option value="por_liquidar">Por liquidar</option>
												<option value="con_diferencia">Con diferencia</option>
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
<?php echo modules::run('comisiones_seguros/ocultotabla'); ?>
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

$formAttr = array('method' => 'POST', 'id' => 'exportarComisiones','autocomplete'  => 'off');
echo form_open(base_url('comisiones_seguros/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php
echo form_close();