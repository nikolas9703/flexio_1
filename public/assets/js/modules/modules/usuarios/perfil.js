var moduloPerfil = (function(){
	
	return{
		getOportinudades:function(parametros){
			var parametos = parametros;
			return $.ajax({
				 type: "post",
					url: phost() + 'usuarios/ajax-oportunidades',
					datatype:'json',
					cache: false,				
					data:parametos,
					success:function(data){
						var resultados = $.parseJSON(data);
						var ganados = resultados.results[0][0];
						var perdidos = resultados.results[0][1];
						var nuevos = resultados.results[0][2];
						
						$("#oportunidad_ganada").html(ganados.ganados +" Oportunidades");
						$("#ganado_porcentaje").html("+"+ganados.porcentaje +" %");
						$("#ganado_total").html("$ "+ganados.total_ganados);
						$("#ganado_diferencia").html("+ $"+ganados.diferencia);
						
						
						$("#oportunidad_perdida").html(perdidos.perdidos +" Oportunidades");
						$("#perdido_porcentaje").html("-"+perdidos.porcentaje +" %");
						$("#perdido_total").html("$ "+perdidos.total_perdidos);
						$("#perdida_diferencia").html("+ $"+perdidos.diferencia);
						
						
						$("#oportunidad_nueva").html(nuevos.nuevas +" Oportunidades");
						$("#nuevo_porcentaje").html("+"+nuevos.porcentaje +" %");
						$("#nuevo_total").html("$ "+nuevos.total_nuevas);
						$("#nueva_diferencia").html("+ $"+nuevos.diferencia);
					}
			 });
			
		},
		getActividades:function(parametros){
			return $.ajax({
				 type: "post",
					url: phost() + 'usuarios/ajax-actividades',
					datatype:'json',
					cache: false,				
					data:parametros,
					success:function(data){
						var resultados = $.parseJSON(data);
						$("#actividad_agregadas").empty();
						$("#actividad_completadas").empty();
						_.forEach(resultados.results[0],function(value, i){
							if(value.completada == 0){
								$("#actividad_agregadas").append('<span class="form-group col-md-12 col-sm-12 col-xs-12  label-actividad-tareas ' + ((value.actividad == 0)? "gray-bg1": "green-bg") +'"><i class="icon-size fa '+ value.icono +'"></i> '+ value.actividad +' '+ value.nombre +'</span>');
							}else{
								$("#actividad_completadas").append('<span class="form-group col-md-12 col-sm-12 col-xs-12  label-actividad-tareas ' + ((value.actividad == 0)? "gray-bg1": "green-bg") +'"><i class="icon-size fa '+ value.icono +'"></i> '+ value.actividad +' '+ value.nombre +'</span>');
							}
						});
					}
			 });
		},
		getAgentes:function(parametros){
			var parametos = parametros;
			return $.ajax({
				    type: "post",					
					url: phost() + 'usuarios/ajax-polular-tabla',
					datatype:'json',
					cache: false,				
					data:parametros,
					success:function(data){
						
						var resultados = $.parseJSON(data);
						$("#usuariosGrid").jqGrid({
							url: phost() + 'usuarios/ajax-tabla-agentes',
						   	colNames:resultados.colName,
					  		  colModel: resultados.colModel,
					  	 		height: "auto",
					  			autowidth: true,
					  			datatype: 'json',
					  			mtype: "POST",
					  		   	postData:parametros,
					  			loadtext: '<p>Cargando...',
					  			hoverrows: false,
					  		    viewrecords: true,
					  		   // refresh: true,
					  		    gridview: true,
					  		    multiselect: false,
					  		    footerrow: true,
				                loadComplete: function () {
				                    var colTotal = $("#usuariosGrid").jqGrid('getCol', 'Total', false, 'sum');
				                    var colLlamada = $("#usuariosGrid").jqGrid('getCol', 'Llamada', false, 'sum');
				                    var colReunion = $("#usuariosGrid").jqGrid('getCol', 'Reuni\u00f3n', false, 'sum');
				                    var colTarea = $("#usuariosGrid").jqGrid('getCol', 'Tarea', false, 'sum');
				                    var colPresentacion = $("#usuariosGrid").jqGrid('getCol', 'Presentaci\u00f3n', false, 'sum');
				                    $("#usuariosGrid").jqGrid('footerData', 'set', {Agentes: 'TOTALES', Total: colTotal, Llamada:colLlamada, Reuni\u00f3n:colReunion, Tarea:colTarea, Presentaci\u00f3n:colPresentacion});
				                   
				                    $("span.pie").peity("pie", {
				                        fill: ['#1ab394', '#d7d7d7', '#ffffff'],
				                        width:32,
				                        height:32
				                    });
				                }
						});
						
	
					},complete:function(data){
						$("#usuariosGrid").jqGrid('setGridParam', { datatype: 'json',postData:parametros }).trigger('reloadGrid');	
					}
			 });
	
	}
		};
	
})();
//javasript usando el patron de dise�o modular