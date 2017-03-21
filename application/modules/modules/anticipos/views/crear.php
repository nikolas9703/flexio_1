<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div id="formCrearAnticipos" class="wrapper-content">
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
                'id'           => 'form_crear_anticipos',
                'autocomplete' => 'off',
                'class' => 'vue-formulario'
              );

            echo form_open(base_url('anticipos/guardar'), $formAttr);?>

            <!--loading-->
			<div id="contFormAnticipo">
				<div class="row" v-if="config.loading"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span>Cargando...</span></div>
				<div class="row rowhigth" v-show="config.acceso" v-if="!config.loading">
						<!--componente empezar desde-->
						<empezar-desde :info="config" :empezable="empezable"></empezar-desde>
						<!--componente empezar desde-->
				</div>
            </div>


            <div id="hackf" class="ibox border-bottom" v-show="config.acceso && !config.loading" >
                <div class="ibox-title">
                    <h5>Datos del Anticipo</h5>
                    <div class="ibox-tools">
                          <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                    </div>
                </div>

                <div class="ibox-content" style="display:block;">
      	            <div class="row">
                      	<?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('anticipos/ocultoformulario', $info);
                      	?>
                    </div>
                </div>
            </div>
            <?php  echo  form_close();?>

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
