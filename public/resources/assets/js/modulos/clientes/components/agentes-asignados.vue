<script type="text/javascript">
	
</script>
<template>
	<div class="row" id="agentes_seguros">
		<div class="col-md-12">
			<div class="ibox float-e-margins border-bottom">
				<div class="ibox-title">
					<h5><i class="fa fa-user-plus"></i>&nbsp;Agentes asignados al cliente <!--<small>Líneas de negocios o Centros contables</small>--></h5>
					<div class="ibox-tools">
						<a class="collapse-link" id="coll_agt">
							<i class="fa fa-chevron-up"></i>
						</a>
					</div>
				</div>
				<input type="hidden" name="detalle_unico" id="detalle_unico" value="">
				<div class="ibox-content">
					<div class="row">
						<div class="col-lg-12">

							<div class="row">
								<div class="col-xs-2 col-sm-2 col-md-1 col-lg-1">
									<label class="nombre_doc_titulo" ></label>
								</div> 
								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<label class="nombre_doc_titulo" id="nombre_agente">Agente</label>
								</div>    
								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<label class="nombre_doc_titulo" id="identificacion_agente">Identificación</label>
								</div> 
								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<label class="nombre_doc_titulo" id="no_identificacion_agente">No. Identificación</label>
								</div> 
								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<label class="nombre_doc_titulo" id="ramo_agente">Ramo</label>
								</div> 
								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<label class="nombre_doc_titulo" id="participacion_agente">Participación</label>
								</div> 
								<div class="col-xs-2 col-sm-2 col-md-1 col-lg-1">
									<label class="nombre_doc_titulo" ></label>
								</div>        
							</div>

							<div class="row" v-for="asignado in detalle.agentesCliente">
								<div class="col-xs-2 col-sm-2 col-md-1 col-lg-1">
									<button type="button" class="btn btn-default btn-block" v-if="$index == 0" @click.stop="addRow()"><i class="fa fa-plus"></i></button>
									<button type="button" class="btn btn-default btn-block" v-if="$index != 0" @click.stop="removeRow(asignado)"><i class="fa fa-trash"></i>
									</button>
									<input type="hidden" name="agentesCliente[{{$index}}][id]" v-model="asignado.id">
								</div>

								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<select class="form-control selAgente" id="selAgente_{{$index}}" name="agentesCliente[{{$index}}]" v-select2="asignado.agente_id" :config="config.select2">
										<option value="">Seleccione</option>
										<option :value="row.id" v-for="row in catalogos.agentes" v-html="row.nombre"></option>
									</select>
								</div>

								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<input type="text" class="form-control" id="identificacion_agt_{{$index}}" disabled="disabled">
								</div>

								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<input type="text" class="form-control" id="no_identificacion_agt_{{$index}}" disabled="disabled">
								</div>

								<div id="agentes_ramos" class="col-xs-12 col-sm-12 col-md-5 col-lg-5 agt_ram_{{$index}}">
									<!--<div class="row ramagt_{{$index}}" id="ramagt_{{$index}}" v-for="agt_ramo in detalle.agentesRamoCliente">
										<div class="col-xs-6 col-sm-6 col-md-5 col-lg-5">
											<select class="form-control" name="agentesCliente[{{$index}}][usuario_id]" v-select2="agt_ramo.agente_id" :config="config.select2">
												<option value="">Seleccione</option>
												<option :value="row.id" v-for="row in catalogos.agentes" v-html="row.nombre"></option>
											</select>
										</div>
										<div class="col-xs-6 col-sm-6 col-md-5 col-lg-5">
											<input type="text" name="agentesCliente[{{$index}}][linea_negocio]" class="form-control" v-model="agt_ramo.linea_negocio">
										</div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
											<button type="button" class="btn btn-default btn-block" v-if="$index == 0" @click.stop="addRowRamos()"><i class="fa fa-plus"></i></button>
											<button type="button" class="btn btn-default btn-block" v-if="$index != 0" @click.stop="removeRowRamos(agt_ramo)"><i class="fa fa-trash"></i>
											</button>
											<input type="hidden" name="agentesCliente[{{$index}}][id]" v-model="asignado.id">
										</div>
									</div>-->
									<div class="row ramosagentes_{{$index}}" id="ramosagentes_{{$index}}_0">
										<div class="col-xs-6 col-sm-6 col-md-5 col-lg-5">
											<select class="form-control reini_ramo select_ramo_{{$index}} iniramo" id="ramos_{{$index}}_0" name="ramos_agentes[{{$index}}][0]" multiple>
												<!--<option value="">Seleccione</option>-->
												<option value="todos">Todos</option>
												<option :value="row.id" v-for="row in catalogos.ramos" v-html="row.nombre"></option>											
											</select>
										</div>
										<input type="hidden" class="hidden_ramos select_ramo_h_{{$index}}" id="ramos_h_{{$index}}_0" name="ramos_agentes_h[{{$index}}][0]">
										<div class="col-xs-6 col-sm-6 col-md-5 col-lg-5">
											<input type="text" name="porcentajes_agentes[{{$index}}][0]" class="form-control input_participacion_{{$index}}" id="porcentajes_{{$index}}_0">
										</div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
											<button type="button" class="btn btn-default btn-block" @click="agregar_ramos($index)"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div id="insertacampo">									
									</div>
								</div>								
							</div>

							<!--<table class="table table-noline">
								<thead>
									<tr>
										<th width="1%"></th>	
										<th width="14%">Nombre</th>
										<th width="14%">Identificaci&oacute;n</th>
										<th width="14%">N° Identificaci&oacute;n</th>
										<th width="14%">Ramo</th>
										<th width="14%">Participaci&oacute;n</th>	
										<th width="1%"></th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="asignado in detalle.agentesCliente">
										<td>
											<button type="button" class="btn btn-default btn-block" v-if="$index == 0" @click.stop="addRow()"><i class="fa fa-plus"></i></button>
											<button type="button" class="btn btn-default btn-block" v-if="$index != 0" @click.stop="removeRow(asignado)"><i class="fa fa-trash"></i>
											</button>
											<input type="hidden" name="agentesCliente[{{$index}}][id]" v-model="asignado.id">
										</td>

										<td>
											<select class="form-control" id="selAgente" name="agentesCliente[{{$index}}][agente_id]" v-select2="asignado.agente_id" :config="config.select2">
												<option value="">Seleccione</option>
												<option :value="row.id" v-for="row in catalogos.agentes" v-html="row.nombre"></option>
											</select>
										</td>

										<td>
											<input type="text" class="form-control" id="identificacion_agt" disabled="disabled">
										</td>

										<td>
											<input type="text" class="form-control" id="no_identificacion_agt" disabled="disabled">
										</td>
										<div>
											
										</div>
										<td>
											<select class="form-control" name="agentesCliente[{{$index}}][usuario_id]" v-select2="asignado.usuario_id" :config="config.select2">
												<option value="">Seleccione</option>
												<option :value="row.id" v-for="row in catalogos.usuarios" v-html="row.nombre"></option>
											</select>
										</td>
										<td>
											<input type="text" name="agentesCliente[{{$index}}][linea_negocio]" class="form-control" v-model="asignado.linea_negocio">
										</td>
										<td>
											<button type="button" class="btn btn-default btn-block" v-if="$index == 0" @click.stop="addRow()"><i class="fa fa-plus"></i></button>
											<button type="button" class="btn btn-default btn-block" v-if="$index != 0" @click.stop="removeRow(asignado)"><i class="fa fa-trash"></i>
											</button>
											<input type="hidden" name="agentesCliente[{{$index}}][id]" v-model="asignado.id">
										</td>
									</tr>
								</tbody>
							</table>-->
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
				console.log("entro a detalle");
				//this.detalle.agentesCliente.push({usuario_id:'', linea_negocio:'', id:''});
				var x = this.detalle.agentesCliente.push({agente_id:'', id:''});
				console.log(x);

				setTimeout(function(){ 
					$(".selAgente").on('change', function () {
						var idx = $(this).attr("id");
						var x = idx.split("_");
						var id_agente = $("#selAgente_"+x[1]+"").val();

						if (id_agente != "") {
			                //$('select[name="ramos_agentes['+x[1]+'][0]"]').attr("data-rule-required", true);
			                $('.select_ramo_'+x[1]).each(function(){
			                    $(this).attr("data-rule-required", true);
			                });
			                //$('input[name="porcentajes_agentes['+x[1]+'][0]"]').attr("data-rule-required", true);
			                $('.input_participacion_'+x[1]).each(function(){
			                    $(this).attr("data-rule-required", true);
			                });
			            }else{
			                //$('select[name="ramos_agentes['+x[1]+'][0]"]').removeAttr("data-rule-required");
			                $('.select_ramo_'+x[1]).each(function(){
			                    $(this).removeAttr("data-rule-required");
			                });
			                //$('input[name="porcentajes_agentes['+x[1]+'][0]"]').removeAttr("data-rule-required");
			                $('.input_participacion_'+x[1]).each(function(){
			                    $(this).removeAttr("data-rule-required");
			                });
			            }     

						$("#identificacion_agt_"+x[1]+"").val("");
						$("#no_identificacion_agt_"+x[1]+"").val("");

						$.ajax({
							url: phost() + "clientes/ajax_get_agente",
							type: "POST",
							data: {
								erptkn: tkn,
								agente: id_agente
							},
							dataType: "json",
							success: function (response) {
								if (!_.isEmpty(response)) {
									$("#identificacion_agt_"+x[1]+"").val(response[0].tipo_identificacion);
									$("#no_identificacion_agt_"+x[1]+"").val(response[0].identificacion);
								}
							}
						});
					});

					$('.iniramo').select2();
				}, 50);
				

			},
			removeRow: function(row){
				this.detalle.agentesCliente.$remove(row);

				setTimeout(function(){ 
					$(".reini_ramo").each(function(){
						console.log("entro remove");
						$(this).addClass("select2-hidden-accessible");
					});

					var anterior="";
					$(".iniramo").each(function(){
						console.log("cuenta selector ramos");
					});

				}, 50);
			},
			addRowRamos: function(){
				this.detalle.agentesRamoCliente.push({agente_id:'', ramos:'', porcentajes:'', id:''});
				$(".iniramo").select2();
			},
			removeRowRamos: function(row){
				this.detalle.agentesRamoCliente.$remove(row);
			},
			agregar_ramos: function(index){

				var x = 0;
				$(".ramosagentes_"+index+"").each(function(){
					$(this).attr("id", "ramosagentes_"+index+"_"+x);
					x = x + 1;
				});
				
				$('.agt_ram_' + index).find("#insertacampo").before('<div class="row ramosagentes_'+index+'" id="ramosagentes_'+index+'_'+x+'"><div class="col-xs-6 col-sm-6 col-md-5 col-lg-5"><select class="form-control select_ramo_'+index+' iniramo" id="ramos_'+index+'_'+x+'" name="ramos_agentes['+index+']['+x+']" multiple></select></div><input class="hidden_ramos select_ramo_h_'+index+'" type="hidden" id="ramos_h_'+index+'_'+x+'" name="ramos_agentes_h['+index+']['+x+']"><div class="col-xs-6 col-sm-6 col-md-5 col-lg-5"><input type="text" name="porcentajes_agentes['+index+']['+x+']" class="form-control input_participacion_'+index+'" id="porcentajes_'+index+'_'+x+'"></div><div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><button type="button" class="btn btn-default btn-block btnramos_'+index+'" onclick="remover_ramos('+index+', '+x+')"><i class="fa fa-trash"></i></button></div></div>');				

				$("#ramos_0_0 option").clone().appendTo("#ramos_"+index+"_"+x);

				deshabilita2(index);				
				$(".iniramo").select2();

			}

		}

	}

</script>
