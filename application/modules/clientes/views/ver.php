<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content" id="formClienteCrearDiv">
	            <div class="row">

                  <!-- formulario de clientes -->
                  <div v-if="!config.showFormContacto">
                      <?php
                          echo modules::run('clientes/ocultoformulario');
                  	?>
                  </div>

                  <div>
                        <!-- formulario de contacto -->
                        <div id="vistaFormularioContacto" v-if="config.showFormContacto">

                            <formulario-contacto :catalogos="catalogos" :detalle.sync="contacto" :config="config"></formulario-contacto>

                        </div>


                        <div class="row">
                            <?php SubpanelTabs::visualizar($subpanels); ?>
                        </div>
                    </div>

                    <!-- Comentarios -->
                    <div class="row">
                        <vista_comments
                        v-if="config.vista === 'ver'"
                        :config="config"
                        :historial.sync="comentario.comentarios"
                        :modelo="comentario.comentable_type"
                        :registro_id="comentario.comentable_id"
                        ></vista_comments>
                    </div>
                 <!-- Comentarios -->

        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php echo Modal::config(array( "id" => "optionsModal", "size"  => "sm"))->html();?> <!-- modal opciones -->
