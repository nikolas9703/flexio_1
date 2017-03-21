<div id="wrapper">
<?php  
Template::cargar_vista('sidebar'); 

if($politicas['contrasena']['configuracion_avanzada'] == 1 ){
 	$Exp_regular = '/^(?=.*[A-Za-z]{'.$politicas['contrasena']['minima_cantidad_letras'].',})(?=.*\d{'.$politicas['contrasena']['minima_cantidad_numeros'].',})(?=.*[$@$!%*#?&]{'.$politicas['contrasena']['minima_cantidad_caracteres'].',})[A-Za-z\d$@$!%*#?&]{'.$politicas['contrasena']['long_minima_contrasena'].',}$/i';
 	$mensaje = 'Para la contraseña al menos se requiere '.$politicas['contrasena']['long_minima_contrasena'].' Caracteres, '.$politicas['contrasena']['minima_cantidad_letras'].' letras, '.$politicas['contrasena']['minima_cantidad_numeros'].' números y '.$politicas['contrasena']['minima_cantidad_caracteres'].' caracteres especiales.';
}else{
 	$Exp_regular = '';
 	$mensaje='';
}
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
                    <input type="hidden" id="editar_perfil" value="<?php echo $politicas['usuario']['editar_perfil']; ?>"   />
                      <?php  Template::cargar_formulario($info); ?> 
                      <input type="hidden" id="expr_regular" value="<?php echo $Exp_regular;?>"> 
                      <input type="hidden" id="longitud_minima" value="<?php echo $politicas['contrasena']['long_minima_contrasena'];?>"> 
	                     
                 	</div>
                </div>
               
  			 
			</div>
			<div class="row">
				 	<?php if($mensaje !=''){?>
				 
							<div class="alert alert-warning alert-dismissable" style="width: 50%;" >
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?php echo $mensaje; ?>.                            </div>
                      <?php } ?>          
				 
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

