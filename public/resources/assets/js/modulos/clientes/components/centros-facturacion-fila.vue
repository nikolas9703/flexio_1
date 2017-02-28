<template>
	<tr>
		<td>
			<input type="text" name="centro_facturacion[{{parent_index}}][nombre]" class="form-control" aria-required="true" data-rule-required="true" v-model="centro_facturacion.nombre">
		</td>
		<td>
			<select class="form-control" name="centro_facturacion[{{parent_index}}][provincia_id]" v-select2="centro_facturacion.provincia_id" :config="config.select2">
				<option value="">Seleccione</option>
				<option :value="provincia.id" v-for="provincia in catalogos.provincias" v-html="provincia.nombre"></option>
			</select>
		</td>

		<td>
			<select class="form-control" name="centro_facturacion[{{parent_index}}][distrito_id]" v-select2="centro_facturacion.distrito_id" :config="config.select2">
				<option value="">Seleccione</option>
				<option :value="distrito.id" v-for="distrito in getDistritos" v-html="distrito.nombre"></option>
			</select>
		</td>
		<td>
			<select class="form-control" name="centro_facturacion[{{parent_index}}][corregimiento_id]" v-select2="centro_facturacion.corregimiento_id" :config="config.select2">
				<option value="">Seleccione</option>
				<option :value="corregimiento.id" v-for="corregimiento in getCorregimientos" v-html="corregimiento.nombre"></option>
			</select>
		</td>
		<td>
			<input type="text" class="form-control" name="centro_facturacion[{{parent_index}}][direccion]" aria-required="true" data-rule-required="true" v-model="centro_facturacion.direccion">
		</td>
		<td>
			<button type="button" class="btn btn-default btn-block" v-if="parent_index == 0" @click.stop="addRow()" id="boton_centrofact"><i class="fa fa-plus"></i></button>
			<button type="button" class="btn btn-default btn-block" v-if="parent_index != 0" @click.stop="removeRow(centro_facturacion)"><i class="fa fa-trash"></i></button>
			<input type="hidden" name="centro_facturacion[{{parent_index}}][id]" v-model="centro_facturacion.id">
		</td>
	</tr>
</template>

<script>
export default {

	props: {

		config: Object,
		detalle: Object,
		catalogos: Object,
		parent_index: Number,
		centro_facturacion: Object,

	},

	data: function () {

		return {};

	},

	//watch id seleccionado en computed gets

	watch:{

		'centro_facturacion.provincia_id': function(val, oldVal){

			var context = this;
			var aux = _.filter(context.getDistritos, function(distrito){
				return distrito.id == context.centro_facturacion.distrito_id;
			});

			if(!aux.length)
			{
				context.centro_facturacion.distrito_id = '';
			}

		},

		'centro_facturacion.distrito_id': function(val, oldVal){

			var context = this;
			var aux = _.filter(context.getCorregimientos, function(corregimiento){
				return corregimiento.id == context.centro_facturacion.corregimiento_id;
			});

			if(!aux.length)
			{
				context.centro_facturacion.corregimiento_id = '';
			}

		},

	},

	computed:{

		getDistritos: function(){

			var context = this;
			return _.filter(context.catalogos.distritos, function(distrito){
				return distrito.provincia_id == context.centro_facturacion.provincia_id;
			});

		},

		getCorregimientos: function(){

			var context = this;
			return _.filter(context.catalogos.corregimientos, function(corregimiento){
				return corregimiento.distrito_id == context.centro_facturacion.distrito_id;//cambiar cara centro de facturacion a componente
			});

		}

	},

	methods: {

		addRow: function () {
			this.detalle.centros_facturacion.push({
				nombre: '',
				direcion: '',
                provincia_id: '',
                distrito_id: '',
                corregimiento_id: '',
                id:''
			});
		},
		removeRow: function (row) {
			this.detalle.centros_facturacion.$remove(row);
		}

	}

}
</script>
