<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content" id="form_crear_traslado_div">

                <?php

                    $formAttr = array(
                        'method' => 'POST',
                        'id' => 'form_crear_traslado',
                        'autocomplete' => 'off'
                    );

                    echo form_open(base_url('traslados/guardar'), $formAttr);
                ?>

                <!--componente empezar desde-->
                <empezar_desde :empezable.sync="empezable" :detalle.sync="detalle" :config.sync="config"></empezar_desde>

                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5>Datos del Traslado</h5>
                        <div class="ibox-tools">
                              <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>

                    <div class="ibox-content" style="display:block;">
          	            <div class="row">
                          	<?php
                                echo modules::run('traslados/ocultoformulario');
                          	?>
                        </div>
                    </div>
                </div>
                <?php  echo  form_close();?>

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
