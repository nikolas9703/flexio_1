<template>
<div class="row" id="agentes_otros">
	<div class="col-md-12">
		<div class="ibox float-e-margins border-bottom">
			<div class="ibox-title">
				<h5><i class="fa fa-user-plus"></i>&nbsp;Usuarios asignados al cliente <small>Líneas de negocios o Centros contables</small></h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-lg-12">
						<table class="table table-noline">
							<thead>
								<tr>
									<th width="50%">Línea de negocio o Centro contable</th>
									<th width="50%">Asignado a</th>
									<th width="1%"></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="asignado in detalle.asignados">
									<td>
										<input type="text" name="asignados[{{$index}}][linea_negocio]" class="form-control" v-model="asignado.linea_negocio">
									</td>

									<td>
										<select class="form-control" name="asignados[{{$index}}][usuario_id]" v-select2="asignado.usuario_id" :config="config.select2">
											<option value="">Seleccione</option>
											<option :value="row.id" v-for="row in catalogos.usuarios" v-html="row.nombre"></option>
										</select>
									</td>
									<td>
										<button type="button" class="btn btn-default btn-block" v-if="$index == 0" @click.stop="addRow()"><i class="fa fa-plus"></i></button>
										<button type="button" class="btn btn-default btn-block" v-if="$index != 0" @click.stop="removeRow(asignado)"><i class="fa fa-trash"></i></button>
										<input type="hidden" name="asignados[{{$index}}][id]" v-model="asignado.id">
									</td>
								</tr>
							</tbody>
						</table>
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

	methods: {

		addRow: function(){
			this.detalle.asignados.push({usuario_id:'', linea_negocio:'', id:''});
		},
		removeRow: function(row){
			this.detalle.asignados.$remove(row);
		}

	}

}
</script>
