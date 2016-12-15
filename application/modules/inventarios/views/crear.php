<?php if(preg_match("/inventarios/i", self::$ci->router->fetch_class())):?>
<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content" id="crear_items_form_div">

                <div class="row">
                    <?php endif;?>

                    <?php

                    $formAttr = array(
                        'method' => 'POST',
                        'id' => 'crear_items_form',
                        'autocomplete' => 'off'
                    );

                    echo form_open(base_url('inventarios/guardar'), $formAttr);
                    echo modules::run('inventarios/ocultoformulario');
                    echo form_close();

                    ?>

                    <?php if(preg_match("/inventarios/i", self::$ci->router->fetch_class())):?>
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
