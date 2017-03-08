<template>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

	<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<label for="proveedor_id">Proveedor <span required="" aria-required="true">*</span></label>
		<select name="campo[proveedor_id]" class="form-control" data-rule-required="true" v-select2ajax="detalle.proveedor_id" :config="select2proveedor" :disabled="config.disableDetalle || empezable.type != ''">
                <option value="">Seleccione</option>
            </select>
		<label id="proveedor_id-error" class="error" for="proveedor_id"></label>
	</div>

	<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<label for="monto_factura">Monto de la factura</label>
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="input" disabled name="campo[monto_factura]" :value="detalle.monto_factura | currency ''" class="form-control">
		</div>
		<label id="termino_pago-error" class="error" for="termino_pago"></label>
	</div>

	<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3"><label></label>
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="input-left-addon" disabled value="{{detalle.proveedor.saldo_pendiente | currency ''}}" class="form-control debito">
		</div>
		<label class="label-danger-text">Saldo por pagar</label>
	</div>

	<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="input-left-addon" disabled value="{{detalle.proveedor.credito | currency ''}}" class="form-control debito">
		</div>
		<label class="label-success-text">Crédito a favor</label>
	</div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

	<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<label for="fecha_desde">Fecha de factura <span required="" aria-required="true">*</span></label>
		<div class="input-group">
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			<input type="text" name="campo[fecha_factura]" class="form-control" data-rule-required="true" v-model="detalle.fecha_factura" disabled>
		</div>
		<label id="fecha_desde-error" class="error" for="fecha_desde"></label>
	</div>

	<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<label for="fecha">Fecha de nota de cr&eacute;dito de proveedor <span required="" aria-required="true">*</span></label>
		<div class="input-group">
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			<input type="text" name="campo[fecha]" class="form-control" data-rule-required="true" v-datepicker="detalle.fecha" :config="config.datepicker2" :disabled="config.disableDetalle">
		</div>
		<label id="fecha-error" class="error" for="fecha"></label>
	</div>

	<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
		<label for="centro_contable_id">Centro Contable <span required="" aria-required="true">*</span></label>
		<select name="campo[centro_contable_id]" class="form-control" data-rule-required="true" v-select2="detalle.centro_contable_id" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="centro_contable.centro_contable_id" v-for="centro_contable in catalogos.centros_contables">{{centro_contable.nombre}}</option>
            </select>
		<label id="item_precio_id-error" class="error" for="item_precio_id"></label>
	</div>

	<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
		<label>Creado por <span required="" aria-required="true">*</span></label>
		<select name="campo[creado_por]" class="form-control" data-rule-required="true" v-select2="detalle.creado_por" :config="config.select2" :disabled="true">
                <option value="">Seleccione</option>
                <option :value="usuario.id" v-for="usuario in catalogos.usuarios">{{{ usuario.nombre +" "+ usuario.apellido}}}</option>
            </select>
		<label id="vendedor-error" class="error" for="vendedor"></label>
	</div>

	<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 " style="clear:both;">
		<label>No. de nota del proveedor <span required="" aria-required="true">*</span></label>
		<input type="text" data-rule-required="true" name="campo[no_nota_credito]" class="form-control no_nota_credito" v-model="detalle.no_nota_credito" :disabled="config.disableDetalle">
		<label id="no_nota_credito-error" class="error" for="no_nota_credito"></label>
	</div>

	<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
		<label>Estado <span required="" aria-required="true">*</span></label>
		<select name="campo[estado]" class="form-control" data-rule-required="true" v-select2="detalle.estado" :config="config.select2" :disabled=" config.vista == 'crear' || (config.vista != 'crear' && estado_actual == 'anulado' )">
                <option value="">Seleccione</option>
                <option :value="estado.etiqueta" v-for="estado in getEstados">{{estado.valor}}</option>
            </select>
		<label id="estado-error" class="error" for="estado"></label>
	</div>

	<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" v-show="empezable.id !== ''">
		<label for="saldo_factura">Saldo de la factura</label>
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="input" disabled :value="detalle.saldo_factura | currency ''" class="form-control">
		</div>
	</div>


</div>
<div class="row" v-if="detalle.listas_loader">
	<div class="col-md-12 text-center"><i class="fa fa-spin fa-cog"></i> Obteniendo items</div>
</div>
</template>

<script>
export default {

	props: {

		config: Object,
		detalle: Object,
		catalogos: Object,
		empezable: Object

	},

	data: function () {
		var context = this;

		return {

			select2proveedor: {
				catalogo: function (data) {
					context.catalogos.proveedores = JSON.parse(JSON.stringify(data));
				},
				ajax: {
					url: function (params) {
						return phost() + 'proveedores/ajax-get-proveedores';
					},
					data: function (params) {
						return {
							q: params.term
						}
					},
					processResults: function (data) {
						context.select2proveedor.catalogo(data);
						return {
							results: data
						};
					}
				}
			},
			estado_actual: this.detalle.estado
		};

	},

	watch: {

		'empezable.id': function (val, oldVal) {
			var context = this;
			var datos = $.extend({
				erptkn: tkn
			}, {
				factura_id: val
			});

			if (val === '') return;

			this.$http.post({
				url: window.phost() + "facturas_compras/ajax_get_factura_balance",
				method: 'POST',
				data: datos
			}).then(function (response) {

				if (_.has(response.data, 'session')) {
					window.location.assign(window.phost());
					return;
				}
				if (!_.isEmpty(response.data)) {

					context.detalle.saldo_factura = response.data.saldo;
					context.$parent.$broadcast('eCalculate');

				}
			});
		},
		'detalle.proveedor_id': function (val, oldVal) {

            var context = this;
            var datos = $.extend({erptkn: window.tkn}, {proveedor_id: val});
            if (val == null || val == '') {
                context.detalle.proveedor.saldo_pendiente = 0;
				context.detalle.proveedor.credito = 0;
				return;
			}

			context.$http.post({
				url: window.phost() + "proveedores/ajax_get_proveedor_pago",
				method: 'POST',
				data: datos
			}).then(function (response) {
                if (!_.isEmpty(response.data)) {
                    context.detalle.proveedor.saldo_pendiente = response.data.saldo;
    				context.detalle.proveedor.credito = response.data.credito;
				}
			});

		}

	},

	computed: {

		getEstados: function () {

			var context = this;
			if (context.config.vista == 'ver') {
				var nota_debito = JSON.parse(JSON.stringify(window.nota_debito));

				if (nota_debito.estado == 'aprobado') {

					return _.filter(context.catalogos.estados, function (estado) {
						return estado.etiqueta == 'aprobado' || estado.etiqueta == 'anulado';
					});
				} else if (nota_debito.estado == 'anulado') {
					return _.filter(context.catalogos.estados, function (estado) {
						return estado.etiqueta == 'anulado';
					});
				}
			}
			return context.catalogos.estados;

		}

	},
}
</script>
