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
                <div id="mensaje_info"></div>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>

				<div role="tabpanel">
				 	<!-- Tab panes -->
				  	<div class="row tab-content">
				    	<div role="tabpanel" class="tab-pane active" id="tabla">

							<!-- BUSCADOR -->
							<div class="ibox border-bottom">
								<div class="ibox-title">
									<h5>Buscar transacci&oacute;n</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;" id="buscarHistorialForm">
									<!-- Inicia campos de Busqueda -->
							     	<div class="row">

                                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
							            	<label for="">Centro contable</label>
                                            <div class="row">
							            	<select type="text" nombre="centro_contable" id="centro_contable" multiple="multiple" class="form-control select2" style="with:auto">
                                                <?php foreach ($centros_contable as $key => $centro) { ?>
                                                    <option value="<?php echo $centro->id ?>"><?php echo $centro->nombre ?></option>
                                                <?php }?>
                                            </select>
                                        </div>
										</div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                            <label for="fecha">Rango de fecha</label><br>
                                            <div class="input-daterange input-group" id="fecha">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" class="input-sm form-control" id="start">
                                                <span class="input-group-addon">a</span>
                                                <input type="text" class="input-sm form-control" id="end">
                                            </div>
                                        </div>



							        	<div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
							            	<label for="">Transacción</label>
							            	<input type="text" id="nombre" class="form-control" value="" placeholder="">
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
							<!-- /BUSCADOR -->

				    		<!-- JQGRID -->
				    		<?php echo modules::run('contabilidad/ocultotablahistorial'); ?>
				    		<!-- /JQGRID -->
				    	</div>

				  	</div>
				</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<!-- exportar-->
<?php
$formAttr1 = array('method' => 'POST', 'id' => 'exportar_historial_cuenta','autocomplete'  => 'off');
echo form_open(base_url('contabilidad/exportar_historial_transacciones'), $formAttr1);
?>
<input type="hidden" name="ids" id="ids" />
<input type="hidden" name="cuenta_ids" id="cuenta_ids" />
<input type="hidden" name="exportar_centro_contable" id="exportar_centro_contable" />
<input type="hidden" name="exportar_fecha_min" id="exportar_fecha_min" />
<input type="hidden" name="exportar_fecha_max" id="exportar_fecha_max" />
<input type="hidden" name="exportar_transaccion" id="exportar_transaccion" />
<?php echo form_close(); ?>
<!-- expotar historial-->
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

echo Modal::config(array(
	"id" => "crearPlanModal",
	"size" => "sm"
))->html();
$formAttr = array(
  'method'       => 'POST',
  'id'           => 'form_crear_cuenta',
  'autocomplete' => 'off'
);
?>
