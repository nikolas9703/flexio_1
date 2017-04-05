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

							<div class="row" v-for="(index, asignado) in detalle.agentesCliente">
								<div class="col-xs-2 col-sm-2 col-md-1 col-lg-1">
									<button type="button" class="btn btn-default btn-block" v-if="index == 0" @click.stop="addRow()"><i class="fa fa-plus"></i></button>
									<button type="button" class="btn btn-default btn-block" v-if="index != 0" @click.stop="removeRow(asignado)"><i class="fa fa-trash"></i>
									</button>
									<!--<input type="hidden" name="agentesCliente[{{index}}]" v-model="asignado.id">-->
								</div>

								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<select class="form-control selAgente" id="selAgente_{{index}}" name="agentesCliente[{{index}}]" v-select2="asignado.agente_id" :config="config.select2" @change="verificaAgentes()">
										<option value="">Seleccione</option>
										<option :value="row.id" v-for="row in catalogos.agentes" v-html="row.nombre"></option>
									</select>
								</div>

								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<input type="text" class="form-control" value="{{asignado.identificacion}}" id="identificacion_agt_{{index}}" disabled="disabled">
								</div>

								<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
									<input type="text" class="form-control" value="{{asignado.no_identificacion}}" id="no_identificacion_agt_{{index}}" disabled="disabled">
								</div>

								<div id="agentes_ramos" class="col-xs-12 col-sm-12 col-md-5 col-lg-5 agt_ram_{{index}} agtramosdiv">
									<div class="row ramosagentes_{{index}}" v-for="(index1, agt_ramo) in detalle.agentesRamoCliente[index]" id="ramosagentes_{{index}}_{{index1}}" >
										<div class="col-xs-6 col-sm-6 col-md-5 col-lg-5">
											<select class="form-control reini_ramo select_ramo_{{index}} iniramo" id="ramos_{{index}}_{{index1}}" name="ramos_agentes[{{index}}][{{index1}}]" multiple v-select2="agt_ramo.ramos" :config="config.select2" :data-rule-required="agt_ramo.requerido">
												<option value="todos">Todos</option>
												<option :value="row.id" v-for="row in catalogos.ramos" v-html="row.nombre"></option>
											</select>
										</div>
										<input type="hidden" class="hidden_ramos select_ramo_h_{{index}}" id="ramos_h_{{index}}_{{index1}}" name="ramos_agentes_h[{{index}}][{{index1}}]">
										<div class="col-xs-6 col-sm-6 col-md-5 col-lg-5">
											<input type="text" name="porcentajes_agentes[{{index}}][{{index1}}]" class="form-control iniparti input_participacion_{{index}}" id="porcentajes_{{index}}_{{index1}}" value="{{agt_ramo.porcentajes}}" :data-rule-required="agt_ramo.requerido">
										</div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
											<button type="button" class="btn btn-default btn-block" v-if="index1 == 0" @click.stop="addRowRamos(index)"><i class="fa fa-plus"></i></button>
											<button type="button" class="btn btn-default btn-block" v-if="index1 != 0" @click.stop="removeRowRamos(index,agt_ramo)"><i class="fa fa-trash"></i>
											</button>
										</div>
									</div>
									<!--<div class="row divramo ramosagentes_{{$index}}" id="ramosagentes_{{$index}}_0">
										<div class="col-xs-6 col-sm-6 col-md-5 col-lg-5">
											<select class="form-control reini_ramo select_ramo_{{$index}} iniramo" id="ramos_{{$index}}_0" name="ramos_agentes[{{$index}}][0]" multiple>
												<option value="todos">Todos</option>
												<option :value="row.id" v-for="row in catalogos.ramos" v-html="row.nombre"></option>										
											</select>
										</div>
										<input type="hidden" class="hidden_ramos select_ramo_h_{{$index}}" id="ramos_h_{{$index}}_0" name="ramos_agentes_h[{{$index}}][0]">
										<div class="col-xs-6 col-sm-6 col-md-5 col-lg-5">
											<input type="text" name="porcentajes_agentes[{{$index}}][0]" class="form-control iniparti input_participacion_{{$index}}" id="porcentajes_{{$index}}_0">
										</div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
											<button type="button" class="btn btn-default btn-block" @click="agregar_ramos($index)"><i class="fa fa-plus"></i></button>
										</div>
									</div>-->
									<div id="insertacampo">									
									</div>
								</div>								
							</div>
							
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

				var x = this.detalle.agentesCliente.push({agente_id:'', id:''});
				var y = this.detalle.agentesRamoCliente.push([[{ ramos : '', porcentajes : '', id : '' }]]);

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
					$(".agtramosdiv").each(function(){
						$(this).find(".select2-container").each(function(){
							$(this).find(".select2-selection").css({"overflow": "scroll", "height": "100px", "overflow-x": "hidden"}); 
						});
					});



				}, 50);
				
			},
			removeRow: function(row){
				this.detalle.agentesCliente.$remove(row);

				/*setTimeout(function(){ 
					$(".reini_ramo").each(function(){
						$(this).addClass("select2-hidden-accessible");
					});

					//Cambia indices de Selector Ramos despues de borrar toda la fila
					var anterior="";
					var indexant = 0;
					$(".iniramo").each(function(){
						if (anterior!="") {
							var id = $(this).attr("id");
							var ex = id.split("_");
							var y = ex[1];
							if (indexant != ex[1]) {
								if (ex[2] != 0 && ex[2] != "0") {
									$(this).attr("id", "ramos_"+indexant+"_"+ex[2]);
									$(this).attr("name", "ramos_agentes["+indexant+"]["+ex[2]+"]");					
									$(this).removeClass("select_ramo_"+ex[1]+"");
									$(this).addClass("select_ramo_"+indexant+"");	
									y = indexant;
								}
							}
							indexant = y;
						}else{
							anterior = "entro";
						}				
					});

					//Cambia indices de Participacion despues de borrar toda la fila
					var anterior="";
					var indexant = 0;
					$(".iniparti").each(function(){
						if (anterior!="") {
							var id = $(this).attr("id");
							var ex = id.split("_");
							var y = ex[1];
							if (indexant != ex[1]) {
								if (ex[2] != 0 && ex[2] != "0") {
									$(this).attr("id", "porcentajes_"+indexant+"_"+ex[2]);
									$(this).attr("name", "porcentajes_agentes["+indexant+"]["+ex[2]+"]");				
									$(this).removeClass("input_participacion_"+ex[1]+"");
									$(this).addClass("input_participacion_"+indexant+"");	
									y = indexant;
								}
							}
							indexant = y;
						}else{
							anterior = "entro";
						}				
					});

					//Cambia indices de Participacion despues de borrar toda la fila
					var anterior="";
					var indexant = 0;
					$(".hidden_ramos").each(function(){
						if (anterior!="") {
							var id = $(this).attr("id");
							var ex = id.split("_");
							var y = ex[2];
							if (indexant != ex[2]) {
								if (ex[3] != 0 && ex[3] != "0") {
									$(this).attr("id", "ramos_h_"+indexant+"_"+ex[3]);
									$(this).attr("name", "ramos_agentes_h["+indexant+"]["+ex[3]+"]");				
									$(this).removeClass("select_ramo_h_"+ex[2]+"");
									$(this).addClass("select_ramo_h_"+indexant+"");	
									y = indexant;
								}
							}
							indexant = y;
						}else{
							anterior = "entro";
						}				
					});

					//Cambia indices de Participacion despues de borrar toda la fila
					var anterior="";
					var indexant = 0;
					$(".divramo").each(function(){
						if (anterior!="") {
							var id = $(this).attr("id");
							var ex = id.split("_");
							var y = ex[1];
							if (indexant != ex[1]) {
								if (ex[2] != 0 && ex[2] != "0") {
									$(this).attr("id", "ramosagentes_"+indexant+"_"+ex[2]);				
									$(this).removeClass("ramosagentes_"+ex[1]+"");
									$(this).addClass("ramosagentes_"+indexant+"");	
									y = indexant;
								}
							}
							indexant = y;
						}else{
							anterior = "entro";
						}				
					});

				}, 50);*/
			},
			addRowRamos: function(ind){
				console.log(ind);
				this.detalle.agentesRamoCliente[ind].push({ ramos:'', porcentajes:'', id:''});

				setTimeout(function(){ 
					deshabilita2(ind);
					$(".iniramo").select2();
					$(".agtramosdiv").each(function(){
						$(this).find(".select2-container").each(function(){
							$(this).find(".select2-selection").css({"overflow": "scroll", "height": "100px", "overflow-x": "hidden"}); 
						});
					});
				}, 50);				
			},
			removeRowRamos: function(index,row){
				//console.log(row);
				this.detalle.agentesRamoCliente[index].$remove(row);
			},
			agregar_ramos: function(index){

				var x = 0;
				$(".ramosagentes_"+index+"").each(function(){
					$(this).attr("id", "ramosagentes_"+index+"_"+x);
					x = x + 1;
				});
				
				$('.agt_ram_' + index).find("#insertacampo").before('<div class="row divramo ramosagentes_'+index+'" id="ramosagentes_'+index+'_'+x+'"><div class="col-xs-6 col-sm-6 col-md-5 col-lg-5"><select class="form-control select_ramo_'+index+' iniramo" id="ramos_'+index+'_'+x+'" name="ramos_agentes['+index+']['+x+']" multiple></select></div><input class="hidden_ramos select_ramo_h_'+index+'" type="hidden" id="ramos_h_'+index+'_'+x+'" name="ramos_agentes_h['+index+']['+x+']"><div class="col-xs-6 col-sm-6 col-md-5 col-lg-5"><input type="text" name="porcentajes_agentes['+index+']['+x+']" class="form-control iniparti input_participacion_'+index+'" id="porcentajes_'+index+'_'+x+'"></div><div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><button type="button" class="btn btn-default btn-block btnramos_'+index+'" onclick="remover_ramos('+index+', '+x+')"><i class="fa fa-trash"></i></button></div></div>');				

				$("#ramos_0_0 option").clone().appendTo("#ramos_"+index+"_"+x);

				deshabilita2(index);				
				$(".iniramo").select2();
				$(".agtramosdiv").each(function(){
					$(this).find(".select2-container").each(function(){
						$(this).find(".select2-selection").css({"overflow": "scroll", "height": "100px", "overflow-x": "hidden"}); 
					});
				});

			},
			verificaAgentes: function(){
				
				var num = [];
						$(".selAgente").each(function(){
							if ($(this).val() != "" && $(this).val() != null) {
								$.each( $(this).val(), function(key, value){
									num.push(value);                
								});
							}     
						});

						console.log(num);

						$(".selAgente").each(function(){
							var valor = $(this).val();
							$("option", this).each(function(){
								$(this).removeAttr("disabled");
								if ($.inArray($(this).attr('value'), num)>=0) {   
		                			//console.log($(this).attr('value')); 
		                			if ($.inArray($(this).attr('value'), valor)<0) {
		                				$(this).attr("disabled", "disabled");
		                				var y = $(this).attr('data-index');
		                			}            
		                		}
		                	});        
						});
				
			}

		}

	}

</script>
