<template>
<div class="row" id="info_pago">
	<div class="col-md-12">
		<div class="ibox float-e-margins border-bottom">
			<div class="ibox-title">
				<h5><i class="fa fa-credit-card"></i>&nbsp;Información de pago <small id="info_pago_small">Impuestos, Lista de precios &amp; Términos</small></h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-md-3">
						<label>Exonerado de impuesto</label>
						<div class="input-group">
							<span class="input-group-addon">
                                <input type="checkbox" v-model="config.exonerado" @change="cleanExonerado()">
							</span>
							<input type="text" name="campo[exonerado_impuesto]" class="form-control" placeholder="Ingrese número de certificado" v-model="detalle.exonerado_impuesto" :disabled="!config.exonerado">
						</div>
					</div>
					<div class="col-md-3">
						<label>Retiene impuestos</label>
						<select class="form-control" name="campo[retiene_impuesto]" v-select2="detalle.retiene_impuesto" :config="config.select2">
                            <option value="no">No</option>
							<option value="si">Sí</option>
				        </select>
					</div>
					<div class="col-md-3" id="div_lista_precio_venta">
						<label>Lista de precio de ventas</label>
						<select class="form-control" name="campo[lista_precio_venta_id]" v-select2="detalle.lista_precio_venta_id" :config="config.select2">
                            <option value="">Seleccione</option>
                            <option :value="row.id" v-for="row in catalogos.lista_precios_venta" v-html="row.nombre"></option>
						</select>
					</div>
					<div class="col-md-3" id="div_lista_precio_alquiler">
						<label>Lista de precio de alquiler</label>
						<select class="form-control" name="campo[lista_precio_alquiler_id]" v-select2="detalle.lista_precio_alquiler_id" :config="config.select2">
                            <option value="">Seleccione</option>
                            <option :value="row.id" v-for="row in catalogos.lista_precios_alquiler" v-html="row.nombre"></option>
						</select>
					</div>
				</div>

				<br>

				<div class="row" id="div_lista_terminos_pago">
					<div class="col-md-3">
						<label>Términos de pago</label>
						<select class="form-control" name="campo[termino_pago]" v-select2="detalle.termino_pago" :config="config.select2">
                            <option value="">Seleccione</option>
                            <option :value="row.valor" v-for="row in catalogos.terminos_pago" v-html="row.etiqueta"></option>
						</select>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
</template>

<script>
export default {

	props: {

		config: Object,
		detalle: Object,
		catalogos: Object

	},

	data: function () {

		return {};

	},

    methods:{

        cleanExonerado: function(){

            var context = this;
            context.detalle.exonerado_impuesto = '';

        }

    },

}
</script>
