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
	            
	            <div class="row">
                  	<?php 
                		$info = !empty($info) ? array("info" => $info) : array();
                		echo modules::run('comisiones/ocultoformulario', $info); 
                	?>
                </div> 
                 <div class="row">
	               <div class="form-group col-sm-3"> </div>
	                <div class="form-group col-sm-3"> </div>
	                <div class="form-group col-sm-3"> </div>
                  <div class="form-group col-sm-3">
                  <?php if($permiso_editar == 1) {?>
               <!--    <button type="button" class="btn btn-primary btn-block" id="agregarColaborador">Agregar Colaborador</button> </div> -->
                  <?php } ?>
                 </div>
                <!-- JQGRID -->
				<?php echo modules::run('comisiones/ocultotablacolaboradores'); ?>
				<!-- /JQGRID -->
					 <br/><br/>
					 <?php echo modules::run('comisiones/ocultoformulariocomentarios'); ?>
         	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php 

echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();


?>
  
  <div id ="oculto"></div>
 <div class="modal fade" id="pantallaAgregarColaborador" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
         <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Agregar Colaboradores: </h4>
            </div>
             <div class="modal-body">
              
 
								 	
					 <div class="row">
										<div class="col-xs-5">
 											<select name="from[]" id="lista_colaboradores" class="form-control" size="8" multiple="multiple">
											<?php 
											
											
									$opciones = '';
 									 if(!empty($colaboradores_noactivados)){
										foreach ($colaboradores_noactivados AS $row){
 									?>
											<option value="<?php echo $row->id; ?>"><?php echo $row->apellido.', '.$row->nombre." - ".$row->cedula; ?></option>
										 
									<?php   }}?>
									 
											</select>
										</div>
										
										<div class="col-xs-2">
											<button type="button" id="lista_colaboradores_rightAll" class="btn btn-block"><i class="fa fa-forward"></i></button>
											<button type="button" id="lista_colaboradores_rightSelected" class="btn btn-block"><i class="fa fa-chevron-right"></i></button>
											<button type="button" id="lista_colaboradores_leftSelected" class="btn btn-block"><i class="fa fa-chevron-left"></i></button>
											<button type="button" id="lista_colaboradores_leftAll" class="btn btn-block"><i class="fa fa-backward"></i></button>
										</div>
										
										<div class="col-xs-5">
											<select name="to[]" id="lista_colaboradores_to" class="form-control" size="8" multiple="multiple"></select>
										</div>
									</div>
				 
            </div>
            <div class="modal-footer">
            
            
            <div class="row"> 
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>
	   		   </div>
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="confimrarAgregarColaborador" class="btn btn-w-m btn-primary btn-block" type="button">Agregar</button>
	   		   </div>
	   		   </div>
            
            
            </div>
        </div>
    </div>
</div>
