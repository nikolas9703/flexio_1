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
									<h5>Buscar Colaborador</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;">
									<!-- Inicia campos de Busqueda -->
							     	
							     	<?php
			                        $formAttr = array(
			                            'method'        => 'POST', 
			                            'id'            => 'buscarColaboradorForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Nombre</label>
							            	<input type="text" id="nombre" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">C&eacute;dula</label>
							            	<input type="text" id="cedula" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">No. Colaborador</label>
							            	<input type="text" id="codigo" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Cargo</label>
							            	<input type="text" id="cargo" class="form-control" value="" placeholder="">
										</div>
										<!--<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Fecha de Contrataci&oacute;n</label>
							            	<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<input type="text" id="fecha" class="form-control" value="" readonly="readonly" />
											</div>
										</div>-->
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">Fecha de Contrataci&oacute;n</label>
											<div class="input-group">
								    			<input type="input" id="fecha_contratacion_desde" readonly="readonly" class="form-control" value="" />
												<span class="input-group-addon">a</span>
												<input type="input" id="fecha_contratacion_hasta" readonly="readonly" class="form-control" value="" />
								    		</div>
										</div>
										
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">&Aacute;rea de Negocio</label>
							            	<select class="form-control chosen-select" id="departamento_id">
			                                	<option value="">Seleccione</option>
			                                	<?php 
			                                	if(!empty($lista_departamentos))
			                                	{
			                                		foreach ($lista_departamentos AS $departamento){
			                                			echo '<option value="'. $departamento["id"] .'">'. $departamento["nombre"] .'</option>';
			                                		}
			                                	}
			                                	?>
					                        </select>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Centro Contable</label>
							            	<input type="text" id="nombre_centro_contable" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Estado</label>
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
				    		<?php echo modules::run('colaboradores/ocultotabla'); ?>
				    		<!-- /JQGRID -->
				    	</div>
						<div role="tabpanel" class="tab-pane" id="grid">
				            <?php echo Grid::set()->html(); ?>
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

echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();

$formAttr = array('method' => 'POST', 'id' => 'crearConsumoForm','autocomplete'  => 'off');
echo form_open(base_url('consumos/crear'), $formAttr);
echo form_close();

$formAttr = array('method' => 'POST', 'id' => 'crearPlantillaForm','autocomplete'  => 'off');
echo form_open(base_url('plantillas/crear/carta-de-trabajo-sencilla/1'), $formAttr);
echo form_close();

$formAttr = array('method' => 'POST', 'id' => 'crearDescuentoForm','autocomplete'  => 'off');
echo form_open(base_url('descuentos/crear'), $formAttr); 
echo form_close();
?>
