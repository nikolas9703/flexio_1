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
									<h5>Buscar descuentos</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;">
									<!-- Inicia campos de Busqueda -->

							     	<?php
			                        $formAttr = array(
			                            'method'        => 'POST',
			                            'id'            => 'buscarDescuentosForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">No. descuento</label>
							            	<input type="text" id="numero" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">C&eacute;dula</label>
							            	<input type="text" id="cedula" class="form-control" value="" placeholder="">
										</div>
                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                              <label for="">Colaborador</label>
                              <input type="text" id="nombre_colaborador" class="form-control" value="" placeholder="">
                  </div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Tipo de descuento</label>

                                                                        <select id="tipo_descuento" class="form-control chosen-select">
                                                                            <option value="">Seleccione</option>
                                                                          <?php



                                                                        foreach($descuentos AS $info){

                                                                                echo '<option value="'. $info->id_cat .'">'. $info->etiqueta .'</option>';

                                                                        }



                                                                        ?>

                                                                        </select>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Acreedor</label>

							            	<select id="acreedor" class="form-control chosen-select">
                                                                            <option value="">Seleccione</option>
                                                                          <?php



                                                                        foreach($acreedores_list AS $info){

                                                                                echo '<option value="'. $info['id'] .'">'. $info['nombre'] .'</option>';

                                                                        }



                                                                        ?>

                                                                        </select>
										</div>
										<!--<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Fecha de Contrataci&oacute;n</label>
							            	<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<input type="text" id="fecha" class="form-control" value="" readonly="readonly" />
											</div>
										</div>-->




										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Estado</label>
                                                                        <?php

                                                                    //    print_r($estados);

                                                                        ?>
							            	<select class="form-control chosen-select" id="estado_id">
			                                	<option value="">Seleccione</option>
			                                	<?php
			                                	if(!empty($estados))
			                                	{
			                                		foreach ($estados AS $estado){
			                                			echo '<option value="'. $estado->id_cat .'">'. $estado->etiqueta .'</option>';
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
				    		<?php echo Jqgrid::cargar("descuentosgrid")  ?>
				    		<!-- /JQGRID -->
				    	</div>
						<div role="tabpanel" class="tab-pane" id="grid">
				            <?php // echo Grid::set()->html(); ?>
				    	</div>

				  	</div>
				</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarDescuentos','autocomplete'  => 'off');
echo form_open(base_url('descuentos/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>

<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

/*echo Modal::config(array(
	"id" => "formularioEvaluacionModal",
	"size" => "lg",
	"contenido" => modules::run("colaboradores/formulario_evaluacion", array()),
	"attr" => array(
		"ng-controller" => "formularioEvaluacionController",
		"flow-init" => "",
		"flow-file-added" => 'archivoSeleccionado($file, $event, $flow)'
	)
))->html();

echo Modal::config(array(
	"id" => "entregaInventarioModal",
	"size" => "lg",
	"contenido" => modules::run("colaboradores/formulario_entrega_inventario", array()),
	"attr" => array(
		"ng-controller" => "formularioEntregaInventarioController",
	)
))->html();*/
?>
