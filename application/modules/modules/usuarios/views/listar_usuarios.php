<style>
 .sasas{
	color: #337ab7 !important;
 } 
</style>
<div id="wrapper">

<?php 
Template::cargar_vista('sidebar'); 

?>
<div id="page-wrapper" class="gray-bg row">
    
<?php Template::cargar_vista('navbar'); 

 
?>
<div class="row border-bottom"></div>
<?php Template::cargar_vista('breadcrumb'); //Breadcrumb 
 
?>

<div class="col-lg-12">
	<div class="wrapper-content">
    	
    	<div class="row">
                <div class="alert alert-success alert-dismissable message-box <?php echo !empty($message) ? 'show' : 'hide'  ?>">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <?php echo !empty($message) ? $message : ''  ?>
                </div>

				<div class="ibox float-e-margins border-bottom">
					<div class="ibox-title">
						<h5>Buscar Usuario</h5>
						<div class="ibox-tools">
							<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
						</div>
					</div>
					<div class="ibox-content" style="display:none;">  <!--  -->
						<!-- Inicia campos de Busqueda -->
							<div class="row">
								<div class="form-group col-md-3 col-sm-6 col-xs-12">
									<label for="">Nombre</label>
									<input type="text" id="nombre" class="form-control" placeholder="">
								</div>
								<div class="form-group col-md-3 col-sm-6 col-xs-12">
									<label for="">Apellido</label>
									<input type="text" id="apellido" class="form-control" placeholder="">
								</div>
								<div class="form-group col-md-3 col-sm-6 col-xs-12">
									<label for="disabledTextInput">Estados</label> 
									 
                                     <select id="estado" name="estado" class="form-control" >
                                                     <option value="" >Seleccione</option>
                                          <?php
                                            if(!empty($estados))
                                            {
                                                foreach ($estados AS $estado)
                                                {
                                                    echo '<option value="'. $estado .'">'. ucfirst($estado) .'</option>';
                                                }
                                            }
                                        ?>
                                      </select>	
								</div>
								<div class="form-group col-md-3 col-sm-6 col-xs-12">
									<label for="disabledTextInput">Rol</label> 
									<select id="id_rol"class="form-control">
										<option value="">Seleccione</option>
                                        <?php
                                        if(!empty($roles))
                                        {
                                            foreach ($roles AS $role) {
                                                echo '<option value="'. $role['id_rol'] .'">'. $role['nombre_rol'] .'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
								</div>
							</div>
							<div class="row">
								<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
									<input type="button" id="searchBtn" class="btn btn-w-m btn-default" value="Filtrar" />
									<input type="button" id="clearBtn" class="btn btn-w-m btn-default" value="Limpiar" />
								</div>
							
							</div>
							<!-- Termina campos de Busqueda -->
					</div>

				</div>
 				
				<?php
                $formAttr = array(
	                'method'       => 'post', 
	                'id'           => 'crearUsuarioForm',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
                ?>
               <input type="hidden" id="min_usuario" name="min_usuario" value="<?php echo $politicas['usuario']['long_minima_usuario']; ?>">
               <input type="hidden" id="max_usuario" name="max_usuario" value="<?php echo $politicas['usuario']['long_maxima_usuario']; ?>" >
              <input type="hidden" id="id_usuario"  name="id_usuario" value="0" >
				<div class="ibox float-e-margins border-bottom" >
					<div class="ibox-title">
						<h5 ><span id="titulo_form">Crear Usuario</span></h5>
						<div class="ibox-tools">
							<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
						</div>
					</div>
					<div class="ibox-content m-b-sm" style="display:none;"  id="div_crear_usuario">
							<div class="row">
								<div class="form-group col-md-12">
									<p class="required pull-right  text-danger">* Campos requeridos</p>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-md-4 col-sm-6 col-xs-12">
									<label for="c_email">Email</label> <span required="" aria-required="true">*</span>
									<input type="text" id="c_email" name="email" value="" class="form-control" placeholder="">
								</div>
								<div class="form-group col-md-4 col-sm-6 col-xs-12">
									<label for="c_confirmar_email">Confirmar Email</label> <span required="" aria-required="true">*</span>
									<input onpaste="return false;" type="text" id="c_confirmar_email" name="confirmar_email" value="" class="form-control" placeholder="">
								</div>
								<div class="form-group col-md-4 col-sm-6 col-xs-12">
									 <label for="c_usuario">Usuario</label> <span required="" aria-required="true">*</span>
									<div class="input-group m-b">
									<?php if($politicas['usuario']['uso_correo'] == 1){?>
									       <span class="input-group-addon">
									       <input type="checkbox" id="c_usar_correo" name="usar_correo" >  Usar Email </span> 
									
									<!-- <div class="checkbox checkbox-success">
 											<label for="c_usar_correo">Usar Email.</label>
									</div> -->
								
								<?php } ?>
										
										<input type="text" id="c_usuario" name="usuario" value="" class="form-control" placeholder="">
									</div>
								</div>
								 
							</div>
							
							<div class="row">
								<div class="form-group col-md-4 col-sm-6 col-xs-12">
									<label for="c_email">Categoría</label> <span required="" aria-required="true">*</span>
									<select id="id_categoria" name="id_categoria"   class="form-control" >
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($categorias))
		                                        {
		                                            foreach ($categorias AS $categoria) {
		 
		                                                echo '<option value="'. $categoria['uuid_categoria'] .'">'. $categoria['nombre'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
								</div>
								<div class="form-group col-md-4 col-sm-6 col-xs-12">
										<label for="disabledTextInput">Rol de Usuario</label>
									 
											<select id="c_id_roles" name="id_roles[]" value="" class="form-control" multiple="multitple" size="1" data-placeholder="Seleccione">
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($roles))
		                                        {
		                                            foreach ($roles AS $role) {
		 
		                                                echo '<option value="'. $role['id_rol'] .'">'. $role['nombre_rol'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
		                                    
		                                    
		                                    
		                                    <select id="c_id_roles_original" name="c_id_roles_original" style="visibility:hidden" value="" class="form-control" multiple="multitple" size="1" data-placeholder="Seleccione">
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($roles))
		                                        {
		                                            foreach ($roles AS $role) {
		 
		                                                echo '<option value="'. $role['id_rol'] .'">'. $role['nombre_rol'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
								</div>
								<div class="form-group col-md-4 col-sm-6 col-xs-12">
									<label for="c_email">Genera Comisión</label>
									<div class="input-group m-b"><span class="input-group-addon"> <input type="checkbox" name="ch_comision"  id="ch_comision"> </span> <input type="text" id="comision"  name="comision" data-inputmask="'mask': '9[9][9][.*{1,20}]', 'greedy' : false" class="form-control"><span class="input-group-addon">%</span></div>
								</div>
							</div>
							<div class="row">

								<div class="form-group col-md-4 col-sm-6 col-xs-12">
									<label for="c_email">Reporta a (Rol)</label>
									<select id="reporta_rol"   name="reporta_rol" class="form-control"  disabled="disabled"  >
										<option value="">Seleccione</option>
                                        <?php
                                        if(!empty($roles))
                                        {
                                            foreach ($roles AS $role) {
                                                echo '<option value="'. $role['id_rol'] .'">'. $role['nombre_rol'] .'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
								</div>

                                <div class="form-group col-md-4 col-sm-6 col-xs-12">
                                    <label for="c_email">Reporta a (Usuario)</label>
                                    <select id="reporta_usuario" name="reporta_usuario"  class="form-control" disabled="disabled"   >
                                        <option value="">Seleccione</option>
                                         <?php
                                       if(!empty($usuarios))
                                        {
                                            foreach ($usuarios AS $usuario) {
                                                echo '<option value="'. $usuario['id_usuario'] .'">'. $usuario['nombre'] .'</option>';
                                            }
                                        }  
                                        ?>
                                    </select>
                                </div>

 							</div>
							
							<div class="row">
								<div class="form-group col-md-4 col-sm-6 col-xs-6">
								  <div class="checkbox checkbox-default">  
 										   <input type="checkbox" id="c_enviar_correo" name="enviar_correo" class="form-control" onclick="return false" checked="checked">
										<label for="c_enviar_correo">Enviarle un correo al usuario con su informaci&oacute;n de acceso.</label>  
									</div>
									
								</div>
								<div class="form-group col-md-4 col-sm-6 col-xs-6">
								
								 
								
								
								 </div>
								
								
								
							</div>
							<div class="row">
								<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
									<input type="button" id="cancelarFormBtn" class="btn btn-w-m btn-default" value="Cancelar" />
									<input type="button" id="guardarFormBtn" class="btn btn-w-m btn-primary" value="Guardar" />
								</div>
 							</div>
 						</div>
					</div>
					<?php echo form_close(); ?>
			</div>
			
			<!-- Listado de Usuarios -->
			<div class="row">
				<div class="NoRecords text-center lead"></div>

				<!-- the grid table -->
				<table class="table table-striped" id="usuariosGrid"></table>

				<!-- pager definition -->
				<div id="pager"></div>
			</div>
			<!-- /Listado de Usuarios -->
			
		</div>
    </div>
</div>
	

	<div class="modal fade" id="optionsModal" tabindex="-1" role="dialog"
		aria-labelledby="optionsModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Opciones</h4>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>

