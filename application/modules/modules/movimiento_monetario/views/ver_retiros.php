<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content" id="movimiento_monetario_div">

                <?php
                $formAttr = [
                    'method' => 'POST',
                    'id' => 'movimiento_monetario_form',
                    'autocomplete' => 'off'
                ];
                echo form_open(base_url('movimiento_monetario/guardar'), $formAttr);?>

                <!--componente empezar desde-->
                <empezar_desde :empezable.sync="empezable" :detalle.sync="detalle" :config="config"></empezar_desde>
                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5><i class="fa fa-info-circle"></i> Datos del retiro de dinero <small></small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>

                    <div class="ibox-content" style="display:block;">
          	            <?php
                            echo modules::run('movimiento_monetario/ocultoformulario');
                        ?>
                    </div>
                </div>
                <?php  echo  form_close();?>

                <div class="row">
                    <?php SubpanelTabs::visualizar($subpanels); ?>
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
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
?>
