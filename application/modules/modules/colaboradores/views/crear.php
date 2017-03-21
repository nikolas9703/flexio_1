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

	            <div class="row" role="tabpanel">
					<?php 
					$info = !empty($info) ? array("info" => $info) : array();
					
					if(empty($info)):
						echo modules::run('colaboradores/ocultoformulario', $info);
					else:
					?>
					<div class="tab-content"> 
						<div role="tabpanel" class="tab-pane fade in active" id="colaboradorTab">
							<?php echo modules::run('colaboradores/ocultoformulario', $info); ?>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="vacacionTab">
							<?php echo modules::run('vacaciones/formularioparcial', $info); ?>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="ausenciaTab">
							<?php echo modules::run('ausencias/formularioparcial', $info); ?>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="incapacidadTab">
							<?php echo modules::run('incapacidades/formularioparcial', $info); ?>
						</div>
						<div id="licenciaTab" role="tabpanel" class="tab-pane fade">
							<?php echo modules::run('licencias/formularioparcial', $info); ?>
						</div>
						<div id="permisoTab" role="tabpanel" class="tab-pane fade">
							<?php echo modules::run('permisos/formularioparcial', $info); ?>
						</div>
						<div id="liquidacionTab" role="tabpanel" class="tab-pane fade">
							<?php echo modules::run('liquidaciones/formularioparcial', $info); ?>
						</div>
						<div id="evaluacionTab" role="tabpanel" class="tab-pane fade">
							<?php echo modules::run('evaluaciones/formularioparcial', $info); ?>
						</div>
						<div id="descuentoTab" role="tabpanel" class="tab-pane fade">
							<?php echo modules::run('descuentos/formulario_descuento', $info); ?>
						</div>
						<div id="plantillaTab" role="tabpanel" class="tab-pane fade">
							<?php echo modules::run('colaboradores/ocultoformulario', $info); ?>
						</div>
					</div>
					<?php endif;  ?>
                </div>
               
				<?php  if(!empty($info) && $crear_colaborador=="ver"): ?>
                <div class="row" id="sub-panel" >
					<div style="height:50px !important" class="panel-heading white-bg">	
			    		<ul class="nav nav-tabs nav-tabs-xs">
							<li class="active"><a role="tab" data-toggle="tab" href="#accionPersonalTabla">Acciones de personal</a></li>
							<li><a role="tab" data-toggle="tab" href="#descuentosTabla">Descuentos</a></li>
							<li><a role="tab" data-toggle="tab" href="#plantillasTabla">Plantillas</a></li>
                                                        <li><a role="tab" data-toggle="tab" href="#contratoTabla">Contratos</a></li>    
                                        </ul>
					</div>
					<div class="tab-content white-bg p-xs">
						<div id="accionPersonalTabla" class="tab-pane active" role="tabpanel">
						
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
					        	<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
					            	<label for="">Acci&oacute;n personal</label>
					            	<select class="form-control chosen-select" id="tipo_accion">
	                                	<option value="">Seleccione</option>
	                                	<option value="Evaluaciones">Evaluaciones</option>
	                                	<option value="Licencias">Licencias</option>
	                                	<option value="Liquidaciones">Liquidaciones</option>
	                                	<option value="Permisos">Permisos</option>
	                                	<option value="Ausencias">Ausencias</option>
	                                	<option value="Vacaciones">Vacaciones</option>
	                                	<option value="Incapacidades">Incapacidades</option>
			                        </select>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
					            	<label for="">Rango de Fecha</label>
					            	<div class="input-group">
						    			<input type="text" id="fecha_ap_desde" readonly="readonly" class="form-control" value="" />
										<span class="input-group-addon">a</span>
										<input type="text" id="fecha_ap_hasta" readonly="readonly" class="form-control" value="" />
						    		</div>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
					            	<label for="">Estado</label>
					            	<select class="form-control chosen-select" id="estado">
	                                	<option value="">Seleccione</option>
	                                	<option value="aprobado">Aprobado</option>
	                                	<option value="rechazado">Rechazado</option>
			                        </select>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
								</div>
							</div>
							<?php echo form_close(); ?>
							<!-- Termina campos de Busqueda -->

							<?php echo modules::run('accion_personal/ocultotabla', ""); ?>
						</div>
						<div id="descuentosTabla" class="tab-pane" role="tabpanel">
						
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
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
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
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
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
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
					            	<label for="">Rango de Fecha</label>
					            	<div class="input-group">
						    			<input type="text" id="fecha_desc_desde" readonly="readonly" class="form-control" value="" />
										<span class="input-group-addon">a</span>
										<input type="text" id="fecha_desc_hasta" readonly="readonly" class="form-control" value="" />
						    		</div>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
								</div>
							</div>
							<?php echo form_close(); ?>
							<!-- Termina campos de Busqueda -->
						
							<?php echo modules::run('descuentos/ocultotabla', ""); ?>
						</div>
                                            <!-- Sub panel para Plantillas -->
                                            <div id="plantillasTabla" class="tab-pane" role="tabpanel">
						
							<!-- Inicia campos de Busqueda -->						     	
							<?php
							$formAttr = array(
								'method'        => 'POST', 
								'id'            => 'buscarPlantillasForm',
								'autocomplete'  => 'off'
							);
							echo form_open(base_url(uri_string()), $formAttr);
							?>
							<div class="row">
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">Tipo de plantilla</label>
									<select id="plantilla_id" class="form-control chosen-select">
										<option value="">Seleccione</option>
										<?php
                                                                                $i=1;                                  
                                                                                foreach($plantillas AS $grupo => $plantillass){
                                                                                foreach($plantillass AS $plantilla){
                                                                                echo '<option value="' . $grupo."-".$plantilla['nombre']. '">' .$grupo." - ".$plantilla['nombre']. '</option>';
                                                                                $i++;
                                                                                }
                                                                                $i++;
                                                                                }
                                                                                ?> 
									</select>
								</div>
								
						 		<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Fecha de creaci&oacute;n</label>
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
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
								</div>
							</div>
							<?php echo form_close(); ?> 
							<!-- Termina campos de Busqueda -->
							<?php echo modules::run('plantillas/ocultotabla', ""); ?>
						</div>
                                    <div id="contratoTabla" class="tab-pane" role="tabpanel">
						
							<!-- Inicia campos de Busqueda -->
					     	<?php
	                        $formAttr = array(
	                            'method'        => 'POST', 
	                            'id'            => 'buscarRecontratacionForm',
	                            'autocomplete'  => 'off'
	                          );
	                         echo form_open(base_url(uri_string()), $formAttr);
	                        ?>
					     	<div class="row">
					        	<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
					            	<label for="">N° de contrato</label>
					            	<input type="text" id="no_contrato" class="form-control" value="" />

								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
					            	<label for="">Rango de Fecha</label>
					            	<div class="input-group"> 
						    			<input type="text" id="fecha_contratacion_desde" readonly="readonly" class="form-control" value="" />
										<span class="input-group-addon">a</span>
										<input type="text" id="fecha_contratacion_hasta" readonly="readonly" class="form-control" value="" />
						    		</div>
								</div>
								
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
								</div>
							</div>
							<?php echo form_close(); ?>
							<!-- Termina campos de Busqueda -->

							<?php echo modules::run('colaboradores/ocultorecontrataciontabla', ""); ?>
						</div>        
					</div>
				</div>
                    <?php endif;  ?>
                    <?php if($recontratacion == "recontratacion"): ?>
                    <div class="row" id="sub-panel" >
					<div style="height:50px !important" class="panel-heading white-bg">	
			    		<ul class="nav nav-tabs nav-tabs-xs">
							<li class="active"><a role="tab" data-toggle="tab" href="#accionPersonalTabla">Contratos</a></li>
						</ul>
					</div>
					<div class="tab-content white-bg p-xs">
						<div id="accionPersonalTabla" class="tab-pane active" role="tabpanel">
						
							<!-- Inicia campos de Busqueda -->
					     	<?php
	                        $formAttr = array(
	                            'method'        => 'POST', 
	                            'id'            => 'buscarRecontratacionForm',
	                            'autocomplete'  => 'off'
	                          );
	                         echo form_open(base_url(uri_string()), $formAttr);
	                        ?>
					     	<div class="row">
					        	<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
					            	<label for="">N° de contrato</label>
					            	<input type="text" id="no_contrato" class="form-control" value="" />

								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
					            	<label for="">Rango de Fecha</label>
					            	<div class="input-group"> 
						    			<input type="text" id="fecha_contratacion_desde" readonly="readonly" class="form-control" value="" />
										<span class="input-group-addon">a</span>
										<input type="text" id="fecha_contratacion_hasta" readonly="readonly" class="form-control" value="" />
						    		</div>
								</div>
								
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
								</div>
							</div>
							<?php echo form_close(); ?>
							<!-- Termina campos de Busqueda -->

							<?php echo modules::run('colaboradores/ocultorecontrataciontabla', ""); ?>
						</div>
						
					</div>
				</div>

				<?php endif;  ?>
				<br/><br/>
				<?php echo modules::run('colaboradores/ocultoformulariocomentarios'); ?>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 
//Redirigir Formulario Crear
$formAttr = array('method' => 'POST', 'id' => 'crearPlantillaForm', 'autocomplete' => 'off');
echo form_open(base_url('plantillas/crear/carta-de-trabajo-secilla/1'), $formAttr);
echo form_close();

echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
?>