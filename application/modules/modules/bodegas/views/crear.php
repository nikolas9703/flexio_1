<?php if(preg_match("/bodegas/i", self::$ci->router->fetch_class())):?>
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
                        <?php endif;?>
                        <div class="row">
                            <div class="tab-content">
                                <div id="datosgeneralesdelabodega-58" class="tab-pane active">
                                    <?php 
                                    
                                        $formAttr = array(
                                            'method'       => 'POST',
                                            'id'           => 'crearBodegasForm',
                                            'autocomplete' => 'off'
                                        );

                                        echo form_open(base_url('bodegas/crear'), $formAttr);
                                    ?>
                                        <div class="ibox">
                                            <div class="ibox-title border-bottom">
                                                <h5>Datos generales de la bodega</h5>
                                                <div class="ibox-tools">
                                                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                </div>
                                            </div>
                                            <div class="ibox-content m-b-sm" style="display: block; border:0px">
                                                <?php echo modules::run('bodegas/ocultoformulario', $campos); ?>
                                            </div>
                                        </div>
                                    <?php echo form_close();?>
                                </div>
                            </div>
                        </div>
                        <?php if(preg_match("/bodegas/i", self::$ci->router->fetch_class())):?>
                    </div>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php 

    echo    Modal::config(array(
                "id"    => "optionsModal",
                "size"  => "sm"
            ))->html();

?>

<?php endif;?>