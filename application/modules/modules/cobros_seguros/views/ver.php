<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div id="formCrearCobros" class="wrapper-content">
	            <div class="row">
                <div id="mensaje_info"></div>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["mensaje"] : ''  ?>
	                </div>
	            </div>
              <?php
              $formAttr = array(
                'method'       => 'POST',
                'id'           => 'form_crear_cobro',
                'autocomplete' => 'off',
                'class' => 'vue-formulario'
              );

            echo form_open(base_url('cobros_seguros/guardar'), $formAttr);?>

            <!--loading-->
            <div class="row"v-if="config.loading"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span>Cargando...</span></div>
            <!--loading-->
            <!--permisos-->

            <!--permisos-->
            <!--componente empezar desde-->
            <div class="row" v-show="config.acceso && !config.loading">
              <empezar-desde :info="config" :empezable="empezable"></empezar-desde>

            <!--componente empezar desde-->

            <!--componente formulario-->
            <formulario :catalogos="catalogoFormulario" :cobro="cobro" :config="config"></formulario>
            <!--componente formulario-->

          </div>

              <?php  echo  form_close();?>
              <!-- Comentarios -->
               <div class="row">
                   <vista_comments
                    v-if="config.vista === 'ver' && config.acceso && !config.loading""
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
echo Modal::config(array(
  "id" => "opcionesAnularModal",
  "size" => "md"
))->html();
?>
