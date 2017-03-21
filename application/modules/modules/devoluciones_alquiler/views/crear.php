<div id="wrapper">
    <?php Template::cargar_vista ( 'sidebar' );?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
			<div class="wrapper-content">
			    <div class="row">
                    <div id="mensaje_info"></div>
                    <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo!empty($mensaje) ? $mensaje["mensaje"] : '' ?>
                    </div>
                </div>
				<div class="row">

					<?php
					$formAttr = array (
						'method' => 'POST',
						'id' => 'form_devoluciones_alquiler',
 						'autocomplete' => 'off'
					);
 					echo form_open(base_url('devoluciones_alquiler/crear'), $formAttr);
					?>

          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #D9D9D9;padding: 7px 0 7px 0px;">

						<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
							<label class="m-t-xs">Empezar retorno desde</label>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
  						      <select :disabled="disabledEditar" class="form-control" name="campo[empezar_desde_type]" required="" data-rule-required="true" v-select2="devolucion_alquiler.empezar_desde_type" v-model="devolucion_alquiler.empezar_desde_type" @change="cambiarTipo(devolucion_alquiler.empezar_desde_type)">
                          <option value="">Seleccione</option>
                          <option value="entrega">Entrega</option>
					                <option value="Contrato de alquiler">Contrato de alquiler</option>
                   </select>
  						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">

						 <select :disabled="disabledHeaderEmpezableId" class="form-control" required="" data-rule-required="true" class="white-bg" name="campo[empezar_desde_id]" v-select2="devolucion_alquiler.empezar_desde_id" v-model="devolucion_alquiler.empezar_desde_id"  @change="cambiarEmpezable(devolucion_alquiler.empezar_desde_type, devolucion_alquiler.empezar_desde_id)"  >
								<option value="">Seleccione</option>
 								<option v-for="retorno in empezables" v-bind:value="retorno.id">
                                        {{retorno.codigo}} - {{retorno.cliente_nombre}}
                                      </option>
 							</select>
 						</div>
						<div class="col-xs-12 col-sm-3 col-md-6 col-lg-6 text-left"></div>
					</div>

					<!-- Tabs Content -->
					 <div class="ibox">
						<div class="ibox-title border-bottom">
							<h5>Datos del retorno</h5>
							<div class="ibox-tools">
								<a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
							</div>
						</div>
						<div class="ibox-content m-b-sm"
							style="display: block; border: 0px">
							<div class="row">

								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Nombre ">
									<label>Cliente <span class="required" aria-required="true">*</span></label>
									<select id="cliente_id" name="campo[cliente_id]" class="form-control"  v-model="cliente_id" :disabled="true">
										<option value="">Seleccione</option>
										<template v-for="cliente in clienteOptions" track-by="$index" >
		                                 	<option v-bind:value="cliente.id">{{{cliente.nombre}}}</option>
		                            	</template>
									</select>
								</div>
									<!--<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Fecha de alquiler de contrato </label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input :disabled="true" type="text" id="fecha_alquiler_contrato" value="" name="campo[fecha_alquiler_contrato]" class="form-control fecha-alquiler-contrato" v-datepicker="fecha_alquiler" readonly="readonly">
									</div>
								</div>-->
                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                  <label for="">Fecha de inicio y fin de contrato</label>
                  <div class="input-group">
                    <div class="input-group-addon"><span class="fa fa-calendar"></span></div>
                    <input type="input-left-addon" name="campo[fecha_inicio_contrato]" id="fecha_inicio" class="form-control" v-model="fecha_inicio_contrato" disabled>
                    <span class="input-group-addon">a</span>
                    <input type="input-left-addon" name="campo[fecha_fin_contrato]" id="fecha_final" class="form-control" v-model="fecha_fin_contrato" disabled>
                  </div>
                </div>

								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>&nbsp;</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="text" id="saldo" class="form-control" value="" name="campo[saldo_pendiente_acumulado]" disabled="disabled" v-model="saldo_pendiente_acumulado" />
									</div>
									<label class="label-danger m-t-xs text-center col-xs-12 col-sm-12 col-md-12 col-lg-12">Saldo pendiente acumulado</label>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>&nbsp;</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="text" id="credito_favor" class="form-control" value="" name="campo[credito_favor]" disabled="disabled" v-model="credito_favor" />
									</div>
									<label class="m-t-xs text-center col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background: #5cb85c; color: #fff;">Credito a favor</label>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Fecha y hora del retorno </label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input :disabled="false" type="text" id="fecha_devolucion" value="" name="campo[fecha_devolucion]" class="form-control"  >
									</div>
								</div>

								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Recibido por <span class="required">*</span></label>
									<select name="campo[recibido_id]" id="recibido_id" class="form-control"   :disabled="true" v-model="recibido_id" d required="" data-rule-required="true" >
 										<template v-for="option in recibidosOptions" track-by="$index" >
		                                 	<option v-bind:value="option.id">{{{option.nombre}}} {{{option.apellido}}}</option>
		                            	</template>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Vendedor <span class="required">*</span></label>
									<select data-rule-required="true" name="campo[vendedor_id]" id="vendedor_id"   class="form-control"   :disabled="true"    v-model="vendedor_id" >
 										<template v-for="option in vendedoresOptions | orderBy option.nombre" track-by="$index" >
		                                 	<option v-bind:value="option.id">{{{option.nombre}}} {{{option.apellido}}}</option>
		                            	</template>
									</select>
								</div>


								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Estado </label>
									<select class="grey-bg"  :disabled="disabledEstadoId"  required="" data-rule-required="true"  name="campo[estado_id]" id="estado_id" v-chosen="estado_id" v-model="devolucion_alquiler.estado_id">
										<option value="">Seleccione</option>
										<template v-for="option in estadosOptions" track-by="$index" >
		                                 	<option v-bind:value="option.id">{{{option.nombre}}}</option>
		                            	</template>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-12">

									 <tabla_entregas></tabla_entregas>
									<?php echo modules::run('devoluciones_alquiler/cargar_templates_vue'); ?>

								</div>
							</div>
                            <div class="row">
                                	 <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
                                    <label>Observaciones </label>
                                    <textarea id="observaciones" name="campo[observaciones]" ng-model="datosFactura.observaciones" class="form-control"><?php //if(isset($info['cotizacion'])){ echo $info['cotizacion']['observaciones']; }?></textarea>
                                  </div>
                              </div>




							<div class="row m-t-lg">
					 <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
									<a class="btn btn-default btn-block" href="<?= base_url("devoluciones_alquiler/listar");?>">Cancelar</a>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
									<button class="btn btn-primary btn-block" id="guardarBtn" :disabled="guardarBtnDisabled" type="button" @click="guardar">{{{guardarBtn}}}</button>
									<!-- <input type="button" id="guardarBtn" class="btn btn-primary btn-block" v-model="guardarBtn" @click.stop.prevent="guardar" :disabled="guardarBtnDisabled==true" /> -->

 								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="campo[id]" v-model="devolucion_alquiler.id" />
					<?php
					/*echo Modal::config(array(
						"id" => "opcionesModal",
						"size" => "sm",
						"titulo" => "{{{modal.titulo}}}",
						"contenido" => "{{{modal.contenido}}}",
						"footer" => "{{{modal.footer}}}",
					))->html();*/
					echo form_close();
					?>
				</div>
			</div>

		</div>
		<!-- cierra .col-lg-12 -->
	</div>
	<!-- cierra #page-wrapper -->
</div>
<!-- cierra #wrapper -->
