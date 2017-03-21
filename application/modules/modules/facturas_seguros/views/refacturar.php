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
	                    <?php echo !empty($mensaje) ? $mensaje["mensaje"] : ''  ?>
	                </div>
	            </div>
              <?php
              $formAttr = array(
                'method'       => 'POST',
                'id'           => 'form_crear_facturas',
                'autocomplete' => 'off'
              );

            echo form_open(base_url('facturas/guardar_refactura'), $formAttr);?>


              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 header-ventas" v-show="acceso">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="text-header">Empezar factura desde </div>
                  </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <select  class="form-control" name="tipo" id="tipo" disabled>
                  <option value="refactura">Factura de Compra</option>
                </select>

              </div>
              <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <select  class="form-control" name="venta_uuid" id="venta_uuid" disabled>
                  <option value=""></option>
                  </select>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3"></div>
              </div>


              <div class="ibox border-bottom">
                  <div class="ibox-title">
                      <h5>Datos de la factura</h5>
                      <div class="ibox-tools">
                          <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </div>
                  </div>

                  <div class="ibox-content" style="display:block;">
      	            <div class="row">
                      	<?php
                      		$info = !empty($info) ? array("info" => $info) : array();
                      		 echo modules::run('facturas/ocultorefacturar', $info);
                      	?>
                    </div>
            </div>
          </div>
              <?php  echo  form_close();?>



        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
