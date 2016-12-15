<?php if(preg_match("/consumos/i", self::$ci->router->fetch_class())):?>
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
                        <?php echo modules::run('consumos/ocultoformulario', $campos); ?>
                        <?php if(preg_match("/consumos/i", self::$ci->router->fetch_class())):?>
                    </div>
                    
                    <?php //Subpanel::visualizar_grupo_subpanel("11E579C5908EA71997CAC4DA26054BB3"); ?>
        	</div>
	<!--comentarios-->
	<div id="rootApp" class="row">
		<vista_comments
			v-if="config.vista ==='editar'"
			:config="config"
			:historial.sync="comentarios"
			:modelo="modelo"
			:registro_id="id"
		></vista_comments>
	</div>
	<!--comentarios-->
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