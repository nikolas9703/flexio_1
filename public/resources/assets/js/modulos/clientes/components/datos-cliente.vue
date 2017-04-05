<template>
<div class="row">

	<div class="col-md-12">
		<!-- Form Section Start -->
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><i class="fa fa-info-circle"></i>&nbsp;Datos del cliente</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox">
				<div class="ibox-content">
					<div class="row">
						<div class="col-md-6">
							<label>Nombre del cliente <span required="" aria-required="true">*</span></label>
							<input type="text" class="form-control" name="campo[nombre]" aria-required="true" data-rule-required="true" v-model="detalle.nombre">
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 " v-show="config.vista!=='crear'">
							<label></label>
                            <div class="input-group">
                            	<span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                <input type="input-left-addon" disabled="" value="{{detalle.saldo | currency ''}}" class="form-control">
                            </div>
                            <br>
                            <span class="btn btn-danger btn-block">Saldo por cobrar</span>
                        </div>
						<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 " v-show="config.vista!=='crear'">
							<label></label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-dollar"></i></span>
								<input type="input-left-addon" disabled="" value="{{detalle.credito | currency ''}}" class="form-control">
							</div>
							<br>
							<span class="btn btn-block" style="background:#61BD4F;color: white;">Crédito a favor</span>
						</div>
					</div>
					<br>
					<!-- ID Component Start -->
                    <identificacion :config.sync="config" :detalle.sync="detalle"></identificacion>
					<!-- ID Component End -->
					<br>
					<div class="row">
						<telefonos-cliente :config.sync="config" :detalle.sync="detalle"></telefonos-cliente>
                        <correos-cliente :config.sync="config" :detalle.sync="detalle"></correos-cliente>
					</div>

					<br>

					<div class="row">
						<div class="col-md-3">
							<label>Toma de contacto <span required="" aria-required="true">*</span></label>
							<select class="form-control" name="campo[toma_contacto_id]" aria-required="true" data-rule-required="true" v-select2="detalle.toma_contacto_id" :config="config.select2">
								<option value="">Seleccione</option>
								<option :value="row.id" v-for="row in catalogos.tomas_contacto" v-html="row.nombre"></option>
                            </select>
						</div>
						<div class="col-md-3">
							<label>Tipo de cliente</label>
							<select class="form-control" name="campo[tipo]" v-select2="detalle.tipo" :config="config.select2">
                            	<option value="">Seleccione</option>
								<option :value="row.id" v-for="row in catalogos.tipos_cliente" v-html="row.nombre"></option>
                            </select>
						</div>
						<div class="col-md-3">
							<label>Categoría de cliente</label>
							<select class="form-control" name="campo[categoria]" v-select2="detalle.categoria" :config="config.select2">
                            	<option value="">Seleccione</option>
								<option :value="row.id" v-for="row in catalogos.categorias_cliente" v-html="row.nombre"></option>
                            </select>
						</div>
						<div class="col-md-3" id="div_limite_ventas">
							<label>Límite de crédito de ventas</label>
							<div class="input-group m-b">
								<span class="input-group-addon">$</span>
								<input type="text" class="form-control" name="campo[credito_limite]" v-model="detalle.credito_limite | currencyDisplay">
							</div>
						</div>
					</div>

					<br>

					<div class="row">
						<div class="col-md-6">
							<label>Observaciones</label>
							<textarea  class="form-control" name="campo[comentario]" v-model="detalle.comentario"></textarea>
						</div>
						<div class="col-md-3">
							<label>Estado <span required="" aria-required="true">*</span></label>
							<select class="form-control" name="campo[estado]" id="campo_estado" aria-required="true" data-rule-required="true" v-select2="detalle.estado" :config="config.select2">
                            	<option value="">Seleccione</option>
								<option :value="row.etiqueta" v-for="row in catalogos.estados_cliente" v-html="row.valor"></option>
                            </select>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<!-- Form Section End -->
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

    components:{

        'identificacion': require('./../../../vue/components/identificacion.vue'),
        'telefonos-cliente': require('./telefonos-cliente.vue'),
        'correos-cliente': require('./correos-cliente.vue'),

    }

}
</script>
