<?php  
Template::cargar_vista('sidebar'); 

?>
<div id="page-wrapper" class="gray-bg row">
    
<?php Template::cargar_vista('navbar'); ?>
<div class="row border-bottom"></div>
<?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>
<div class="col-lg-12">
	<div class="wrapper-content">
    	
    	<div class="container-fluid">
                <div class="alert alert-success alert-dismissable message-box <?php echo !empty($message) ? 'show' : 'hide'  ?>">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
                    <?php echo !empty($message) ? $message : ''  ?>
                </div>
  				<?php
  			 
                $formAttr = array(
	                'method'       => 'post', 
	                'id'           => 'crearUsuarioForm',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
                ?>
				<div class="ibox float-e-margins border-bottom">
					<!--  <div class="ibox-title">
						<h3><i class="fa fa-camera"></i>    Perfil: <?=$info['nombre'].' '.$info['apellido'];?></h3>
						<div class="ibox-tools">
							<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
						</div>
					</div> -->
					
 				
                <div class="row">
               
					<div class="col-md-4 perfil-usuario">
					 <div class="col-md-12 perfil-heigh">		
					 	<div class="col-md-4">
						   <img alt="image" class="img-circle perfil-image img-responsive" src="<?php echo base_url('public/uploads').'/'.$info["imagen_archivo"];?>">
					   </div>   
					                   
		 			   <div class="col-md-8 reset-padding-rigth">
							<div id="perfil-nombre">	 			 
						        <label for="">Nombre</label>
							     <p class="form-control-static"><b><?=$info['nombre'].' '.$info['apellido'];?></b></p>  
		 					</div>		
		 					<div id="perfil-mail">					 
							<label for="">Correo Electr&oacute;nico</label>
							<p class="form-control-static"><b><?=$info['email'];?></b></p>
		 					</div>							 
		 			  </div>
		 			
		 			</div>
					 			        
                	<div class="col-md-12 last-login">				 
	 					<div class="col-md-6">
	 					 <div><h5>&Uacute;ltimo Login</h5></div>
	 					         <b><?php  echo $info['last_login']." ".$info['hace'];?></b>
	 				   </div>
	 				    <div class="col-md-6 ">
						   <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-cog"></i> <span class="hidden-sm hidden-xs">Opciones</span></button>               			 
	 				   </div>
                   </div>					 
             </div> <!-- Fin Perfil del usuario -->
					 			   
					 			 
						 		<div class="col-md-1"></div>
						 		 
					 			<div class="col-md-7 perfil-grupo-fechas">
					 			
									 <div data-toggle="buttons" class="btn-group opciones-group">
									      <label class="btn btn-sm btn-white"><input type="radio" id="option1" name="options" value="hoy"> Hoy </label>
									      <label class="btn btn-sm btn-white"> <input type="radio" id="option2" name="options" value="ayer"> Ayer </label>
									      <label class="btn btn-sm btn-white"> <input type="radio" id="option3" name="options" value="esta_semana"> Esta Semana </label>
									      <label class="btn btn-sm btn-white"> <input type="radio" id="option4" name="options" value="ultima_semana"> &uacute;ltima Semana </label>
									      <label class="btn btn-sm btn-white active"> <input checked="checked" type="radio" id="option5" name="options" value="este_mes"> Este Mes </label>
									      <label class="btn btn-sm btn-white"> <input type="radio" id="option6" name="options" value="ultimo_mes"> &uacute;ltimo Mes </label>
									  </div>
 					 			    	
										
										 
										<div class="col-md-12">
											<div class="col-md-4 col-sm-6 col-xs-12">
												<label for="">Oportunidades Ganadas</label>
			 									<span id="oportunidad_ganada" class="col-md-12 label label-perfil-titulo green-bg text-center">0</span>
			 									<div class="font-bold"><i class="carret-green carret-font fa fa-caret-up"></i><span id="ganado_porcentaje">0</span></div> 
											</div>
											<div class="col-md-4 col-sm-6 col-xs-12">
												<label for="">Oportunidades Perdidas</label>
												<span id="oportunidad_perdida" class="form-group col-md-12 label label-perfil-titulo red-bg text-center">0</span>
												<div class="font-bold"><i class="carret-red carret-font fa fa-caret-down"></i><span id="perdido_porcentaje">0</span></div> 
											</div>
											<div class="col-md-4 col-sm-6 col-xs-12">
												<label for="">Oportunidades Nuevas</label>
												<span id="oportunidad_nueva" class="col-md-12 label label-perfil-titulo green-bg text-center">0</span> 
												<div class="font-bold"><i class="carret-green carret-font fa fa-caret-up"></i><span id="nuevo_porcentaje">0</span></div> 
											</div>
 										</div>
										<div class="col-md-12">
											<div class="form-group col-md-4 col-sm-6 col-xs-12">
 			 									<span id="ganado_total" class="form-group col-md-12 col-sm-12 col-xs-12  label label-perfil-titulo green-bg">0</span>
 			 									<div class="font-bold"><i class="carret-green carret-font fa fa-caret-up"></i><span id="ganado_diferencia">0</span></div> 
											</div>
											<div class="form-group col-md-4 col-sm-6 col-xs-12">
 												<span id="perdido_total" class="form-group col-md-12 col-sm-12 col-xs-12  label label-perfil-titulo red-bg">0</span>
 												<div class="font-bold"><i class="carret-red carret-font fa fa-caret-down"></i><span id="perdida_diferencia">0</span></div> 
											</div>
											<div class="form-group col-md-4 col-sm-6 col-xs-12">
 												<span id="nuevo_total" class="form-group col-md-12 col-sm-12 col-xs-12  label label-perfil-titulo green-bg"> 0</span>
 												<div class="font-bold"><i class="carret-green carret-font fa fa-caret-up"></i><span id="nueva_diferencia">0</span></div>  
											</div>
 										</div>
										
										
										
										
										
								</div><!-- fin perfil grupo por fechas -->
					 	   
 								
								
						</div>
             <div class="row"> <!-- Actividades -->
             	<div class="col-md-12 perfil-actividades">
                	<div class="col-md-6">
                		<div class="perfil-actividad-title">Actividades Completadas</div>
                		  <div id="actividad_completadas">
	                		 <span class="form-group col-md-12 col-sm-12 col-xs-12  label-actividad-tareas gray-bg1"><i class="icon-size fa fa-slideshare"></i> 0 Llamadas</span>
	                		 <span class="form-group col-md-12 col-sm-12 col-xs-12  label-actividad-tareas gray-bg1"><i class="icon-size fa fa-group"></i> 0 Reuniones</span>
	                		 <span class="form-group col-md-12 col-sm-12 col-xs-12  label-actividad-tareas gray-bg1"><i class="icon-size fa fa-clock-o"></i> 0 Tareas</span>
	                		 <span class="form-group col-md-12 col-sm-12 col-xs-12  label-actividad-tareas gray-bg1" ><i class="icon-size fa fa-slideshare"></i> 0 Presentaciones</span>  
                 		 </div>
                 		</div>
                 		
               		<div class="col-md-6"><div class="perfil-actividad-title">Actividades Agregadas</div>
               		   <div id="actividad_agregadas">
	                		<span class="form-group col-md-12 col-sm-12 col-xs-12 label-actividad-tareas gray-bg1"><i class="icon-size fa fa-slideshare"></i> 0 Llamadas</span> <br />
	                		<span  class="form-group col-md-12 col-sm-12 col-xs-12 label-actividad-tareas gray-bg1"><i class="icon-size fa fa-group"></i> 0 Reuniones</span> <br />
	                		<span  class="form-group col-md-12 col-sm-12 col-xs-12 label-actividad-tareas gray-bg1"><i class="icon-size fa fa-clock-o"></i> 0 Tareas</span> <br />
	                		<span  class="form-group col-md-12 col-sm-12 col-xs-12 label-actividad-tareas gray-bg1"><i class="icon-size fa fa-slideshare"></i> 0 Presentaciones</span>
                   	  </div>
                   </div>
                </div>
             </div>
                
                
                
            
					 	
					  
  
 						
					</div>
					<?php echo form_close(); ?>
			
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
</div>
	
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
   <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Opciones: <?=$info['nombre'].' '.$info['apellido'];?></h4>
				</div>
				<div class="modal-body">
				
						<a href="<?php echo base_url("usuarios/ver-usuario/". $info['id_usuario']); ?>" id="" data-usuario="25" class="btn btn-block btn-outline btn-success" type="button">Actualizar Datos Personales</a>
					
				</div>
				<div class="modal-footer"></div>
			</div>
		</div>
</div>

