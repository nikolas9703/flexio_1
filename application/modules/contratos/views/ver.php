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
                    <i class="fa fa-ban fa-lg"></i><?php echo !empty($mensaje) ? $mensaje["mensaje"] : ''  ?>
                </div>
	            </div>
              <?php
              $formAttr = array(
                'method'       => 'POST',
                'id'           => 'form_crear_contrato',
                'autocomplete' => 'off'
              );
            echo form_open(base_url('contratos/guardar'), $formAttr);?>

              <div class="ibox border-bottom">
                  <div class="ibox-title">
                      <h5>Datos del Contrato</h5>
                      <div class="ibox-tools">
                          <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </div>
                  </div>

                  <div class="ibox-content" style="display:block;">
      	            <div class="row"  v-if="acceso">
                      	<?php
                      		$info = !empty($contrato) ?  $contrato : array();
                      		 echo modules::run('contratos/ocultoformulario', $info);
                      	?>
                    </div>
            </div>
          </div>
              <?php  echo  form_close();?>



        	</div>
<div class="wrapper-content">
  <div class="row" id="subpanel">
    <?php SubpanelTabs::visualizar($contrato_id); ?>
  </div>
</div>
            <!--comentarios-->
            <div id="rootApp" class="row">
                <vista_comments
                    v-if="config.vista ==='editar'"
                    :config="config"
                    :historial.sync="comentarios"
                    :modelo="modelo"
                    :registro_id="id"
                ></vista_comments>
            </div>
            <!--comentarios-->
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

echo Modal::config(array(
	"id" => "optionsModal",
	"size" => "sm"
))->html();
