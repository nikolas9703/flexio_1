 $(function(){
  	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		tablaBeneficios.redimensionar();
	});
});
 
 //Modulo Tabla de Cargos
var tablaBeneficios = (function(){
	var original_deduccion = $('#id_deducciones_original').html();
	var original_acumulado = $('#id_acumulados_original').html();
	
 	var url 		= 'configuracion_planilla/ajax-listar-beneficios';
 	var grid_id 	= "tablaBeneficiosGrid";
	var grid_obj 	=  $("#tablaBeneficiosGrid");
	var opcionesModal = $('#opcionesModal');
	var formulario = $('#crearBeneficioForm');
  	
	var botones = {
			opciones: ".viewOptions",
 			guardar: "#guardarBeneficioBtn",
 			cancelar: "#cancelarFormBtn" ,
			editar: ".editarBeneficioBtn"  
   };
 	
  	
	var tabla = function(){
 		grid_obj.jqGrid({
 			url: phost() + 'configuracion_planilla/ajax-listar-beneficios',
 		   	datatype: "json",
 		   	colNames:[
 				'Nombre',
 				'Descripci&oacute;n',
 				'Cuenta pasivo',
 				'',
 				'Modificador Actual %',
 				'Estado',
 				'Estado_ID',
 	 			'',
 	 			'',
 	 			'',
 				'',
 			],
 		   	colModel:[
 				{name:'Nombre', index:'nombre', width:50 },
 				{name:'Descripcion', index:'descripcion', width:50},
 				{name:'Pasivo', index:'cuenta_pasivo', width:50},
 				{name:'Cuenta_pasivo_id', index:'cuenta_pasivo_id', width:50, hidden: true},
 				{name:'Modificador', index:'modificador_actual', width:50},
 				{name:'Estado', index:'estado', width:50},
 				{name:'Estado_id', index:'estado_id', width:50, hidden: true},
 	 			{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true,  align:"center"},
 				{name:'options', index:'options', hidedlg:true, hidden: true},
 				{name:'acumulados', index:'acumulados', hidedlg:true, hidden: true},
 				{name:'deducciones', index:'deducciones', hidedlg:true, hidden: true},
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
 			pager: "#tablaBeneficiosGridPager",
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
 		        $(this).closest("div.ui-jqgrid-view").find("#jqgh_tablaBeneficiosGrid_cb, #jqgh_tablaBeneficiosGrid_link").css("text-align", "center");
 		    }, 
 		    beforeRequest: function(data, status, xhr){},
 			loadComplete: function(data){
 			 
 				//check if isset data
 				if( data['total'] == 0 ){
 					$('#gbox_'+ grid_id).hide();
 					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron beneficios.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
			opcionesModal.on("click", ".editarBeneficioBtn", function(e){
   		 		e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				
		 		var id = $(this).attr("data-id");
		  		var rowINFO 	= $("#tablaBeneficiosGrid").getRowData(id);
 		 		//Valores del Formulario
		  	    var nombre 			= rowINFO['Nombre'];
		  	    var descripcion 	= rowINFO['Descripcion'];
		  	    var cuenta_pasivo_id 	= rowINFO['Cuenta_pasivo_id'];
		  	    var modificador_actual 	= rowINFO['Modificador'];
		  	    var estado_id 		= rowINFO['Estado_id'];
		  	    var acumulados_lista 			= rowINFO["acumulados"] != undefined && rowINFO["acumulados"] != "" ? unserialize(rowINFO["acumulados"]) : ""; 
		  	    var deducciones_lista 			= rowINFO["deducciones"] != undefined && rowINFO["deducciones"] != "" ? unserialize(rowINFO["deducciones"]) : "";
		 
		  	    formulario.find('.ibox-content:not(:visible)').prev().find('a').trigger('click');
		  		formulario.find('#titulo_form').text("Editando Beneficio: "+ rowINFO['Nombre']);
		  	    formulario.find('#nombre, #descripcion').prop("value", "");
		  	    formulario.find('#nombre').prop("value", nombre);
		   	    formulario.find('#id_beneficio').prop("value", id);
		   	    formulario.find('#descripcion').prop("value", descripcion);
		   	    formulario.find('#cuenta_pasivo_id').prop("value", cuenta_pasivo_id);
		   	    formulario.find('#estado_id').prop("value", estado_id);
		   	    formulario.find('#modificador_actual').prop("value", modificador_actual);
		   	    
			 	formulario.find('#id_deducciones, #id_acumulados').empty();
			   	formulario.find('#id_deducciones, #id_acumulados').trigger("chosen:updated");
			   	formulario.find('#id_deducciones').append(original_deduccion);
				formulario.find('#id_acumulados').append(original_acumulado);
			   	formulario.find('#id_deducciones, #id_acumulados').trigger("chosen:updated");
			   	
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
		  	   
		   	    $('#opcionesModal').modal('hide');
		 	});
			
			$(botones.cancelar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				limpiarBeneficio();
 			});
			$(botones.guardar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
		 		 
				agregarBeneficio();
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
	var agregarBeneficio = function(){

		if(formulario.validate().form() == true )
		{
			$.ajax({
				url: phost() + 'configuracion_planilla/ajax-crear-beneficio',
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
						 limpiarBeneficio();
						 recargar();
					}else{
					toastr.error(json.mensaje);
				}
	  
			}); 
		}
		
	};
	 
	//Limpiar campos de busqueda
	var limpiarBeneficio = function(){
		formulario.find('input[type="text"]').prop("value", "");
 		formulario.find('select').val("");
   		formulario.find('#estado_id option[value="1"]').prop('selected', 'selected');
		formulario.find('input[type="input-right-addon"]').prop("value", "");
		
		formulario.find('#id_beneficio').val("0");
	 	formulario.find('#id_deducciones, #id_acumulados').empty();
	   	formulario.find('#id_deducciones, #id_acumulados').trigger("chosen:updated");
	   	formulario.find('#id_deducciones').append(original_deduccion);
		formulario.find('#id_acumulados').append(original_acumulado);
	   	formulario.find('#id_deducciones, #id_acumulados').trigger("chosen:updated");
	   	
	   	formulario.find('#titulo_form').text("Datos generales");
	};
	
	return{	    
		init: function() {
			tabla();
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

tablaBeneficios.init();

