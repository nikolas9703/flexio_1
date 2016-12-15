<?php if(preg_match("/ajustes/i", self::$ci->router->fetch_class())):?>
<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content" id="ajustes_form_div">

	            <div class="row">
                        <?php endif;?>
                        <div class="tab-content">
                            <div id="datosgeneralesdelproveedor-43" class="tab-pane active">
                                <?php
                                    $aux = [
                                        "method" => "POST",
                                        "id" => "ajustes_form",
                                        "autocomplete" => "off"
                                    ];
                                    echo form_open(base_url("ajustes/guardar"), $aux);
                                ?>
                                <div class="ibox">
                                    <div class="ibox-title border-bottom">
                                        <h5>Ajustes</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </div>
                                    </div>
                                    <?php echo modules::run('ajustes/ocultoformulario'); ?>
                                </div>
                                <?php echo form_close();?>
                            </div>
                        </div>
                        <?php if(preg_match("/ajustes/i", self::$ci->router->fetch_class())):?>
                    </div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php

    echo Modal::config(array(
        "id" => "optionsModal",
        "size" => "sm"
    ))->html();

?>

<?php endif;?>
