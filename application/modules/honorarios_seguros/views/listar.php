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
                                    <h5>Buscar honorarios</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->

                                    <?php
                                    $formAttr = array(
                                        'method' => 'POST',
                                        'id' => 'buscarHonorariosForm',
                                        'autocomplete' => 'off'
                                    );
                                    echo form_open(base_url(uri_string()), $formAttr);
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. de honorario</label>
                                            <input type="text" id="no_honorario" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Agente</label>
                                            <select class="form-control chosen-select" id="agente">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (count($agentes)>0) {
                                                    foreach ($agentes AS $agen) {
                                                        echo '<option value="' . $agen->id . '">' . $agen->nombre . '</option>';
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
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Estado</label>
                                            <select class="form-control chosen-select" id="estado">
                                                <option value="">Seleccione</option>
                                                <option value="en_proceso">En proceso</option>
												<option value="por_pagar">Por pagar</option>
                                                <option value="pagada">Pagada</option>
                                            </select>
                                        </div>
                                    </div>
									<div class="row">
									<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Usuario</label>
                                            <select class="form-control chosen-select" id="usuario">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($usuarios)) {
                                                    foreach ($usuarios AS $user) {
                                                        echo '<option value="' . $user->id . '">' . $user->nombre . " " . $user->apellido . '</option>';
                                                    }
                                                }
                                                ?>
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
<?php echo modules::run('honorarios_seguros/ocultotabla'); ?>
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

$formAttr = array('method' => 'POST', 'id' => 'exportarHonorarios','autocomplete'  => 'off');
echo form_open(base_url('honorarios_seguros/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php
echo form_close();