<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
            <div class="wrapper-content" id="listar_series_div">

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">
                            <!-- JQGRID -->
                            <?php echo modules::run('series/ocultotabla'); ?>
                            <!-- /JQGRID -->
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->

        <?php echo Modal::config(array(
        	"id" => "documentosModal",
        	"size" => "lg",
        	"titulo" => "Subir Documentos",
        	"contenido" => modules::run("documentos/formulario", array())
        ))->html(); ?>  <!-- modal subir documentos -->

    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
