 $(function(){
 	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		AcumuladosGrid.redimensionar();
	});
});
 
 //Modulo Tabla de Cargos
var AcumuladosGrid = (function(){
 	var url 		= 'configuracion_planilla/ajax-listar-acumulados';
 	var grid_id 	= "tablaAcumuladosGrid";
	var grid_obj 	=  $("#tablaAcumuladosGrid");
	var opcionesModal = $('#opcionesModal');
	var formulario = $('#crearAcumuladosForm');
  	
	var botones = {
			opciones: ".viewOptions",
			cancelar: "#cancelarAcumuladoBtn" ,
			guardar: "#guardarAcumuladoBtn",
    };
 	
  	
	var tabla = function(){
 		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	   'Id',
		   	   'Nombre',
		   	   'Cuenta de pasivo',
		   	   //'Valor acumulado',
		   	  // 'Rata actual %',
		   	   'Descripci&oacute;n',
 		   	   '',
     	       'Estado',
   	           'Estado Id',
   	           'fecha Corte',
   	           '',
   	           '',
   	           '',
  			   ''
  			],
		   	colModel:[
		   	    {name:'Id', index:'id',hidden: true  },
		   	    {name:'Nombre', index:'nombre',  sortable:false },
  				{name:'Cuenta_Pasivo', index:'Cuenta_pasivo',  sortable:false},
  				//{name:'Valor Acumulado', index:'Valor_Acumulado', sortable:false, resizable:false, hidedlg:true },
  				//{name:'Rata_Actual', index:'Rata_actual', sortable:false, resizable:false, hidedlg:true },
		   	    {name:'Descripcion', index:'descripcion',  sortable:false},
  				{name:'Cuenta_pasivo_id', index:'Cuenta Pasivo ID', sortable:false, resizable:false, hidedlg:true, hidden: true  },
  				{name:'Estado', index:'Estado', sortable:false, resizable:false, hidedlg:true  },
  				{name:'Estado_id', index:'Estado_id', sortable:false, resizable:false, hidedlg:true, hidden: true  },
  				{name:'Fecha_Corte', index:'Fecha_corte', sortable:false, resizable:false, hidedlg:true, hidden: true  },
  				{name:'options', index:'options', hidedlg:true, align:"center" },
  				{name:'options', index:'options', hidedlg:true, hidden: true},
  				{name:'Maximo_acumulable', index:'Maximo_acumulable', hidedlg:true, hidden: true},
   				{name:'Tipo_acumulado', index:'Tipo_acumulado', hidedlg:true, hidden: true},
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
		    sortname: 'justificacion',
		    sortorder: "ASC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {//propiedadesGrid_cb
	 	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
		        $(this).closest("div.ui-jqgrid-view").find("#jqgh_tablaAcumuladossGrid_cb, #jqgh_tablaAcumuladossGrid_link").css("text-align", "center");
		    }, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron acumulados.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}
 				$('#'+ grid_id +'Pager_right').empty();
 				
			},
			  
			  onSelectRow: function(id){
				  $(this).find('tr#'+ id).removeClass('ui-state-highlight');
    		},
 		});
	};
 
	
	var campos = function(){
		$(formulario).validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
 		});
		
		$(formulario).find('#nombre').rules("add",{ required: true});
		$(formulario).find('#cuenta_pasivo_id').rules("add",{ required: true});
  		
		formulario.find('#fecha_corte').daterangepicker({
		    	singleDatePicker: true,
		        showDropdowns: true,
		        opens: "left",
		        setDate: '2013-01-01',
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
	//Limpiar campos de busqueda
	var limpiarDeduccion = function(){
		formulario.find('input[type="text"]').prop("value", "");
		formulario.find('select').val("");
		formulario.find('#estado_id option[value="1"]').prop('selected', 'selected');
		formulario.find('input[type="input-left-addon"]').prop("value", "");
		formulario.find('#id_acumulado').val("0");
		formulario.find('#titulo_form_acumulado').text("Datos generales");
		
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
			opcionesModal.on("click", ".editarAcumuladoBtn", function(e){
 		 		e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				
				$('#id_acumulado').prop("value", "0");
				$('#cuenta_pasivo_id').val("");
     			   	 
				var id = $(this).attr("data-id");
		  		var rowINFO 	= grid_obj.getRowData(id);
		  		
		  		
		 		$('#titulo_form_acumulado').text("Editando Acumulado:  "+ rowINFO['Nombre']);

  		  	    var nombre 			= rowINFO['Nombre'];
  		  	    var descripcion 	= rowINFO['Descripcion'];
  		  	    var estado_id 		= rowINFO['Estado_id'];
  		  	    var cuenta_pasivo_id 		= rowINFO['Cuenta_pasivo_id'];
  		  	    var fecha_corte 		= rowINFO['Fecha_Corte'];
  		  	    var maximo_acumulable 		= rowINFO['Maximo_acumulable'];
  		  	    var tipo_acumulado 		= rowINFO['Tipo_acumulado'];
  		  	    
   				formulario.find('.ibox-content:not(:visible)').prev().find('a').trigger('click');
   				formulario.find('#nombre').prop("value", nombre);
   				formulario.find('#descripcion').prop("value", descripcion);
   				formulario.find('#id_acumulado').prop("value", id);
   				formulario.find('#maximo_acumulable').prop("value", maximo_acumulable);
   				formulario.find('#estado_id option[value="'+ estado_id +'"]').prop('selected', 'selected');
   				formulario.find('#cuenta_pasivo_id option[value="'+ cuenta_pasivo_id +'"]').prop('selected', 'selected');
   				formulario.find('#tipo_acumulado option[value="'+ tipo_acumulado +'"]').prop('selected', 'selected');

   				formulario.find('#fecha_corte').daterangepicker({
   			    	singleDatePicker: true,
   			        showDropdowns: true,
   			        opens: "left",
   			        startDate:fecha_corte,
   	 		        locale: {
   			        	format: 'DD/MM/YYYY',
   			        	applyLabel: 'Seleccionar',
   			            cancelLabel: 'Cancelar',
   			        	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
   			            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
   			            firstDay: 1
   			        }
   			    }); 
		  	    //Ocultar ventana
		   	    $('#opcionesModal').modal('hide');
		 	});
			
			$(botones.cancelar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
			 	limpiarDeduccion();
 			});
			$(botones.guardar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
		 		 
				agregarAcumulado();
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
	var agregarAcumulado = function(){

		if(formulario.validate().form() == true )
		{
			$.ajax({
				url: phost() + 'configuracion_planilla/ajax-crear-acumulado',
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
						 limpiarDeduccion();
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

AcumuladosGrid.init();





