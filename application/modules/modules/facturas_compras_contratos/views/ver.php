<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content"  ng-controller="facturasController">
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
                'id'           => 'form_editar_facturas',
                'autocomplete' => 'off'
              );

            echo form_open(base_url('facturas_compras_contratos/guardar'), $formAttr);?>

            <div class="row rowhigth" ng-show="acceso">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group col-xs-12 col-sm-2 col-md-3 col-lg-3" style="padding-top: 7px; margin-bottom: 28px ! important;">
                        <span>Empezar factura desde </span>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-3 col-lg-3" style="margin-bottom: 0px ! important;">
                        <select  class="form-control" name="tipo" id="tipo" ng-model="tipo" ng-disabled="true" ng-change="ngChanged.empezarDesde(tipo)">
                            <option value="">Seleccione</option>
                            <option value="Ordenes_orm">&Oacute;rdenes de compra</option>
                            <option value="Flexio\Modulo\SubContratos\Models\SubContrato">Subcontratos</option>
                        </select>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-3 col-lg-3" style="margin-bottom: 0px ! important;">
                        <select  class="form-control" name="uuid_tipo" id="uuid_tipo" ng-model="uuid_tipo" ng-disabled="true" ng-change="ngChanged.empezarDesdeId(tipo, uuid_tipo)">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($operaciones as  $operacion) {?>
                            <option value="<?php echo $operacion["registro"]->id.$operacion["tipo"]?>" ng-show="<?php echo ($operacion["tipo"] == "orden_compra") ? "tipo=='Ordenes_orm'" : "tipo=='Subcontratos'"?>"><?php echo $operacion["registro"]->proveedor->nombre.' - '. $operacion["registro"]->numero_documento?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
            </div>

              <div class="ibox border-bottom" ng-show="acceso">
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
                      		 echo modules::run('facturas_compras_contratos/ocultoformulario', $info);
                      	?>
                    </div>
            </div>
          </div>
              <?php  echo  form_close();?>
                <div class="row" id="subpanel" style="margin-left: -15px;margin-right: -25px;">
                    <?php SubpanelTabs::visualizar($factura_compra_id); ?>
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
