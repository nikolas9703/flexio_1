 $(function(){
 	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		tablaLiquidacionesGrid.redimensionar();
	});
});
 
 //Modulo Tabla de Cargos
var tablaLiquidacionesGrid = (function(){
 	var url 			= 'configuracion_planilla/ajax-listar-liquidaciones';
 	var grid_id 		= "tablaLiquidacionesGrid";
	var grid_obj 		=  $("#tablaLiquidacionesGrid");
	var opcionesModal 	= $('#opcionesModal');
	var formulario 		= $('#crearLiquidacionForm');
  	
	var botones = {
			opciones: ".viewOptions",
			cancelar: "#cancelarLiquidacionBtn" ,
			guardar: "#guardarFormLiquidacionBtn",
			eliminar: "#confirmarEliminar"
      };
 	
  	
	var tabla = function(){
 		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	   'Id',
		   	   'Tipo de Liquidaci&oacuten',
		   	   'Pagos aplicables',
   	           'Acumulados aplicables',
   	           'Estado',
   	           '',
  			   ''
  			],
		   	colModel:[
		   	    {name:'Id', index:'id',hidden: true  },
		   	    {name:'Nombre', index:'nombre',  sortable:false,  width:25  },
		   	    {name:'Pagos', index:'pagos',  sortable:false,  width:25 },
   				{name:'Acumulados', index:'acumulados', sortable:false,  width:25 },
   				{name:'Estado', index:'estado', sortable:false,  width:10, resizable:false, hidedlg:true},
   				{name:'link', index:'link',  sortable:false, resizable:false, width:10, hidedlg:true, align:"center"},
   				{name:'options', index:'options', hidedlg:true, hidden: true}
 		   	],
			mtype: "POST",
		   	postData: {
  		   		erptkn: tkn
		   	},
			height: "auto",
			autowidth: true,
			rowList: [10, 20,50, 100],
			rowNum: 10,
			page: 1,
			pager: "#"+ grid_id +"Pager",
			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
		    viewrecords: true,
		    refresh: true,
		    gridview: true,
		    multiselect: false,
		    sortname: 'created_at',
		    sortorder: "DESC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {//propiedadesGrid_cb
	 	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
		        $(this).closest("div.ui-jqgrid-view").find("#jqgh_tablaLiquidacionesGrid_cb, #jqgh_tablaLiquidacionesGrid_link").css("text-align", "center");
		    }, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron liquidaciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}
 				$('#'+ grid_id +'Pager_right').empty();
 				
  				$('button[data-nombre="HR"]').prop( "disabled", true );
			},
			  
			  onSelectRow: function(id){
				  $(this).find('tr#'+ id).removeClass('ui-state-highlight');
    		},
 		});
	};
	   var ajax = function(url, parametros){
			
			if(parametros == "" || url == ""){
				return false;
			}
			
			return $.ajax({
				url: phost() + url,
				data: $.extend({erptkn: tkn}, parametros),
				type: "POST",
				dataType: "json",
				cache: false,
			});
		};
	
	var tabla_inicial = function(tablaID){
		
		$('#'+tablaID).find('.agregarFila').tablaDinamica({
 			afterAddRow: function(row){
 				if(tablaID == 'pagos_aplicables')
				{
 					$(formulario).find('select[class="form-control pagos_aplicables_normales"], select[class="form-control deducciones_pagos_normales"]').chosen({width: '100%'}).trigger('chosen:updated');
				}
 				else{
 					$(formulario).find('select[class="form-control deducciones_pagos_acumulados"], select[class="form-control pagos_aplicables_acumulados"]').chosen({width: '100%'}).trigger('chosen:updated');

 				}
			},
			afterDeleteRow: function(){
				
			},
			onClickDeleteBtn: function(tabla_id, row){
				
				
 				var scope = this;
				
					$(row).addClass('alert-warning');
				
					opcionesModal.on('hidden.bs.modal', function (e) {
						$(row).removeClass('alert-warning');
				});
				 
				opcionesModal.find('.modal-title').empty().append('Confirme');
			    opcionesModal.find('.modal-body').empty().append('&#191;Esta seguro que desea eliminar?');
			    opcionesModal.find('.modal-footer').empty()
				.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
				.append(
					$('<button class="btn btn-w-m btn-danger" type="button" />').append('Eliminar').click(function(e){
						
 						e.preventDefault();
						e.returnValue=false;
						e.stopPropagation();
						
						var response = {};
 						var id = $(row).find('input:hidden[name*="[id_registro]"]').val();
  						response = ajax('configuracion_planilla/ajax-eliminar-pago-acumulado', {id: id});
							response.done(function(json){

				            //Check Session
							if( $.isEmptyObject(json.session) == false){
								window.location = phost() + "login?expired";
							}
				            
							//If json object is empty.
							if($.isEmptyObject(json) == true){
								return false;
							}
							
							//Mostrar Mensaje
							if(json.response == "" || json.response == undefined || json.response == 0){
								toastr.error(json.mensaje);
							}else{
								toastr.success(json.mensaje);
							}
							
							//Eliminar fila de la tabla
							scope.deleterow(row);
							recargar();
							//Ocultar Modal
							opcionesModal.modal('hide');
						
							
				        }).fail(function(xhr, textStatus, errorThrown) {
							//mensaje error
							toastr.error('Hubo un error al tratar de eliminar.');
						})
					})
				);
			    opcionesModal.modal('show');
			}
		});
	}
	
	
	var campos = function(){
 
		
		$(formulario).validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
 		});
  		
  		//Init Tabla Dinamica Plugin
 		tabla_inicial("pagos_aplicables");
		tabla_inicial("acumulados_aplicables");
		  
	};
	//Limpiar campos de busqueda
	var limpiarLiquidacion = function(){
		$(formulario).find('input[type="text"]').prop("value", "");
		$(formulario).find('select').prop("value", "");

 		$(formulario).find('table#pagos_aplicables tbody tr:gt(0)').remove();
		$(formulario).find('table#acumulados_aplicables tbody tr:gt(0)').remove();

		$(formulario).find('#pagos_aplicables_normales0').val("");
		$(formulario).find('#deducciones_pagos_normales0').val("");
		
		$(formulario).find('#pagos_aplicables_acumulados0').val("");
		$(formulario).find('#deducciones_pagos_acumulados0').val("");
 
		$(formulario).find('#id_liquidacion').val("0");
		
		$(formulario).find('#tipo_liquidacion, #estado_id, #pagos_aplicables_normales0,#deducciones_pagos_normales0,#pagos_aplicables_acumulados0,#deducciones_pagos_acumulados0').find('option').removeAttr("selected");
   		$(formulario).find('#tipo_liquidacion, #estado_id, #pagos_aplicables_normales0,#deducciones_pagos_normales0,#pagos_aplicables_acumulados0,#deducciones_pagos_acumulados0').chosen({width: '100%'}).trigger('chosen:updated');
		//actualizar_chosen();
		
	};
	
	 
 	//Inicializar Eventos de Botones
	var eventos = function(){
  
	 		//Bnoton de Opciones
			grid_obj.on("click", botones.opciones, function(e){
 				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				
				var id = $(this).attr("data-id");
		 
				var rowINFO = grid_obj.getRowData(id);
	  		    var options = rowINFO["options"];
	  		    
	   	 	    //Init Modal
			    opcionesModal.find('.modal-title').empty().append('Opciones: '+rowINFO['Nombre']);
			    opcionesModal.find('.modal-body').empty().append(options);
			    opcionesModal.find('.modal-footer').empty();
			    opcionesModal.modal('show');
			}); 
			opcionesModal.on("click", ".editarLiquidacionBtn", function(e){
				
  		 		e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				
				$('#id_liquidacion').prop("value", "0");
				var id = $(this).attr("data-id");
 
 				$.ajax({
					url: phost() + 'configuracion_planilla/ajax-get-liquidacion',
					data: formulario.serialize()+"&liquidacion_id="+id,
						type: "POST",
					dataType: "json",
					cache: false,
				}).done(function(json) {
 					//Check Session
					if( $.isEmptyObject(json.session) == false){
						window.location = phost() + "login?expired";
					}
					
 		         	
					$(formulario).find('#id_liquidacion').val(id);
		         	$(formulario).find('#tipo_liquidacion  option[value="'+json.general.tipo_liquidacion+'"]').prop('selected', 'selected');
					$(formulario).find('#tipo_liquidacion').chosen({width: '100%'}).trigger('chosen:updated');
					
					
					$(formulario).find('#estado_id  option[value="'+json.general.estado_id+'"]').prop('selected', 'selected');
					$(formulario).find('#estado_id').chosen({width: '100%'}).trigger('chosen:updated');
					
						agregandoFilasPagos(json.pagos);
						agregandoFilasAcumulados(json.acumulados);
 				});
	
		  	    //Ocultar ventana
		   	    $('#opcionesModal').modal('hide');
		 	});
			
			opcionesModal.on("click", botones.eliminar, function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				 var id_liquidacion = $(this).attr('data-id');
				
			    //Init boton de opciones
				opcionesModal.find('.modal-title').empty().append('Confirme');
				opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea eliminar esta liquidaci&oacute;n?');
				opcionesModal.find('.modal-footer')
					.empty()
					.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
					.append('<button id="eliminarLiquidacion" data-id="'+ id_liquidacion +'" class="btn btn-w-m btn-danger" type="button">Eliminar</button>');
			});	
			
			 	//Opcion: Desactivar Usuario
			opcionesModal.on("click", "#eliminarLiquidacion", function(e){
						e.preventDefault();
					e.returnValue=false;
					e.stopPropagation();
			
					var id_liquidacion = $(this).attr('data-id');
					   	//Guardar Formulario
					$.ajax({
						url: phost() + 'configuracion_planilla/ajax-eliminar-liquidacion',
						data: {
							id: id_liquidacion,
			                erptkn: tkn
						},
						type: "POST",
						dataType: "json",
						cache: false,
					}).done(function(json) {
						
						 //Check Session
						if( $.isEmptyObject(json.session) == false){
							window.location = phost() + "login?expired";
						}
						//If json object is empty.
						if($.isEmptyObject(json) == true){
							return false;
						}
		 				//Mostrar Mensaje
						if(json.response == "" || json.response == undefined || json.response == 0){
							toastr.error(json.mensaje);
						}else{
							toastr.success(json.mensaje);
						}
		 				//Ocultar Modal
						opcionesModal.modal('hide');
						recargar();
					});
			});
			
			$(botones.cancelar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
			 	limpiarLiquidacion();
 			});
			$(botones.guardar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
		 		 
				agregarLiquidacion();
			});
			  
  
 	};
	 
 	
 	 var agregandoFilasAcumulados = function(acumulados){
 		 
 		var tablaID = '#acumulados_aplicables';
		var pagoIDFirst = '#pagos_aplicables_acumulados0';
		var deduccionesIDFirst = '#deducciones_pagos_acumulados0';
		var IdpagoIDFirst = 'id_acumulado0';
		
   		$(tablaID+' tbody tr:gt(0)').remove();
    	$(tablaID).find(deduccionesIDFirst).val("");
   		$(tablaID).find('option[value=""]').remove();
   		
   		$(tablaID).find(deduccionesIDFirst).chosen({width: '100%'}).trigger('chosen:updated');
   		
 		var counter = 0;
 		$.each( acumulados, function( key, value ) {
 				
 				var id = value[0].id;
 				if(counter>0){
	 				var fieldhtml =  $("#acumulados_aplicables_fila0").closest('tr').clone();
	 					$(fieldhtml).find(".agregarFila").remove();
	 					
     		             
	 					$(fieldhtml).find('.chosen-container').remove();
	 					$(fieldhtml).find('.chosen-container-single').remove();
	 					$(fieldhtml).find('label.error').remove();  
	 					
	 					$(fieldhtml).find('select').find('option').removeAttr('selected');
	 			}else{
	 				var fieldhtml = $(tablaID+" tbody tr:first");
    	 		}
 				
 				//Registo de la fila, se usa  para borrar
 				$(fieldhtml).find('input[id="'+IdpagoIDFirst+'"]').val(id);
 				$(fieldhtml).find('input[id="'+IdpagoIDFirst+'"]').attr("name",'liquidacion_acumulado[pago]['+counter+'][id_registro]');
	         	$(fieldhtml).find('input[id="'+IdpagoIDFirst+'"]').attr("id", 'id_acumulado' + counter);
	         	
	         	//Select de los pagos
	         	$(fieldhtml).find(pagoIDFirst +' option[value="'+value[0].tipo_pago_id+'"]').prop('selected', 'selected');
				$(fieldhtml).find(pagoIDFirst).chosen({width: '100%'}).trigger('chosen:updated');
				$(fieldhtml).find(pagoIDFirst).attr("name",'liquidacion_acumulado[pago]['+counter+'][id]');
				$(fieldhtml).find(pagoIDFirst).attr("id", 'pagos_aplicables_acumulados' + counter); //Cambiando el Id a los select*/
					
 				
 				$.each( value, function( key2, value2 ) {
 					$(fieldhtml).find(deduccionesIDFirst+' option[value="'+ value2.deduccion_id +'"]').prop('selected', 'selected');
		 		});
 				//Select de las deducciones
 				$(fieldhtml).find(deduccionesIDFirst).chosen({width: '100%'}).trigger('chosen:updated');
 				$(fieldhtml).find(deduccionesIDFirst).attr("name",'liquidacion_acumulado[pago]['+counter+'][deduccion][]');

				$(fieldhtml).find(deduccionesIDFirst).attr("id", 'deducciones_pagos_acumulados' + counter);
 
				//Cambiando el Id del Row
	       	 	$(fieldhtml).attr("id", "acumulados_aplicables_fila" + counter);
 	         
	       	 	if(counter > 0)
	       	 		$(tablaID+' tr:last').after(fieldhtml);
	       	 	
	       	 	counter++;
  		});
  	 };
 		
  	 
	var agregandoFilasPagos = function(pagos_aplicados){
		
		var tablaID = '#pagos_aplicables';
		var pagoIDFirst = '#pagos_aplicables_normales0';
		var deduccionesIDFirst = '#deducciones_pagos_normales0';
		var IdpagoIDFirst = 'id_pago0';
		
   		$(tablaID+' tbody tr:gt(0)').remove();
    	$(tablaID).find(deduccionesIDFirst).val("");
   		$(tablaID).find('option[value=""]').remove();
   		
   		$(tablaID).find(deduccionesIDFirst).chosen({width: '100%'}).trigger('chosen:updated');
   		
 		var counter = 0;
 		$.each( pagos_aplicados, function( key, value ) {
  				var id = value[0].id;
 				if(counter>0){
	 				var fieldhtml =  $("#pago_aplicable_fila0").closest('tr').clone();
	 					$(fieldhtml).find(".agregarFila").remove();
	 					
     		             
	 					$(fieldhtml).find('.chosen-container').remove();
	 					$(fieldhtml).find('.chosen-container-single').remove();
	 					$(fieldhtml).find('label.error').remove();  
	 					
	 					$(fieldhtml).find('select').find('option').removeAttr('selected');
	 			}else{
	 				var fieldhtml = $(tablaID+" tbody tr:first");
    	 		}
 				
 				//Id de la fila osea del registro, se usa para borrar
 				$(fieldhtml).find('input[id="'+IdpagoIDFirst+'"]').val(id);
 				$(fieldhtml).find('input[id="'+IdpagoIDFirst+'"]').attr("name",'liquidacion_pago[pago]['+counter+'][id_registro]');
	         	$(fieldhtml).find('input[id="'+IdpagoIDFirst+'"]').attr("id", 'id_pago' + counter);
	         	
	         	//Select de los Pagos
	         	$(fieldhtml).find(pagoIDFirst +' option[value="'+value[0].tipo_pago_id+'"]').prop('selected', 'selected');
				$(fieldhtml).find(pagoIDFirst).chosen({width: '100%'}).trigger('chosen:updated');
				$(fieldhtml).find(pagoIDFirst).attr("name",'liquidacion_pago[pago]['+counter+'][id]');
				$(fieldhtml).find(pagoIDFirst).attr("id", 'pagos_aplicables_normales' + counter); //Cambiando el Id a los select*/
 				
	
 				
 				$.each( value, function( key2, value2 ) {
 					$(fieldhtml).find(deduccionesIDFirst+' option[value="'+ value2.deduccion_id +'"]').prop('selected', 'selected');
		 		});
 				//Lista de deducciones de la fila
 				$(fieldhtml).find(deduccionesIDFirst).chosen({width: '100%'}).trigger('chosen:updated');
 				$(fieldhtml).find(deduccionesIDFirst).attr("name",'liquidacion_pago[pago]['+counter+'][deduccion][]');
				$(fieldhtml).find(deduccionesIDFirst).attr("id", 'deducciones_pagos_normales' + counter);
 
				//Cambiando el Id del Row
	       	 	$(fieldhtml).attr("id", "pago_aplicable_fila" + counter);
 	         
	       	 	if(counter > 0)
	       	 		$(tablaID+' tr:last').after(fieldhtml);
	       	 	
	       	 	counter++;
  		});
 		 
	}
	var recargar = function(){
   
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
  				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
	};
  
 	//Buscar cargo en jQgrid
	var agregarLiquidacion = function(){

		if(formulario.validate().form() == true )
		{
			$.ajax({
				url: phost() + 'configuracion_planilla/ajax-crear-liquidacion',
				data: formulario.serialize(),
					type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {
	 
				//Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				if(json.response == true){
						 toastr.success(json.mensaje);
						 limpiarLiquidacion();
						 recargar();
					}else{
					toastr.error(json.mensaje);
				}
			 
				 
			});
			$('#opcionesModal').modal('hide');
		}
		
	};
	return{	    
		init: function() {
			tabla();
 			eventos();
 			campos();
		},
		recargar: function(){
			recargar();
		},
		eventos: function(){
			eventos();
		},
		redimensionar: function(){
			//Al redimensionar ventana
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		}
	};
})();

tablaLiquidacionesGrid.init();





