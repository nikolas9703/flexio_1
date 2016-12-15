<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content" id="form_crear_pago_div">

              <?php
              $formAttr = array(
                'method'       => 'POST',
                'id'           => 'form_crear_pago',
                'autocomplete' => 'off'
              );

            echo form_open(base_url('pagos/guardar'), $formAttr);?>

            <!--componente empezar desde-->
            <empezar_desde :empezable.sync="empezable" :detalle.sync="detalle" :config="config"></empezar_desde>

            <div class="ibox border-bottom">
                  <div class="ibox-title">
                      <h5>Datos del Pago</h5>
                      <div class="ibox-tools">
                          <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </div>
                  </div>

                  <div class="ibox-content" style="display:block;">
      	            <div class="row">
                      	<?php
                      		echo modules::run('pagos/ocultoformulario');
                      	?>
                    </div>
            </div>
          </div>
              <?php  echo  form_close();?>
                <div class="row col-lg-12 col-md-12 col-xs-12">
                    <?php //Subpanel::visualizar_grupo_subpanel($cliente_id); ?>
                </div>

                <!-- Comentarios -->
                 <div class="row">
                     <vista_comments
                      v-if="config.vista === 'editar'"
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
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
