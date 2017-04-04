<template>

	<div class="col-lg-6">
		<table class="table table-noline">
			<thead>
				<tr>
					<th width="45%" style="font-weight:bold">Correo Electrónico <span id="span_correo" required="" aria-required="true">*</span></th>
					<th width="45%"></th>
					<th width="10%">&nbsp;</th>
				</tr>
			</thead>

			<tbody>
				<tr v-for="correo in detalle.correos">
					<td>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
							<input type="text" class="form-control" name="correos[{{$index}}][correo]" aria-required="true" data-rule-required="true" id="correo_cliente" v-model="correo.correo">
						</div>
					</td>
					<td>
						<select class="form-control" name="correos[{{$index}}][tipo]" id="tipo_correo_cliente" aria-required="true" data-rule-required="true" v-select2="correo.tipo" :config="config.select2">
							<option value="">Seleccione</option>
                            <option value="trabajo">Trabajo</option>
                            <option value="personal">Personal</option>
                            <option value="otro">Otro</option>
						</select>
					</td>
					<td>
						<button type="button" class="btn btn-default btn-block" @click.stop="addRow()" v-if="$index === 0"><i class="fa fa-plus"></i></button>
						<button type="button" class="btn btn-default btn-block" @click.stop="removeRow(correo)" v-if="$index !== 0"><i class="fa fa-trash"></i></button>
						<input type="hidden" name="correos[{{$index}}][id]" v-model="correo.id">
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
			this.detalle.correos.push({telefono:'',tipo:'', id:''});
		},

		removeRow: function(row){
			this.detalle.correos.$remove(row);
		}

	}

}
</script>
