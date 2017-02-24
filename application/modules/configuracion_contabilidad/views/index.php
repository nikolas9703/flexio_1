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

              <div class="wrapper-content <?php echo !empty($mensaje) ? 'hide' : ''  ?>">
              <div class="row">
                <ul class="nav nav-tabs" id="configuracionTabs">
                      <li class="active"><a data-toggle="tab" href="#Impuesto">Impuestos</a></li>
                      <li><a data-toggle="tab" href="#Cuenta_pagar">Cuenta por Pagar</a></li>
                      <li><a data-toggle="tab" href="#Cuenta_cobrar">Cuenta por Cobrar</a></li>
                      <li><a data-toggle="tab" href="#bancos">Bancos</a></li>
                      <li><a data-toggle="tab" href="#Cajamenuda">Caja Menuda</a></li>
                      <li><a data-toggle="tab" href="#abonos">Anticipos</a></li>
                      <li><a data-toggle="tab" href="#inventario">Inventario</a></li>
					  <li><a data-toggle="tab" href="#planilla">Planilla</a></li>
                      <li><a data-toggle="tab" href="#contratos">Contratos</a></li>
                      <li><a data-toggle="tab" href="#seguros">Seguros</a></li>
               </ul>
           </div>
				<div class="tab-content row" ng-controller="configImpuestoController">
				 	<!-- Tab panes -->

							<!-- Tab Impuestos -->
							<div class="ibox-content tab-pane fade in active" id="Impuesto">
		                      <?php
		                            $formAttr = array(
		                                'method'        => 'POST',
		                                  'id'            => 'crearImpuestoForm',
		                                  'autocomplete'  => 'off'
		                                  );
		                                 echo form_open(base_url(uri_string()), $formAttr);
		                                ?>
													<div class="row" :disabled="!retiene_impuesto">
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
		                        <select placeholder="Seleccione" name="cuenta_id" id="cuenta_id"  class="form-control chosen-select" ng-model="impuesto.cuenta_id" >
		                          <option value="">Seleccione</option>
		                            <?php foreach ($pasivos as  $pasivo) {?>
		                                <option value="<?php echo $pasivo['id']?>"><?php echo $pasivo['nombre']?></option>
		                            <?php }?>
		                        </select>
		                     </div>
											 </div>
												 <div class="row" ng-hide="retiene_impuesto">
							                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
							                          <label for="">Retiene Impuesto</label>
							                           <select data-placeholder="Seleccione" name="retiene_impuesto" id="retiene_impuesto" class="form-control" ng-model="impuesto.retiene_impuesto" ng-change="cambioRetencion(impuesto.retiene_impuesto)">
							                             <option value="no">No</option>
							                             <option value="si">S&iacute;</option>
							                           </select>
							                        </div>
							                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
							                          <label for="">Porcentaje de retenci&oacute;n</label>
							                            <div class="input-group m-b">
							                              <input ng-model="impuesto.porcentaje_retenido" type="text" id="porcentaje_retenido" name="porcentaje_retenido" class="form-control" placeholder="" autocomplete="off" data-rule-required="true" data-rule-number="true" disabled>
							                              <span class="input-group-addon">%</span>

							                            </div>
																					<label id="porcentaje_retenido-error" class="error" for="porcentaje_retenido"></label>
							                        </div>

							                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
							                          <label for="">Cuenta para retenci&oacute;n</label>
							                           <select data-placeholder="Seleccione" name="cuenta_retenida_id" id="cuenta_retenida_id" class="form-control chosen-select" ng-model="impuesto.cuenta_retenida_id" disabled>
							                             <option value=''>Seleccione</option>
							                               <?php foreach ($pasivos as  $pasivo) {?>
							                                   <option value="<?php echo $pasivo['id']?>"><?php echo $pasivo['nombre']?></option>
							                               <?php }?>
							                           </select>
							                        </div>

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

						    		<?php echo modules::run('configuracion_contabilidad/ocultotablaimpuesto'); ?>

		                </div>
		                <!-- Tab Cuenta por Pagar -->
		                <div class="ibox-content tab-pane fade" id="Cuenta_pagar">
                        <div class="row">
                        <?php echo modules::run('configuracion_contabilidad/cuenta_por_pagar'); ?>

                        </div>
		                </div>

		                <!-- Tab Cuenta por Cobrar -->
		                <div class="ibox-content tab-pane fade"  id="Cuenta_cobrar">
		                  <div class="row">
		    	                <div class="alert alert-dismissable alert-info">
		    	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
		    	                    <p><strong>Seleccione</strong> una sola cuenta por cobrar</p>
		    	                </div>

		                    <?php echo modules::run('configuracion_contabilidad/ocultotablacuentacobrar'); ?>
		    	            </div>
		                </div>

                    <!-- Tab Bancos -->
		                <div class="ibox-content tab-pane fade"  id="bancos">
		                  <div class="row">
		                    <?php echo modules::run('configuracion_contabilidad/bancos'); ?>
		    	            </div>
		                </div>

		                <!-- Tab Caja Menuda -->
		                <div class="ibox-content tab-pane fade"  id="Cajamenuda">
		                  <div class="row">
		    	                <div class="alert alert-dismissable alert-info">
		    	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
		    	                    <p><strong>Seleccione</strong> una sola cuenta por cobrar</p>
		    	                </div>

		                    <?php echo modules::run('configuracion_contabilidad/cajamenuda'); ?>
		    	            </div>
		                </div>
                    <!-- Tab Abonos -->
		                <div class="ibox-content tab-pane fade"  id="abonos">
		                  <div class="row">
		                    <?php echo modules::run('configuracion_contabilidad/abonos'); ?>
		    	            </div>
		                </div>
                    <!-- Tab Inventario -->
		                <div class="ibox-content tab-pane fade"  id="inventario">
		                  <div class="row">
		                    <?php echo modules::run('configuracion_contabilidad/inventarios'); ?>
		    	            </div>
		                </div>
					<!-- Tab Planilla -->
					<div class="ibox-content tab-pane fade"  id="planilla">
						<div class="row">
							<div class="alert alert-dismissable alert-info">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
								<p><strong>Seleccione</strong> una sola cuenta de Bancos</p>
							</div>

							<?php echo modules::run('configuracion_contabilidad/planilla'); ?>
						</div>
					</div>

                                        <!-- Tab Contratos -->
	                <div class="ibox-content tab-pane fade"  id="contratos">
	                  <div class="row">
	                    <?php echo modules::run('configuracion_contabilidad/contratos'); ?>
	    	            </div>
	                </div>

	                <!-- Tab Seguros -->
	                <div class="ibox-content tab-pane fade"  id="seguros">
	                  <div class="row">
	                    <?php echo modules::run('configuracion_contabilidad/seguros'); ?>
	    	            </div>
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

echo Modal::config(array(
	"id" => "modalCambiarEstado",
	"size" => "sm",
  "contenido" => '<div id="loading-progress"></div>'
))->html();

?>
