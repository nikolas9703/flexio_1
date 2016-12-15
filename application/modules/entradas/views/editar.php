<?php if(preg_match("/entradas/i", self::$ci->router->fetch_class())):?>
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
                        <div class="tab-content">
                            <div id="datosgeneralesdelproveedor-43" class="tab-pane active">
                                <?php 
                                    $aux = [
                                        "method"        => "POST",
                                        "id"            => "editarEntradasForm",
                                        "autocomplete"  => "off"
                                    ];
                                    echo form_open(base_url("entradas/guardar"), $aux);
                                ?>
                                <div class="ibox">
                                    <div class="ibox-title border-bottom">
                                        <h5>Datos generales de la entrada</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </div>
                                    </div>
                                    <?php echo modules::run('entradas/ocultoformulario', $campos); ?>
                                </div>
                                <?php echo form_close();?>
                            </div>
                        </div>
                        <?php if(preg_match("/entradas/i", self::$ci->router->fetch_class())):?>
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