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
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>
              <div class="row">
                <ul class="nav nav-tabs" id="configuracionTabs">
                      <li class="active"><a data-toggle="tab" href="#Impuesto">Impuestos</a></li>
                      <!-- <li><a href="#profile">Profile</a></li>
                      <li><a href="#messages">Messages</a></li> -->
               </ul>
           </div>
				<div class="tab-content row" ng-controller="configImpuestoController">
				 	<!-- Tab panes -->

							<!-- BUSCADOR -->

									<!-- Inicia campos de Busqueda -->
							     	<div class="ibox-content tab-pane fade in active" id="Impuesto">
                      <?php
                            $formAttr = array(
                                'method'        => 'POST',
                                  'id'            => 'crearImpuestoForm',
                                  'autocomplete'  => 'off'
                                  );
                                 echo form_open(base_url(uri_string()), $formAttr);
                                ?>
							        	<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
							            	<label for="">Nombre</label>
							            	<input ng-model="impuesto.nombre" type="text" id="nombre" name="nombre" class="form-control"  placeholder="" autocomplete="off" data-rule-required="true">
                       </div>
                     <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                       <label for="">Descripci&oacute;n</label>
                       <input ng-model="impuesto.descripcion" type="text" id="descripcion" name="descripcion" class="form-control" value="" placeholder="" autocomplete="off">
                     </div>
                     <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                       <label for="">Tasa de Impuesto</label>
                         <div class="input-group m-b">
                           <input ng-model="impuesto.impuesto" type="text" id="impuesto" name="impuesto" class="form-control"  placeholder="" autocomplete="off" data-rule-required="true" data-rule-number="true">
                           <span class="input-group-addon">%</span>
                         </div>
                     </div>
                     <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                       <label for="">Cuentas tipo: Pasivo</label>
                        <select name="cuenta_id[]" id="cuenta_id" multiple="true" class="form-control chosen-select" ng-model="impuesto.cuenta_id" >
                          <option value=""></option>
                            <?php foreach ($pasivos as  $pasivo) {?>
                                <option value="<?php echo $pasivo['id']?>"><?php echo $pasivo['nombre']?></option>
                            <?php }?>
                        </select>
                     </div>
                     <?php echo form_close(); ?>

									<div class="row">
							        	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">&nbsp;</div>
										<div class="form-group col-xs-6 col-sm-6 col-md-2 col-lg-2 pull-right">
                      <input type="button" id="guardarImpuestoBtn" ng-click="guardarImpuesto(impuesto)" class="btn btn-primary btn-block" value="Guardar" />
                    </div>
										<div class="form-group col-xs-6 col-sm-6 col-md-2 col-lg-2 pull-right">
                      <input type="button" ng-click="limpiarFormImpuesto($target)" id="cancelarImpuestoBtn" class="btn btn-default btn-block" value="Cancelar" />
										</div>
									</div>

				    		<?php echo modules::run('contabilidad/ocultotablaimpuesto'); ?>

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

echo Modal::config(array(
	"id" => "modalCambiarEstado",
	"size" => "sm",
  "contenido" => '<div id="loading-progress"></div>'
))->html();

?>
