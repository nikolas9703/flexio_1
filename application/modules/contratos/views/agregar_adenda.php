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

              <div class="ibox float-e-margins">
                  <div class="ibox-title">
                      <h5>Datos del Contrato</h5>
                      <div class="ibox-tools">
                          <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </div>
                  </div>

                  <div class="ibox-content" style="display:none;">
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
  <div id="formulario_adenda">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Datos de Adenda</h5>
            <div class="ibox-tools">
                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
            </div>
        </div>

        <div class="ibox-content" style="display:inline-block">
    <?php
    $formAttr = array(
      'method'       => 'POST',
      'id'           => 'form_crear_adenda',
      'autocomplete' => 'off'
    );
      echo form_open(base_url('contratos/guardar_adenda'), $formAttr);
      $info_adenda = !empty($adenda) ?  $adenda : array();
       echo modules::run('contratos/ocultoformularioAdenda', $info_adenda);
       echo  form_close();
    ?>
  </div>
</div>
  </div>
</div>
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
