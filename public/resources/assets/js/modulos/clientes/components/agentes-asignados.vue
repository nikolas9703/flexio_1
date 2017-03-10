<template>
	<div class="row">
		<div class="col-md-12">
			<div class="ibox float-e-margins border-bottom">
				<div class="ibox-title">
					<h5><i class="fa fa-user-plus"></i>&nbsp;Agentes asignados al cliente <small>Líneas de negocios o Centros contables</small></h5>
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
										<th width="1%"></th>	
										<th width="14%">Nombre</th>
<<<<<<< HEAD
										<th width="14%">Identificacio&acute;n</th>
										<th width="14%">N°.Pasaporte</th>
										<th width="14%">Ramo</th>
										<th width="14%">Partcipacio&acute;n</th>	
=======
										<th width="14%">Identificaci&oacute;n</th>
										<th width="14%">N°.Pasaporte</th>
										<th width="14%">Ramo</th>
										<th width="14%">Participaci&oacute;n</th>	
>>>>>>> 0ef8535b3227f3f488cc644c76e2591000f9a362
										<th width="1%"></th>
									</tr>
								</thead>
								<tbody>
<<<<<<< HEAD
									<tr v-for="asignado in detalle.asignados">
=======
									<tr v-for="asignado in detalle.agentesCliente">
>>>>>>> 0ef8535b3227f3f488cc644c76e2591000f9a362
										<td>
											<button type="button" class="btn btn-default btn-block" v-if="$index == 0" @click.stop="addRow()"><i class="fa fa-plus"></i></button>
											<button type="button" class="btn btn-default btn-block" v-if="$index != 0" @click.stop="removeRow(asignado)"><i class="fa fa-trash"></i>
											</button>
<<<<<<< HEAD
											<input type="hidden" name="asignados[{{$index}}][id]" v-model="asignado.id">
										</td>
										<td>
											<input type="text" name="asignados[{{$index}}][linea_negocio]" class="form-control" v-model="asignado.linea_negocio">
										</td>

										<td>
											<select class="form-control" name="asignados[{{$index}}][usuario_id]" v-select2="asignado.usuario_id" :config="config.select2">
=======
											<input type="hidden" name="agentesCliente[{{$index}}][id]" v-model="asignado.id">
										</td>
										<td>
											<input type="text" name="agentesCliente[{{$index}}][linea_negocio]" class="form-control" v-model="asignado.linea_negocio">
										</td>

										<td>
											<select class="form-control" name="agentesCliente[{{$index}}][usuario_id]" v-select2="asignado.usuario_id" :config="config.select2">
>>>>>>> 0ef8535b3227f3f488cc644c76e2591000f9a362
												<option value="">Seleccione</option>
												<option :value="row.id" v-for="row in catalogos.usuarios" v-html="row.nombre"></option>
											</select>
										</td>
										<td>
<<<<<<< HEAD
											<input type="text" name="asignados[{{$index}}][linea_negocio]" class="form-control" v-model="asignado.linea_negocio">
										</td>
										<td>
											<select class="form-control" name="asignados[{{$index}}][usuario_id]" v-select2="asignado.usuario_id" :config="config.select2">
=======
											<input type="text" name="agentesCliente[{{$index}}][linea_negocio]" class="form-control" v-model="asignado.linea_negocio">
										</td>
										<td>
											<select class="form-control" name="agentesCliente[{{$index}}][usuario_id]" v-select2="asignado.usuario_id" :config="config.select2">
>>>>>>> 0ef8535b3227f3f488cc644c76e2591000f9a362
												<option value="">Seleccione</option>
												<option :value="row.id" v-for="row in catalogos.usuarios" v-html="row.nombre"></option>
											</select>
										</td>
										<td>
<<<<<<< HEAD
											<select class="form-control" name="asignados[{{$index}}][usuario_id]" v-select2="asignado.usuario_id" :config="config.select2">
												<option value="">Seleccione</option>
												<option :value="row.id" v-for="row in catalogos.usuarios" v-html="row.nombre"></option>
											</select>
										</td>
										<td>
											<input type="text" name="asignados[{{$index}}][linea_negocio]" class="form-control" v-model="asignado.linea_negocio">
=======
											<input type="text" name="agentesCliente[{{$index}}][linea_negocio]" class="form-control" v-model="asignado.linea_negocio">
>>>>>>> 0ef8535b3227f3f488cc644c76e2591000f9a362
										</td>
										<td>
											<button type="button" class="btn btn-default btn-block" v-if="$index == 0" @click.stop="addRow()"><i class="fa fa-plus"></i></button>
											<button type="button" class="btn btn-default btn-block" v-if="$index != 0" @click.stop="removeRow(asignado)"><i class="fa fa-trash"></i>
											</button>
<<<<<<< HEAD
											<input type="hidden" name="asignados[{{$index}}][id]" v-model="asignado.id">
=======
											<input type="hidden" name="agentesCliente[{{$index}}][id]" v-model="asignado.id">
>>>>>>> 0ef8535b3227f3f488cc644c76e2591000f9a362
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
<<<<<<< HEAD
				this.detalle.asignados.push({usuario_id:'', linea_negocio:'', id:''});
			},
			removeRow: function(row){
				this.detalle.asignados.$remove(row);
=======
				this.detalle.agentesCliente.push({usuario_id:'', linea_negocio:'', id:''});
			},
			removeRow: function(row){
				this.detalle.agentesCliente.$remove(row);
>>>>>>> 0ef8535b3227f3f488cc644c76e2591000f9a362
			}

		}

	}
</script>
