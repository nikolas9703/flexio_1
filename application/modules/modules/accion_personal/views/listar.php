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
									<h5>Buscar acci&oacute;n personal</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;">
									<!-- Inicia campos de Busqueda -->
							     	
							     	<?php
			                        $formAttr = array(
			                            'method'        => 'POST', 
			                            'id'            => 'buscarAccionPersonalForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Colaborador</label>
							            	<input type="text" id="nombre_colaborador" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">No. Colaborador</label>
							            	<input type="text" id="no_colaborador" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">C&eacute;dula</label>
							            	<input type="text" id="cedula" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Centro Contable</label>
							            	<select class="form-control chosen-select" id="centro_id">
			                                	<option value="">Seleccione</option>
			                                	<?php 
			                                	if(!empty($lista_centros))
			                                	{
			                                		foreach ($lista_centros AS $centro){
			                                			echo '<option value="'. $centro["id"] .'">'. $centro["nombre"] .'</option>';
			                                		}
			                                	}
			                                	?>
					                        </select>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Cargo</label>
							            	<select class="form-control chosen-select" id="cargo_id">
			                                	<option value="">Seleccione</option>
			                                	<?php 
			                                	if(!empty($lista_cargos))
			                                	{
			                                		foreach ($lista_cargos AS $cargo){
			                                			echo '<option value="'. $cargo["id"] .'">'. $cargo["nombre"] .'</option>';
			                                		}
			                                	}
			                                	?>
					                        </select>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Acci&oacute;n personal</label>
							            	<select class="form-control chosen-select" id="tipo_accion">
			                                	<option value="">Seleccione</option>
			                                	<option value="evaluaciones">Evaluaciones</option>
												<option value="ausencias">Ausencias</option>
												<option value="vacaciones">Vacaciones</option>
												<option value="licencias">Licencias</option>
												<option value="incapacidades">Incapacidades</option>
												<option value="liquidaciones">Liquidaciones</option>
												<option value="permisos">Permisos</option>
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
				    		<?php echo modules::run('accion_personal/ocultotabla'); ?>
				    		<!-- /JQGRID -->
				    	</div>
				    	
				  	</div>
				</div>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
<?php
$formAttr = array('method' => 'POST', 'id' => 'pagarAccionPersonalForm','autocomplete'  => 'off');
echo form_open(base_url('planilla/crear'), $formAttr);
echo form_close();

echo Modal::config(array(
	"id" => "pagarAccionPersonalModal",
	"size" => "md"
))->html();
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html(); 
?>

