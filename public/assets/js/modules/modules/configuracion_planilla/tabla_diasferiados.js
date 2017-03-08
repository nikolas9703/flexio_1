 $(function(){
  	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		feriadosGrid.redimensionar();
	});
});
 
 //Modulo Tabla de Cargos
var feriadosGrid = (function(){
	var original_deduccion = $('#id_deducciones_original').html();
	var original_acumulado = $('#id_acumulados_original').html();
	
 	var url 		= 'configuracion_planilla/ajax-listar-diasferiados';
 	var grid_id 	= "tablaDiasferiadosGrid";
	var grid_obj 	=  $("#tablaDiasferiadosGrid");
	var opcionesModal = $('#opcionesModal');
	var formulario = $('#crearDiaFeriadoForm');
  	
	var botones = {
			opciones: ".viewOptions",
 			guardar: "#guardarFormDiasFeriadosBtn",
 			cancelar: "#cancelarFeriadoBtn" ,
		//	editar: ".editarBeneficioBtn"  
   };
 	
  	
	var tabla = function(){
 		grid_obj.jqGrid({
 			url: phost() + url,
 		   	datatype: "json",
 		   	colNames:[
 					'Nombre',
 					'Fecha Oficial',
 					'Cuenta de pasivo',
 					'Cuenta ID',
 		 			'Horas no laboradas',
 					'Estado',
 		 			'Estado ID',
 					'',
 					'hidden',
 		 			'descripcion',
 		 			'acumulados',
 		 			'deducciones',
 					
 				],
 			colModel:[
 					{name:'Nombre', index:'nombre', width:70 },
 					{name:'Fecha Oficial', index:'fecha_oficial', width:70},
 					{name:'Cuenta Pasivo', index:'cuenta_pasivo', width:70},
 					{name:'Cuenta_pasivo_id',index:'cuenta_pasivo_id',hidedlg:true, hidden: true},
 		 			{name:'Horas', index:'horas_id', width:70},
 					{name:'Estado', index:'estado', width:70 },
 					{name:'Estado_id',index:'estado_id',hidedlg:true, hidden: true},
 		  			{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true,  align:"center"},
 		  			{name:'options', index:'options', hidedlg:true, hidden: true},
 		 			{name:'Descripcion',index:'descripcion',hidedlg:true, hidden: true},
 					{name:'acumulados', index:'acumulados',hidedlg:true, hidden: true},
 		 			{name:'deducciones',index:'deducciones',hidedlg:true, hidden: true},
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
 			pager: "#tablaDiasferiadosGridPager",
 			loadtext: '<p>Cargando...',
 			hoverrows: false,
 		    viewrecords: true,
 		    refresh: true,
 		    gridview: true,
  		    sortname: 'id',
 		    sortorder: "ASC",
 		    beforeProcessing: function(data, status, xhr){
 		    	//Check Session
 				if( $.isEmptyObject(data.session) == false){
 					window.location = phost() + "login?expired";
 				}
 		    },
 		    loadBeforeSend: function () {//propiedadesGrid_cb
 	 	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
 		        $(this).closest("div.ui-jqgrid-view").find("#jqgh_tablaDiasferiadosGrid_cb, #jqgh_tablaDiasferiadosGrid_link").css("text-align", "center");
 		    }, 
 		    beforeRequest: function(data, status, xhr){},
 			loadComplete: function(data){
 			 
 				//check if isset data
 				if( data['total'] == 0 ){
 					$('#gbox_'+ grid_id).hide();
 					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron dias feriados.').css({"color":"#868686","padding":"30px 0 0"}).show();
 				}
 				else{
 					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
 				}
 			},
 	 		onSelectRow: function(id){
 				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
 			},
		});
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
		
			 
		   opcionesModal.on("click", ".editarFechaBtn", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				
				formulario.find('#fecha_nombre, #fecha_descripcion, #fecha_fecha_oficial,  #id_diaferiado, #cuenta_pasivo_id').prop("value", "");
			    formulario.find('#fecha_horas option:first').prop('selected', true);
			   	    
			   	 //Limpiando los choisens
			   	formulario.find('#id_deducciones, #id_acumulados').empty();
			   	formulario.find('#id_deducciones, #id_acumulados').trigger("chosen:updated");
					
				
				formulario.find('#id_deducciones').append(original_deduccion);
				formulario.find('#id_acumulados').append(original_acumulado);
				formulario.find('#id_deducciones, #id_acumulados').trigger("chosen:updated");
			   	 
				var id = $(this).attr("data-id");
		  		var rowINFO 	= $("#tablaDiasferiadosGrid").getRowData(id);
		 		$('#titulo_form_feriados').text("Editando fecha:  "+ rowINFO['Nombre']);

		 
		  	    var nombre 			= rowINFO['Nombre'];
		  	    var descripcion 	= rowINFO['Descripcion'];
		  	    var fecha 			= rowINFO['Fecha Oficial'];
		  	    var horas 			= rowINFO['Horas'];
		  	    var estado_id 			= rowINFO['Estado_id'];
		  	    var cuenta_pasivo_id 			= rowINFO['Cuenta_pasivo_id'];
		  	    var acumulados_lista 			= rowINFO["acumulados"] != undefined && rowINFO["acumulados"] != "" ? unserialize(rowINFO["acumulados"]) : ""; 
		  	    var deducciones_lista 			= rowINFO["deducciones"] != undefined && rowINFO["deducciones"] != "" ? unserialize(rowINFO["deducciones"]) : "";

		  	    
		  	  formulario.find('#fecha_fecha_oficial').daterangepicker({
		      	  singleDatePicker: true,
		          showDropdowns: true,
		          opens: "left",
		          startDate: fecha,
		          locale: {
		          	 format: 'DD/MM/YYYY',
		          	 applyLabel: 'Seleccionar',
		             cancelLabel: 'Cancelar',
		          	 daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
		             monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		             firstDay: 1
		          }
		      }); 
		  	  
		  	   if(acumulados_lista != '')
		  	   {
		  	 	   	    	 $.each(acumulados_lista, function(i,name) {  
		  	 	   	    		 formulario.find('#id_acumulados option[value="'+ name['id'] +'"]').prop('selected', 'selected');
		   			    	}); 

		  	  }
		  	  if(deducciones_lista != '')
			  {
			 	   	    	 $.each(deducciones_lista, function(i,name) {  
			 	   	    		formulario.find('#id_deducciones option[value="'+ name['id'] +'"]').prop('selected', 'selected');
				  	    		
					    	}); 
			  } 
		  	  
		  	   formulario.find('#id_acumulados, #id_deducciones').chosen({width: '100%'}).trigger('chosen:updated');
		  		   	    
		   	    //Abrir formulario
		   	    formulario.find('.ibox-content:not(:visible)').prev().find('a').trigger('click');
		   	    formulario.find('#id_diaferiado').prop("value", id);
		   	    formulario.find('#fecha_nombre').prop("value", nombre);
		   	    formulario.find('#horas_no_laboradas').prop("value", horas);
		   	    formulario.find('#descripcion').prop("value", descripcion);
		   	    formulario.find('#estado_id option[value="'+ estado_id +'"]').prop('selected', 'selected');
		   	    formulario.find('#cuenta_pasivo_id option[value="'+ cuenta_pasivo_id +'"]').prop('selected', 'selected');
 		   	    $('#opcionesModal').modal('hide');
		 	});
			
			$(botones.cancelar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				limpiarDiaferiado();
 			});
			$(botones.guardar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
		 		 
				agregarDiaferiado();
			});
			  
  
 	};
	 
 	//Reload al jQgrid
	var campos = function(){
		formulario.find('#fecha_fecha_oficial').daterangepicker({
	 	      	singleDatePicker: true,
	 	          showDropdowns: true,
	 	          opens: "left",
	  	          locale: {
	 	          	format: 'DD/MM/YYYY',
	 	          	applyLabel: 'Seleccionar',
	 	             cancelLabel: 'Cancelar',
	 	          	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
	 	              monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	 	              firstDay: 1
	 	          }
	 	      });
		 
	};
 	//Reload al jQgrid
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
	var agregarDiaferiado = function(){

		if(formulario.validate().form() == true )
		{
			$.ajax({
				url: phost() + 'configuracion_planilla/ajax-crear-diaferiado',
				data: formulario.serialize(),
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {

				//Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				//Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				if(json.response == true){
						 toastr.success(json.mensaje);
						 limpiarDiaferiado();
						 recargar();
					}else{
					toastr.error(json.mensaje);
				}
	  
			}); 
		}
		
	};
	 
	//Limpiar campos de busqueda
	var limpiarDiaferiado = function(){
 		formulario.find('#id_diaferiado').val("0");
 		formulario.find('input[type="text"]').prop("value", "");
 		formulario.find('select').val("");
 		formulario.find('select[id="estado_id"]').prop('selected', 'selected');
		
 		formulario.find('#estado_id option[value="1"]').prop('selected', 'selected');
   		
 		formulario.find('#id_deducciones, #id_acumulados').empty();
 		formulario.find('#id_deducciones, #id_acumulados').trigger("chosen:updated");
 		formulario.find('#id_deducciones').append(original_deduccion);
 		formulario.find('#id_acumulados').append(original_acumulado);
 		formulario.find('#id_deducciones, #id_acumulados').trigger("chosen:updated");
 		formulario.find('#titulo_form_feriados').text("Datos generales");
	};
	
	return{	    
		init: function() {
			tabla();
			campos();
 			eventos();
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

feriadosGrid.init();

 
	/*$('#GenerarDiasFeriados').hide();
	
 	 $("#GenerarDiasFeriados").click(function() {
 		var mensaje = 'Esta seguro que desea crear o reemplazar los dias dias feriados de este a&ntilde;o?';
  		var footer_buttons = ['<div class="row">',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
   		   '</div>',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="duplicarDiasAccion" class="btn btn-w-m btn-success btn-block" type="button">Confirmar</button>',
   		   '</div>',
   		   '</div>'
   		].join('\n');
   		
  		$('#opcionesModal').find('.modal-title').empty().append('Confirme');
		$('#opcionesModal').find('.modal-body').empty().append(mensaje);
		$('#opcionesModal').find('.modal-footer').empty().append(footer_buttons);
		$('#opcionesModal').modal('show');
	});*/
 	 
 	
 	/*$('#opcionesModal').on("click", "#duplicarDiasAccion", function(e){
 		  $.ajax({
 				url: phost() + 'configuracion_planilla/ajax-duplicar-diasferiados',
 				data: {
 					erptkn: tkn,
 	 			},
 				type: "POST",
 				dataType: "json",
 				cache: false,
 			}).done(function(json) {

 				//Check Session
 				if( $.isEmptyObject(json.session) == false){
 					window.location = phost() + "login?expired";
 				}
  			 if(json.result == true){
  				setTimeout(function(){
					$("#diasferiadosGrid").jqGrid({
						url: phost() + 'planilla/ajax-listar-diasferiados',
						datatype: "json",
						postData: {
 				 	   		erptkn: tkn
					   	},
					}).trigger('reloadGrid');
				}, 500);
  						toastr.success("Se duplic&oacute; con exito");
 				}else{
 					toastr.error("Hubo problemas al duplicar los dias o no existen fechas del a&ntilde;o pasado.");
 				}
  			 
  				 
 			});
 	 	    //Ocultar ventana
 		    $('#opcionesModal').modal('hide');
 		
 	});*/
 	
 
  
  
