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
 
	           
	            
	            
 <div id="accordion" class="panel-group">
							<div class="panel panel-white">
								<div class="panel-heading">
									<h5 class="panel-title">
										<a href="#collapseOne" data-parent="#accordion" data-toggle="collapse" aria-expanded="true" class="">Datos Generales de la Planilla</a>
									</h5>
								</div>
								<div class="panel-collapse collapse in" id="collapseOne" aria-expanded="true" style="">
									<div class="panel-body">
									  Detalle del colaborador va aqui
									</div>
								</div>
							</div>
 						 
						</div>							
							 
 				
                </div>
                 
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 
//echo $opciones;
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
?>
