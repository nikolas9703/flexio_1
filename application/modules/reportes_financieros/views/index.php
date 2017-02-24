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
	            <div class="row" ng-controller="toastController">
                  <?php //$mensaje = self::$ci->session->flashdata('mensaje'); ?>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>

      				<div role="tabpanel">
      				 	<!-- Tab panes -->
      				  	<div class="row tab-content">
      				    	<div role="tabpanel" class="tab-pane active" id="tabla">
                      <div class="row">
                         <h3>Estados financieros</h3>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="balance_situacion" href="<?php echo base_url('reportes_financieros/reporte/balance_situacion');?>">Hoja de balance de situaci&oacute;n</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">El resumen de lo que se tiene, lo que se debe y el valor del negocio.</p>
                            </div>
                        </div>
                         </div>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="ganancias_perdidas" href="<?php echo base_url('reportes_financieros/reporte/ganancias_perdidas');?>">Estado de ganancias y p&eacute;rdidas</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Ingresos menos gastos; le indica si sus ingresos fueron mayor a sus gastos en un periodo.</p>
                            </div>
                        </div>
                         </div>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="flujo_efectivo" href="<?php echo base_url('reportes_financieros/reporte/flujo_efectivo');?>">Flujo de efectivo</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Movimiento de dinero en efectivo de su negocio; incluye notas de crédito aplicadas a facturas de compras y ventas.</p>
                            </div>
                        </div>
                         </div>
                      </div>
                      <!-- fin de estados financieros-->
                      <div class="row">
                         <h3>Impuestos</h3>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="impuesto_sobre_venta" href="<?php echo base_url('reportes_financieros/reporte/impuestos_sobre_ventas');?>">Informe de impuestos sobre las ventas</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Vea los impuestos sobre las ventas que ha pagado y recibido.
                              </p>
                            </div>
                        </div>
                         </div>
                      </div>
                      <!-- fin de impuestos-->
                      <div class="row">
                         <h3>Clientes</h3>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="estado_cuenta_cliente" href="<?php echo base_url('reportes_financieros/reporte/estado_de_cuenta_de_cliente');?>">Estado de cuenta de cliente</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Vea el ingreso que recibió, desglosado por fuente.</p>
                            </div>
                        </div>
                         </div>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="cuenta_por_cobrar_por_antiguedad" href="<?php echo base_url('reportes_financieros/reporte/cuenta_por_cobrar_por_antiguedad');?>">Cuentas por cobrar por antigüedad</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Vea la cantidad de dinero que se espera por recibir, y el tiempo que ha estado esperando por el.</p>
                            </div>
                        </div>
                         </div>
                      </div>
                      <!-- fin de clientes-->
                      <div class="row">
                         <h3>Proveedores</h3>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="estado_cuenta_proveedor" href="<?php echo base_url('reportes_financieros/reporte/estado_cuenta_proveedor');?>">Estado de cuenta de proveedor</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Vea lo que pagó en gastos, desglosado por destinatario.</p>
                            </div>
                        </div>
                         </div>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="cuenta_por_pagar_antiguedad" href="<?php echo base_url('reportes_financieros/reporte/cuenta_por_pagar_por_antiguedad');?>">Cuentas por pagar por antigüedad</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Conozca los gastos que no ha pagado aún, y por cuánto tiempo el pago ha sido sobresaliente.</p>
                            </div>
                        </div>
                         </div>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="reporte_caja" href="<?php echo base_url('reportes_financieros/reporte/reporte_caja');?>">Reporte de cajas</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Informe de caja menuda.</p>
                            </div>
                           </div>
                         </div>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="cuenta_por_pagar_antiguedad" href="<?php echo base_url('reportes_financieros/reporte/costo_por_centro_compras');?>">Reporte de compras</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Informe de costos por centro contable y categor&iacute;a de items.</p>
                            </div>
                        </div>
                         </div>
                      </div>
                      <!-- nuevo-->
                      <div class="row">
                         <h3>Informes Fiscales</h3>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="estado_cuenta_proveedor" href="<?php echo base_url('reportes_financieros/reporte/formulario_43');?>">Formulario 43</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Informe de compras e importaciones de bienes y servicios.</p>
                            </div>
                        </div>
                         </div>
                         <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
                           <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><a id="cuenta_por_pagar_antiguedad" href="<?php echo base_url('reportes_financieros/reporte/formulario_433');?>">Formulario 433</a></h5>
                            </div>
                            <div class="ibox-content">
                              <p class="parrafo">Informe de retecion de impuesto.</p>
                            </div>
                        </div>
                         </div>
                      </div>
                      <!-- reporte-->
      				    	</div>
      				  	</div>
      				</div>
         </div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->