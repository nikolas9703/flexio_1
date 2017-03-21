<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content"  ng-controller="crearPagoController">
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
                'id'           => 'form_crear_pago',
                'autocomplete' => 'off'
              );

            echo form_open(base_url('pagos_contratos/guardar'), $formAttr);?>

            <div class="row rowhigth" ng-show="acceso">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <span>Aplicar pago a </span>
                  </div>
                <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <select  class="form-control" name="tipo" id="tipo" ng-model="pagosHeader.tipo" ng-change="empezarDesde(pagosHeader.tipo)" ng-disabled="disableTipo">
                  <option value="">Seleccione</option>
                  <option value="factura">Factura</option>
                  <option value="proveedor">Proveedor</option>
                  <option value="subcontrato">Subcontrato</option>
                  <option value="planilla">Planilla</option>
                </select>
              </div>
              <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <select  class="form-control" name="crear_desde" id="crear_desde" ng-model="pagosHeader.uuid" ng-disabled="pagosHeader.tipo === '' || disableTipo" ng-change="llenarFormulario(pagosHeader.uuid)" ng-options="valores as valores.nombre for valores in pagosHeader.collection track by valores.uuid">
                  <option value="">Seleccione</option>
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
                      		 echo modules::run('pagos_contratos/ocultoformulariover', $info);
                      	?>
                    </div>
            </div>
          </div>
              <?php  echo  form_close();?>
                <br/><br/>
                <?php echo modules::run('pagos_contratos/ocultoformulariocomentarios'); ?>
        	</div>
          <div class="row col-lg-12 col-md-12 col-xs-12">
            <?php //Subpanel::visualizar_grupo_subpanel($cliente_id); ?>
          </div>
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
