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
                <div id="mensaje_info"></div>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>

                  <!-- formulario de clientes -->
                	<?php
                		$info = !empty($info) ? array("info" => $info) : array();
                		echo modules::run('clientes/ocultoformulario', $info);
                	?>
                <div  ng-controller="contactoFormularioController">
                      <!-- formulario de contacto -->
                      <div id="vistaFormularioContacto" class="hide">
                          <?php
                          $contacto = !empty($contacto) ? array("info" => $contacto) : array();
                          echo  modules::run('contactos/ocultoformulario', $contacto);?>
                    </div>


                  <?php Subpanel::visualizar_grupo_subpanel($uuid_cliente); ?>
                </div>

                <!-- Comentarios -->
                 <div class="row" id="form_crear_cliente_div">
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
