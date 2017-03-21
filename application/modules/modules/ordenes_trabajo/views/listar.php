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
									<h5>Buscar &oacute;rdenes de trabajo</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;">

							     	<?php
			                        $formAttr = array(
			                            'method'        => 'POST',
			                            'id'            => 'buscarOrdenForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">No. Orden</label>
							            	<input type="text" id="no_orden" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Cliente</label>
							            	<input type="text" id="cliente" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">Rango de Fechas</label>
											<div class="input-group">
								    			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								    			<input type="text" id="fecha_desde" readonly="readonly" class="form-control" value="" />
												<span class="input-group-addon">a</span>
												<input type="text" id="fecha_hasta" readonly="readonly" class="form-control" value="" />
								    		</div>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Estado</label>
							            	<select class="form-control chosen-select" id="estado_id">
			                                	<option value="">Seleccione</option>
			                                	<?php
			                                	if(!empty($estados))
			                                	{
			                                		foreach ($estados AS $option){
			                                			echo '<option value="'. $option["id"] .'">'. $option["nombre"] .'</option>';
			                                		}
			                                	}
			                                	?>
					                        </select>
										</div>
									</div>
									<div class="row">
							        	<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
											<input type="button" id="searchOrdenBtn" class="btn btn-default btn-block" value="Filtrar" />
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
											<input type="button" id="clearOrdenBtn" class="btn btn-default btn-block" value="Limpiar" />
										</div>
									</div>
									<?php echo form_close(); ?>


								</div>
							</div>
							<!-- /BUSCADOR -->


				    		<!-- JQGRID -->
				    		<?php echo modules::run('ordenes_trabajo/ocultotabla'); ?>
				    		<!-- /JQGRID -->
				    	</div>

				  	</div>
				</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarOrdenesForm','autocomplete'  => 'off');
echo form_open(base_url('ordenes_trabajo/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php
echo form_close();

echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();

echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
?>
