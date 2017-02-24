<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">

            	<div class="wrapper-content" id="manual_entry_div">

                  <?php
                  $formAttr = array(
                    'method'       => 'POST',
                    'id'           => 'manual_entry_form',
                    'autocomplete' => 'off'
                  );

                  echo form_open(base_url('entrada_manual/guardar'), $formAttr);?>

                  <div class="ibox border-bottom">

                      <div class="ibox-title">
                          <h5><i class="fa fa-info-circle"></i>&nbsp;Datos de la entrada manual <small></small></h5>
                          <div class="ibox-tools">
                              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                          </div>
                      </div>

                      <div class="ibox-content" style="display:block;">
          	               <div class="row" style="margin-right: 0px;">
                               <?php echo modules::run('entrada_manual/ocultoformulario'); ?>
                           </div>
                       </div>
                   </div>
                   <?php  echo  form_close();?>

                <div class="row"></div>

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
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
?>
