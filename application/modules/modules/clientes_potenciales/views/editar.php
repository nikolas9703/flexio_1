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
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>
	            
	            <div class="row">
                	
                        <div class="tab-content">
                            <div id="datosgeneralesdelclientepotencial-43" class="tab-pane active">
                                <?php 
                                    $aux = [
                                        "method"        => "POST",
                                        "id"            => "crearClientePotencialForm",
                                        "autocomplete"  => "off"
                                    ];
                                    echo form_open(base_url("clientes_potenciales/guardar"), $aux);
                                ?>
                                <div class="ibox">
                                    <div class="ibox-title border-bottom">
                                        <h5>Datos generales del cliente potencial</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </div>
                                    </div>
                                    <?php echo modules::run('clientes_potenciales/ocultoformulario', $campos); ?>
                                </div>
                                <?php echo form_close();?>
                            </div>
                        </div>
                        
                    </div>
                    
                    <br/><br/>
	   	<?php echo modules::run('clientes_potenciales/ocultoformulariocomentarios'); ?>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<!-- inicia #optionsModal -->
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
<!-- termina #optionsModal -->
