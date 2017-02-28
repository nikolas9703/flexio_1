<template>

	<div class="col-lg-6">
		<table class="table table-noline">
			<thead>
				<tr>
					<th width="45%" style="font-weight:bold">Teléfono </th>
					<th width="45%"></th>
					<th width="10%">&nbsp;</th>
				</tr>
			</thead>

			<tbody>
				<tr v-for="telefono in detalle.telefonos">
					<td>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-phone"></i></span>
							<input type="text" name="telefonos[{{$index}}][telefono]" class="form-control"  v-model="telefono.telefono">
						</div>
					</td>
					<td>
						<select class="form-control" name="telefonos[{{$index}}][tipo]"  v-select2="telefono.tipo" :config="config.select2">
							<option value="">Seleccione</option>
							<option value="trabajo">Trabajo</option>
							<option value="movil">Móvil</option>
							<option value="fax">Fax</option>
							<option value="residencial">Residencial</option>
							<option value="otro">Otro</option>
						</select>
					</td>
					<td>
						<button type="button" class="btn btn-default btn-block" @click.stop="addRow()" id="boton_telefono" v-if="$index === 0"><i class="fa fa-plus"></i></button>
						<button type="button" class="btn btn-default btn-block" @click.stop="removeRow(telefono)" v-if="$index !== 0"><i class="fa fa-trash"></i></button>
						<input type="hidden" name="telefonos[{{$index}}][id]" v-model="telefono.id">
					</td>
				</tr>
			</tbody>
		</table>
	</div>

</template>

<script>
export default {

	props: {

		config: Object,
		detalle: Object

	},

	data: function () {

		return {};

	},

	methods:{

		addRow: function(){
			this.detalle.telefonos.push({telefono:'',tipo:'', id:''});
		},

		removeRow: function(row){
			this.detalle.telefonos.$remove(row);
		}

	}

}
</script>
