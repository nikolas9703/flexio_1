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
                
                <div class="alert alert-success alert-dismissable message-box <?php echo !empty($message) ? 'show' : 'hide'  ?>">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?php echo !empty($message) ? $message : ''  ?>
                </div>

                <!-- BUSCADOR -->
                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5>Buscar Rol</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content" style="display:none;">
                        <!-- Inicia campos de Busqueda -->
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="">Nombre</label>
                                <input type="text" id="nombre" class="form-control" value="" placeholder="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Descripci&oacute;n</label>
                                <input type="text" id="descripcion" class="form-control" value="" placeholder="">
                            </div>
                             <div class="form-group col-sm-4">
                                <label for="">Estado</label>
                                 <select id="estado" class="form-control">
                                	<option value="">Seleccione</option>
                                	<option value="1">Activo</option>
                                	<option value="0">Inactivo</option>

                            	</select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-0 col-sm-6 col-md-6">&nbsp;</div>
                            
                             <div class="form-group col-xs-12 col-sm-6 col-md-6"  style="text-align: right;">
                             	<input type="button" id="searchBtn" class="btn btn-w-m btn-default" value="Filtrar" />
                                <input type="button" id="clearBtn" class="btn btn-w-m btn-default" value="Limpiar" />
                            </div>
                        </div>
                        <!-- Termina campos de Busqueda -->
                    </div>
                </div>
                <!-- /BUSCADOR -->

                <!-- CREAR ROL -->
				<?php
				$formAttr = array(
					'method'       => 'POST', 
					'id'           => 'crearRolForm',
					'name'         => 'crearRolForm',
					'autocomplete' => 'off'
				);
				echo form_open(base_url(uri_string()), $formAttr);
				?>
                <div id="crearRolBox" class="ibox border-bottom" ng-cloak="" ng-controller="crearRolFormCtrl">
                
                    <div class="ibox-title">
                        <h5>Crear Rol</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content" style="display:none;">
                    
                        <!-- Inicia campos de Guardar -->
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label for="">Nombre <span required="" aria-required="true">*</span></label>
                                <input type="text" id="nombre" name="nombre" class="form-control" value="" data-rule-required="true" ng-model="rol.nombre" />
                            </div>
                            <div class="form-group col-sm-5">
                                <label for="">Descripci&oacute;n <span required="" aria-required="true">*</span></label>
                                <input type="text" id="descripcion" name="descripcion" class="form-control" value="" data-rule-required="true" ng-model="rol.descripcion" />
                            </div>
                            <div class="form-group col-sm-2">
                                <label>Super Usuario</label>
                            	<div>
								  <label>
								    <input type="checkbox" id="superusuario" name="superusuario" class="js-switch" value="" checklist-model="rol.superusuario" ng-checked="rol.superusuario=='1'"/> 
								  </label>
								</div>
								<small>Autorizar este rol con acceso a todos los modulos.</small>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for="">Deafult</label>
                            	<!--<div class="checkbox"> -->
                            	<div>
                                	<input type="checkbox" id="defaultRol" name="defaultRol" value="" checklist-model="rol.defaultRol" ng-click="check($event)" ng-checked="rol.defaultRol=='1'" />
                                	<!-- ng-checked="rol.defaultRol=='1'" -->
                                   <!-- <label for="defaultRol">&nbsp;</label>-->
                                </div>
								<small>Establecer este rol por defecto para los nuevos usuarios.</small>
                            </div>
                        </div>
                          <div class="row">
                            <div class="col-xs-0 col-sm-6 col-md-6">&nbsp;</div>
                            
                             <div class="form-group col-xs-12 col-sm-6 col-md-6"  style="text-align: right;">
                             	 <input type="button" id="cancelarFormBtn" class="btn btn-w-m btn-default" value="Cancelar" ng-click="cancelar($event)" />
                                <a href="#" id="guardarFormBtn" class="btn btn-w-m btn-primary" ng-model="cargo.guardarcargoBtn" ng-click="guardar($event)">Guardar</a>
                            </div>
                        </div>
                        <input type="hidden" id="rol_id" name="rol_id" value="" ng-model="rol.rol_id" />
                        <!-- Termina campos de Busqueda -->
                        
                    </div>
                </div>
                
                <?php echo form_close(); ?>
                <!-- /CREAR ROL -->

            </div>
            
            <!-- JQGRID -->
    		<?php echo modules::run('roles/ocultotabla'); ?>
    		<!-- /JQGRID -->
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" id="optionsModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Opciones</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
