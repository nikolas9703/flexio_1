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
									<h5>Reporte de Pagos</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:block;">
									<!-- Inicia campos de Busqueda -->
							     	
							     	<?php
			                        $formAttr = array(
			                            'method'        => 'POST', 
			                            'id'            => 'buscarReporteForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Nombre</label>
							            	<input type="text" id="nombre" disabled class="form-control" value="<?php echo $info[0]['nombre']; ?>" placeholder="">
									 </div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">R.U.C.</label>
							            	<input type="text" id="ruc" disabled class="form-control" value="<?php echo $info[0]['ruc']; ?>" placeholder="">
										</div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Tel&eacute;fono</label>
							            	<input type="text" id="telefono" disabled class="form-control" value="<?php echo $info[0]['telefono']; ?>" placeholder="">
										</div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Correo Electr&oacute;nico</label>
							            	<input type="text" id="email" disabled class="form-control" value="<?php echo $info[0]['email']; ?>" placeholder="">
										</div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Tipo de Acreedor</label>
							            	<input type="text" id="tipo_acreedor" disabled class="form-control" value="<?php echo $info[0]['tipo']['etiqueta']; ?>" placeholder="">
										</div>
									<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Descuentos a Colaboradores</label>
                                                                  		<input type="text" id="descuentos_colaboradores" disabled class="form-control" value="<?php echo count($info[0]['descuentos']); ?>" placeholder="">
										</div>
                                                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Desde</label>
							            	<div class="input-group">
						    			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                        <input type="text" name="fecha_desde" value="" class="form-control fecha" readonly="readonly" id="fecha_desde" required>
                                                                        <span class="input-group-addon">a</span>
                                                                        <input type="text" name="fecha_hasta" value="" class="form-control fecha" readonly="readonly" id="fecha_hasta" required>
                                                                        </div>
										</div>                
									</div>
									<div class="row">
							        	<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
										<div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2" style="float:right;">
											<input type="button" id="limpiar" class="btn btn-w-m btn-block " value="Limpiar" />
										</div>
										<div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2" style="float:right;">
											<input type="button" id="Submit" class="btn btn-w-m btn-primary btn-block " value="Actualizar" />
										</div>
                                                                        
									</div>
									<?php echo form_close(); ?>									
								<!-- Termina campos de Busqueda -->
                                                                                            
                                <table id="tabla2" class="table table-bordered" style="display:none;">
                                    
                                <thead>
                                <tr><th>Colaborador</th>
                                    <th>Fecha</th>
                                    <th>Categor&iacute;a</th>
                                    <th>Monto total adeudado</th>
                                    <th>Monto por ciclo</th>
                                    <th>Saldo</th>
                                </tr>
                                </thead>
                                <tbody id="registros">
                                    
                                </tbody>
                                </table>
                                                                
								</div>
							</div>
					
                                                        
                                                        <!-- /BUSCADOR -->
                                                        
                                                        
		
				    	</div>
						
				    	
				  	</div>
				</div>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarColaboradores','autocomplete'  => 'off');
echo form_open(base_url('colaboradores/exportar'), $formAttr);
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

