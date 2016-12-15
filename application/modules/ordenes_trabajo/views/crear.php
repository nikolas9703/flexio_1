<div id="wrapper">
    <?php Template::cargar_vista ( 'sidebar' );?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
			<div class="wrapper-content">
				<div class="row">

					<div class="loader"><span class="h5 font-bold block"><i class="fa fa-cog fa-spin fa-fw"></i> Cargando...</span></div>

					<?php
					$formAttr = array (
						'method' => 'POST',
						'id' => 'ordenTrabajoForm',
						'autocomplete' => 'off',
						'class' => 'hide animated'
					);
					echo form_open (base_url(uri_string()), $formAttr);
					?>
					<div style="background-color: #D9D9D9; padding: 6px 0 39px 10px">

						<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
							<label class="m-t-xs">Empezar orden de trabajo desde</label>
						</div>
						<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
							<select id="orden_de" class="white-bg" name="orden_de" v-chosen="orden_de" v-model="orden_de" data-placeholder="Seleccione" :disabled="id!=''">
								<option value="">Seleccione</option>
								<template v-for="option in ordenDesdeOptions" track-by="$index" >
                                 	<option :value="option.id">{{{option.nombre}}}</option>
                            	</template>
							</select>
						</div>
						<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
							<select id="orden_de_id" class="white-bg" name="orden_de_id"  v-model="orden_de_id" v-chosen="orden_de_id" :disabled="ordenDeIdOptions.length==0||id!=''">
								<option value="">Seleccione</option>
								<template v-for="option in ordenDeIdOptions" track-by="$index" >
                                 	<option :value="option.id">{{{option.nombre}}}</option>
                            	</template>
							</select>
						</div>
						<div class="col-xs-12 col-sm-3 col-md-6 col-lg-6 text-left"></div>
					</div>

					<!-- Tabs Content -->
					<div class="ibox">
						<div class="ibox-title border-bottom">
							<h5>Datos de la orden de trabajo</h5>
							<div class="ibox-tools">
								<a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
							</div>
						</div>
						<div class="ibox-content m-b-sm"
							style="display: block; border: 0px">
							<div class="row">

								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Nombre ">
									<label>Cliente <span class="required" aria-required="true">*</span></label>
									<select id="cliente_id" name="cliente_id" v-chosen="cliente_id" v-model="cliente_id" :disabled="disabledCampoCliente" data-rule-required="true">
										<option value="">Seleccione</option>
										<template v-for="cliente in clienteOptions" track-by="$index" >
		                                 	<option v-bind:value="cliente.id">{{{cliente.nombre}}}</option>
		                            	</template>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Tipo de orden de trabajo </label>
									<select name="tipo_orden_id" id="tipo_orden_id" name="tipo_orden_id" v-chosen="tipo_orden_id" v-model="tipo_orden_id" :disabled="orden_de ==='orden_venta'" @change="tipoSevicioSelect(tipo_orden_id)">
										<option value="">Seleccione</option>
										<template v-for="option in tiposOrdenOptions" track-by="$index">
		                                 	<option v-bind:value="option.id">{{{option.nombre}}}</option>
		                            	</template>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>&nbsp;</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="text" id="saldo" class="form-control" name="saldo_pendiente_acumulado" disabled="disabled" v-model="saldo_pendiente_acumulado" />
									</div>
									<label class="label-danger m-t-xs text-center col-xs-12 col-sm-12 col-md-12 col-lg-12">Saldo pendiente acumulado</label>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>&nbsp;</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="text" id="credito_favor" class="form-control" name="credito_favor" disabled="disabled" v-model="credito_favor" />
									</div>
									<label class="m-t-xs text-center col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background: #5cb85c; color: #fff;">Credito a favor</label>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Fecha de inicio </label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" id="fecha_inicio" value="" name="fecha_inicio" class="form-control" v-datepicker="fecha_inicio" readonly="readonly">
									</div>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Fecha de planificada de fin </label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" id="fecha_planificada_fin" value="" name="fecha_planificada_fin" class="form-control fecha-evaluacion" v-datepicker="fecha_planificada_fin" readonly="readonly">
									</div>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Fecha real de finalizaci&oacute;n </label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" id="fecha_real_fin"  name="fecha_real_fin" class="form-control fecha-evaluacion" v-datepicker="fecha_real_fin" readonly="readonly">
									</div>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Centro contable <span class="required">*</span></label>
									<select name="centro_id" id="centro_id" v-chosen="centro_contable_id" v-model="centro_contable_id" data-rule-required="*">
										<option value="">Seleccione</option>
										<template v-for="option in listaCentrosOptions" track-by="$index" >
		                                 	<option v-bind:value="option.id">{{{option.nombre}}}</option>
		                            	</template>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Lista de precio </label>
									<select name="lista_precio_id" id="lista_precio_id" v-chosen="lista_precio_id" v-model="lista_precio_id" v-select2="lista_precio_id" :config="config.select2">
										<option value="">Seleccione</option>
										<option v-bind:value="option.id" v-for="option in listaTipoPrecioOptions" track-by="$index">{{{option.nombre}}}</option>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Facturable <span class="required">*</span></label>
									<select name="facturable_id" id="facturable_id" v-chosen="facturable_id" v-model="facturable_id" data-rule-required="true" :disabled="orden_de ==='orden_venta'">
										<option value="">Seleccione</option>
										<template v-for="option in listaFacturableOptions" track-by="$index" >
		                                 	<option v-bind:value="option.id">{{{option.nombre}}}</option>
		                            	</template>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Despacho desde bodega </label>
									<select name="bodega_id" id="bodega_id" v-chosen="bodega_id" v-model="bodega_id">
										<option value="">Seleccione</option>
										<template v-for="option in listaBodegasOptions" track-by="$index" >
		                                 	<option v-bind:value="option.id">{{{option.nombre}}}</option>
		                            	</template>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
									<label>Estado <span class="required">*</span></label>
									<select name="estado_id" id="estado_id" v-model="estado_id" class="form-control"
                                    data-rule-required="true" @change="CambiarEstado(estado_id)">
										<option value="">Seleccione</option>
										<template v-for="option in estadosOptions" track-by="$index" >
		                                 	<option v-bind:value="option.id">{{{option.nombre}}}</option>
		                            	</template>
									</select>
								</div>
							</div>
<div class="row" v-if="soloOrdenesVentas">
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
        <label>Equipo Trabajo</label>
        <select name="equipo_trabajo_id" id="equipos_trabajo_id" v-model="equipo_trabajo_id" class="form-control">
            <option value="">Seleccione</option>
            <option v-for="option in filtroEquipotrabajo" :value="option.id" v-text="option.nombre"></option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label>Centro Facturaci&oacute;n</label>
        <select name="centro_facturable_id" id="centro_facturable_id" v-model="centro_facturable_id" class="form-control" data-rule-required="true">
            <option value="">Seleccione</option>
                <option v-for="option in clienteCentroFacturable" :value="option.id" v-text="option.nombre"></option>
        </select>
    </div>
</div>
							<div class="row">
								<div class="col-lg-12">

                  <!--SE AGREGA TABLA DINAMICA DE ITEMS CON TYPE HEAD-->
                  <articulos :config="config" :detalle.sync="detalle" :catalogos="catalogos" v-if="tipo_orden_id == 1"></articulos>
                  <!--SE AGREGA TABLA DINAMICA-->

									<div class="col-lg-12" v-if="tipo_orden_id != 1">
                    <!-- :lista_servicios.sync="lista_servicios" -->

    									<servicios v-ref:servicios v-for="servicio in lista_servicios" track-by="$index" :categorias="categorias" :servicio.sync="servicio" :index="$index"></servicios>

    									<input type="hidden" v-model="delete_items" name="delete_items">
    									<input type="hidden" v-model="delete_servicios" name="delete_servicios">
    									<table class="table tabla-dinamica">
    										<tfoot>
    											<tr>
    												<td width="80%"></td>
    												<td width="10%" class="sum-border"> <span>Subtotal: </span><span id="tsubtotal" class="sum-total">${{subtotal}}</span></td>
    												<td width="10%"><input type="hidden" name="subtotal" value="0" v-model="subtotal" /></td>
    											</tr>
                                                                                            <tr>
    												<td width="80%"></td>
    												<td width="10%" class="sum-border"> <span>Descuento: </span><span id="tdescuento" class="sum-total">${{descuento}}</span></td>
    												<td width="10%"><input type="hidden" name="descuento" value="0" v-model="descuento" /></td>
    											</tr>
    											<tr>
    												<td width="80%"></td>
    												<td width="10%" class="sum-border"><span>Impuesto:</span> <span id="timpuesto" class="sum-total">${{impuesto}}</span></td>
    												<td width="10%"><input type="hidden" name="impuestos" id="himpuesto" value="0" v-model="impuesto" /></td>
    											</tr>
    											<tr>
    												<td width="80%"></td>
    												<td width="80%" class="sum-border"><span>Total: </span> <span id="ttotal" class="sum-total">${{total}}</span></td>
    												<td width="10%"><input type="hidden" name="total" value="0" v-model="total"></td>
    											</tr>
    											<tr>
    												<td width="80%"></td>
    												<td width="80%" class="sum-border"><span class="label label-successful">Cobros </span> <span class="sum-total">${{cobros}}</span></td>
    												<td width="10%"></td>
    											</tr>
    											<tr>
    												<td width="80%"></td>
    												<td width="80%" class="sum-border"><span class="label label-danger">Saldo </span> <span class="sum-total">${{saldo}}</span></td>
    												<td width="10%"></td>
    											</tr>
    										</tfoot>
    									</table>
									</div>
									<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
										<label>Observaciones </label>
										<textarea id="comentario" name="comentario" v-model="comentario" class="form-control">{{comentario}}</textarea>
									</div>

									<?php echo modules::run('ordenes_trabajo/cargar_templates_vue'); ?>

								</div>
							</div>

							<!-- Balance -->

							<div class="row m-t-lg">
								<div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                  <a class="btn btn-default btn-block" href="<?= base_url("ordenes_trabajo/listar");?>">Cancelar</a>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
									<button class="btn btn-primary btn-block" id="guardarBtn" :disabled="guardarBtnDisabled" type="button" @click.stop.prevent="guardar">{{{guardarBtn}}}</button>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="id" v-model="id" />
					<?php
					echo Modal::config(array(
						"id" => "opcionesModal",
						"size" => "sm",
						"titulo" => "{{{modal.titulo}}}",
						"contenido" => "{{{modal.contenido}}}",
						"footer" => "{{{modal.footer}}}",
					))->html();
					echo form_close();
					?>
				</div>
				<td v-if="id!=''">
				<?php echo modules::run('ordenes_trabajo/ocultoformulariocomentarios'); ?>
				</td>

			</div>

		</div>
		<!-- cierra .col-lg-12 -->
	</div>
	<!-- cierra #page-wrapper -->
</div>
<!-- cierra #wrapper -->
<?php

?>
