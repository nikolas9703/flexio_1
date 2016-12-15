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
	            
	            <div class="row" ng-controller="identificacionController">
                        <div class="tab-content">
                            <div id="datosgeneralesdelproveedor-43" class="tab-pane active">
                                <?php 
                                    $aux = [
                                        "method"        => "POST",
                                        "id"            => "crearAcreedoresForm",
                                        "autocomplete"  => "off"
                                    ];
                                    echo form_open(base_url("acreedores/guardar"), $aux);
                                ?>
                                <div class="ibox">
                                    <div class="ibox-title border-bottom">
                                        <h5>Datos generales del acreedor</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </div>
                                    </div>
                                    <?php echo modules::run('acreedores/ocultoformulario'); ?>
                                </div>
                                <?php echo form_close();?>
                            </div>
                        </div>   
                    </div>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->