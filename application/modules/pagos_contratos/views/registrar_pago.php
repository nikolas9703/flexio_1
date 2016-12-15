<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content"  ng-controller="registrarCobroController">
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
                'autocomplete' => 'off'
              );

            echo form_open(base_url('cobros/guardar'), $formAttr);?>

            <div class="row rowhigth" ng-show="acceso">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <span>Aplicar cobro a </span>
                  </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <select  class="form-control" name="tipo" id="tipo" ng-model="tipo" ng-change="empezarDesde(tipo)" ng-disabled="disableTipo">
                  <option value="">Seleccione</option>
                  <option value="factura">Factura</option>
                  <option value="cliente">Cliente</option>
                </select>
              </div>
              <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <select  class="form-control" name="uuid_factura" id="uuid_factura" ng-model="uuid_factura" ng-disabled="tipo ==='' ||disableSelected">
                  <option value="">Seleccione</option>
                  <?php
                  foreach ($facturas as  $factura) {?>
                  <option value="<?php echo $factura['uuid_factura']?>"><?php echo $factura['cliente']['nombre'] .' - '. $factura['codigo']?></option>
                  <?php }?>
                  </select>
            </div>
              </div>
             </div>

              <div class="ibox border-bottom" ng-show="acceso">
                  <div class="ibox-title">
                      <h5>Datos del Pago</h5>
                      <div class="ibox-tools">
                          <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </div>
                  </div>

                  <div class="ibox-content" style="display:block;">
      	            <div class="row">
                      	<?php
                      		$info = !empty($info) ? array("info" => $info) : array();
                      		 echo modules::run('cobros/ocultoformulario', $info);
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
