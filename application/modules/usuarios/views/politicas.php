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
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <?php echo !empty($message) ? $message : ''     ?>
                </div>
                
                <div id="crear_rol_box" class="ibox">
                
                    <div class="ibox-content" style="display: block;">
                    
                    <div class="tab-content">  
                    	<div id="tab-0" class="tab-pane active" "="">
                     		 <div class="ibox float-e-margins border-bottom"">
								<div class="ibox-title">
									<h5>Políticas de Usuario</h5>
									<div class="ibox-tools">
										<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
									</div>
								</div>
								
									 <?php
									 
									 
									  
								 
			                $formAttr = array(
				                'method'       => 'post', 
				                'id'           => 'politicasUsuario',
				                'autocomplete' => 'off',
								'class'			=> 'formulario'
			                );
			                echo form_open(base_url(uri_string()), $formAttr);
			                
                ?>
								
								
								<div class="ibox-content m-b-sm" style="display : none;" >
	 								<div class="form-group row">
		                                <div class="col-xs-6 col-sm-4"> <b>Configuración de Usuario</b>  <br> <br>
	 	                                </div>
		                                <div class="col-xs-6 col-sm-4">  </div>
	  	                                <div class="col-xs-6 col-sm-4">  </div>
	 	                            </div>
									<div class="form-group row ">
		                                <div class="col-xs-6 col-sm-4">
		                                    <label>Longitud Mínima de Usuario</label>
		                                    <input maxlength="2"  style="width: 30%;" type="text" name="long_minima_usuario" value="<?php echo $info['usuario']['long_minima_usuario']; ?>" class="form-control" id="long_minima_usuario"   />
		                                </div>
		                                 <div class="col-xs-6 col-sm-4">
	 	                                	<label style="width: 300px;">Permitir usar Correo Electónico para Usuario:</label>
		                                  <input name="uso_correo" type="checkbox" class="js-switch" <?php echo ($info['usuario']['uso_correo']=='1')?'checked="checked"':''; ?>  />
		                                </div>
		                                <div class="col-xs-6 col-sm-4">&nbsp;</div>
	  	                               
	 	                            </div>
		                            <div class="form-group row ">
		                                <div class="col-xs-6 col-sm-4">
		                                    <label>Longitud Máxima de Usuario</label>
		                                    <input   maxlength="2"  style="width: 30%;" type="text" name="long_maxima_usuario" value="<?php echo $info['usuario']['long_maxima_usuario']; ?>" class="form-control" id="long_maxima_usuario"   />
		                                </div>
		                                <div class="col-xs-6 col-sm-4">&nbsp;</div>
	  	                                <div class="col-xs-6 col-sm-4">&nbsp;</div>
	 	                            </div>
		                            <div class="form-group row ">
		                                <div class="col-xs-6 col-sm-4">
		                                    &nbsp;
		                                </div>
		                                 <div class="col-xs-6 col-sm-4">
	 	                                	<b>Más Opciones</b>
		                                </div>
		                                <div class="col-xs-6 col-sm-4">&nbsp;</div>
	  	                               
	 	                            </div>
		                            <div class="form-group row ">
		                                <div class="col-xs-6 col-sm-4">
		                                    &nbsp;
		                                </div>
		                                 <div class="col-xs-6 col-sm-4">
	 	                                	 <label  style="width: 300px;">Permitir Usuarios Editar Perfil</label>
	 	                                	   <input name="editar_perfil" id='editar_perfil'  type="checkbox" class="js-switch" <?php echo ($info['usuario']['editar_perfil']=='1')?'checked="checked"':''; ?> />
	 	                                	 
		                                    
		                                </div>
		                                <div class="col-xs-6 col-sm-4">&nbsp;</div>
	  	                               
	 	                            </div>
	 	                              <div class="form-group row ">
	                                <div class="col-xs-6 col-sm-4">
	                                                                  
	                                </div>
	                                 <div class="col-xs-6 col-sm-4">
	                                  
 	                                </div>
	                                <div class="col-xs-6 col-sm-4">
	                                 <a href="<?php echo base_url('configuracion/grupo'); ?>" id="cancelarFormBtn" class="btn btn-w-m btn-default">Cancelar</a>
	                                     <input type="button" id="usuarioFormBtn" class="btn btn-w-m btn-primary botones" value="Guardar">	  
 	                                </div>
 	                            </div>
 	                            
 	                              
								</div>
							 
								  </div>
						
						
						
						 <?php echo form_close(); ?>
						 
						  <?php
			               $formAttr = array(
				                'method'       => 'post', 
				                'id'           => 'politicasContrasena',
				                'autocomplete' => 'off',
								'class'			=> 'formulario'	
			                );
			                echo form_open(base_url(uri_string()), $formAttr); 
			                
                ?>
                <input type="hidden" name="cantidad_minima" id="cantidad_minima" value="<?php echo $info['contrasena']['minima_cantidad_letras']+$info['contrasena']['minima_cantidad_numeros']+$info['contrasena']['minima_cantidad_caracteres'];?>" >
						<div class="ibox float-e-margins border-bottom"">
								<div class="ibox-title">
									<h5>Políticas de Contraseña</h5>
									<div class="ibox-tools">
										<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
									</div>
								</div>
							 <div class="ibox-content m-b-sm" style="display: none;">
								
									<div class="row">
		                                <div class="col-xs-6 col-sm-4"> <b>Configuración de Contraseña</b>  <br> <br>
	 	                                </div>
		                                <div class="col-xs-6 col-sm-4"><b>Expiración de Contraseña</b>  <br> <br>  </div>
	  	                                <div class="col-xs-6 col-sm-4">  </div>
	 	                            </div>
	 	                            <div class="row">
		                                <div class="col-xs-6 col-sm-4"><label>Longitud Mínima de Contraseña</label> 
		                                  <input maxlength="2"  type="text" name="long_minima_contrasena" value="<?php echo $info['contrasena']['long_minima_contrasena']; ?>"   class="form-control" id="long_minima_contrasena"   style="width: 30%;"/>
		                                </div>
		                                <div class="col-xs-6 col-sm-4"><label>Contraseña Expira Después de Día(s) <br>
		                                
													<small>(Colocar el valor a 0 para desahbilitar expiración)</small>
		                                    </div>
	  	                                <div class="col-xs-6 col-sm-4"> <input maxlength="4" type="text" name="expira_despues_dias"  value="<?php echo $info['contrasena']['expira_despues_dias']; ?>" class="form-control" id="expira_despues_dias" style="width: 30%;" />  
	  	                                
	  	                                <label>Dia(s)</label></div>
	 	                            </div><br><br>
	 	                            
									<div class="form-group row ">
		                                <div class="col-xs-6 col-sm-4">
 
 
 
 
 
		                                    <label><b>Especificar Configuraciones Avanzadas:</b> </label>
		                                    <input type="checkbox" class="js-switch"  <?php echo ($info['contrasena']['configuracion_avanzada']=='1')?'checked="checked"':''; ?> name='configuracion_avanzada' id='configuracion_avanzada' >
		                                </div>
		                                 <div class="inline col-xs-6 col-sm-4">
	 	                                	<label style="width: 275px;">Notificar Usuarios Antes de Expiración</label>
	 	                                	<input name="notificacion_usuarios_expiracion" id='notificacion_usuarios_expiracion' type="checkbox" class="js-switch"  <?php echo ($info['contrasena']["notificacion_usuarios_expiracion"]=='1')?'checked="checked"':''; ?>   />
	 	                                	<br />
	 	                                	<small>(Mensaje emergente al ingrear al sistema)</small>
		                                    
		                                    
		                                </div>
		                                <div class="col-xs-6 col-sm-4"><input maxlength="2"  type="text" name="contr_notificar_antes_dias"  value="<?php echo $info['contrasena']['contr_notificar_antes_dias']; ?>"  class="form-control" id="contr_notificar_antes_dias" <?php echo ($info['contrasena']['notificacion_usuarios_expiracion']=='1')?'':'disabled="disabled"'; ?>   style="width: 30%;" /><label>Dia(s)</label>  </div>
	  	                               
	 	                            </div>
	 	                            <div class="form-group row ">
		                                <div class="col-xs-6 col-sm-4">
		                                    
		                                </div>
		                                 <div class="col-xs-6 col-sm-4">
	 	                                	 <b>Más Opciones</b>
		                                </div>
		                                <div class="col-xs-6 col-sm-4">&nbsp;</div>
	  	                               
	 	                            </div>
	 	                            
		                            <div class="form-group row ">
		                                <div class="col-xs-6 col-sm-4">
		                                    <label>Mínimo Cantidad de Letras</label>
		                                    <input maxlength="2"  type="text" name="minima_cantidad_letras"  value="<?php echo $info['contrasena']['minima_cantidad_letras']; ?>"  class="form-control" id="minima_cantidad_letras" <?php echo ($info['contrasena']['configuracion_avanzada']=='1')?'':'disabled="disabled"'; ?>   style="width: 30%;" />
		                                </div>
		                                <div class="col-xs-6 col-sm-4"><label  style="width: 275px;">Restringir el Uso de Contraseñas Viejas </label>
		                                <input  type="checkbox" name='restringir_contrasena_vieja' class="js-switch" <?php echo ($info['contrasena']['restringir_contrasena_vieja']=='1')?'checked="checked"':''; ?> >
		                                <br />
		                                <small>(Se guardarán las últimas 10 contraseñas)</small>
		                                
		                                    </div>
	  	                                <div class="col-xs-6 col-sm-4">&nbsp;</div>
	 	                            </div>
		                            <div class="form-group row ">
		                                <div class="col-xs-6 col-sm-4">
		                                   Mínimo Cantidad de Números
		                                   <input maxlength="2"  type="text" name="minima_cantidad_numeros"  value="<?php echo $info['contrasena']['minima_cantidad_numeros']; ?>"   class="form-control" id="minima_cantidad_numeros" <?php echo ($info['contrasena']['configuracion_avanzada']=='1')?'':'disabled="disabled"'; ?>   style="width: 30%;" />
		                                </div>
		                                 <div class="col-xs-6 col-sm-4">
	 	                                	<!--  <label  style="width: 275px;">Cambiar Contraseña en Login Inicial</label>
		                                    <input name="cambiar_contrasena_login"  id="cambiar_contrasena_login"  type="checkbox" class="js-switch" <?php echo ($info['contrasena']['cambiar_contrasena_login']=='1')?'checked="checked"':''; ?> />
		                                    -->
		                                </div>
		                                <div class="col-xs-6 col-sm-4">&nbsp;</div>
		                            </div>
		                            <div class="form-group row ">
		                                <div class="col-xs-6 col-sm-4">
		                                    Mínimo Cantidad de Caractéres Especiales
		                                    <input maxlength="2"  type="text" name="minima_cantidad_caracteres"   value="<?php echo $info['contrasena']['minima_cantidad_caracteres']; ?>"    class="form-control" id="minima_cantidad_caracteres"  <?php echo ($info['contrasena']['configuracion_avanzada']=='1')?'':'disabled="disabled"'; ?>  style="width: 30%;" /> 
		                                    <span style="color:red">Permitidos:  # $ % & ; :</span>
		                                    
		                                </div>
		                                 <div class="col-xs-6 col-sm-4">
	 	                                	<!--<label  style="width: 275px;">Permitir Usuarios Cambiar Contraseña</label>
		                                     <input name="cambiar_contrasena"  id="cambiar_contrasena" type="checkbox" class="js-switch"  <?php echo ($info['contrasena']['cambiar_contrasena']=='1')?'checked="checked"':''; ?> />
		                                     -->
		                                </div>
		                                <div class="col-xs-6 col-sm-4">&nbsp;</div>
	  	                               
	 	                            </div>
	 	                            <div class="form-group row ">
	                                <div class="col-xs-6 col-sm-4">
	                                                                    
	                                </div>
	                                 <div class="col-xs-6 col-sm-4">
 	                                </div>
	                                <div class="col-xs-6 col-sm-4">
	                                <a href="<?php echo base_url('configuracion/grupo'); ?>" id="cancelarFormBtn" class="btn btn-w-m btn-default">Cancelar</a>
	                                     <input type="button" id="contrasenaFormBtn" class="btn btn-w-m btn-primary botones" value="Guardar">	  
 	                                </div>
 	                            </div>
								</div>
								
								 
					   </div>
						 <?php echo form_close(); ?>
						
						
						
						 </div>                </div>
      
                	</div>
                </div>
               
  			 
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

