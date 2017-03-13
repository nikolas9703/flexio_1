 $(function(){
 	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		tablaRecargosGrid.redimensionar();
	});
});
 
 //Modulo Tabla de Cargos
var tablaRecargosGrid = (function(){
 	var url 		= 'configuracion_planilla/ajax-listar-recargos';
 	var grid_id 	= "tablaRecargosGrid";
	var grid_obj 	=  $("#tablaRecargosGrid");
	var opcionesModal = $('#opcionesModal');
	var formulario = $('#crearRecargoForm');
  	
	var botones = {
			opciones: ".viewOptions",
			cancelar: "#cancelarRecargoBtn" ,
			guardar: "#guardarFormRecargoBtn",
    };
 	
  	
	var tabla = function(){
 		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	   'Id',
		   	   'Abreviatura para hoja de tiempo',
		   	   '% por hora',
   	           'Descripci&oacute;n',
   	           'Estado',
   	           '',
   	           '',
 			   ''
  			],
		   	colModel:[
		   	    {name:'Id', index:'id',hidden: true  },
		   	    {name:'Nombre', index:'nombre',  sortable:false,   },
		   	    {name:'Porcentaje_hora', index:'porcentaje_hora', decimalPlaces: 2, sortable:false },
  				{name:'Descripcion', index:'descripcion',  sortable:false  },
  				{name:'Estado', index:'estado', sortable:false, resizable:false, hidedlg:true  },
  				{name:'Estado_id', index:'estado_id', sortable:false, resizable:false, hidedlg:true, hidden: true  },
  				{name:'Estado', index:'estado', sortable:false, resizable:false, hidedlg:true,  align:"center"},
   				{name:'options', index:'options', hidedlg:true, hidden: true},
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
		        $(this).closest("div.ui-jqgrid-view").find("#jqgh_tablaRecargosGrid_cb, #jqgh_tablaRecargosGrid_link").css("text-align", "center");
		    }, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron comentarios.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
 
	
	var campos = function(){
		$(formulario).validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
 		});
		
		$(formulario).find('#nombre').rules("add",{ required: true});
 		
		
   };
	//Limpiar campos de busqueda
	var limpiarRecargo = function(){
 		formulario.find('input[type="text"]').prop("value", "");
   		formulario.find('#estado_id option[value="1"]').prop('selected', 'selected');
		formulario.find('input[type="input-left-addon"]').prop("value", "");
		formulario.find('#id_recargo').val("0");
  	   	formulario.find('#titulo_form_recargos').text("Datos generales");
		
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
			opcionesModal.on("click", ".editarRecargoBtn", function(e){
 		 		e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				
                                
                                //lleva la pagina al top     
                                $("html, body").animate({ scrollTop: 0 }, 600);

				$('#id_recargo').prop("value", "0");
				$('#nombre, #porcentaje_hora, #descripcion').prop("value", "");
  			 	$('#estado_id').empty();
   			   	 
				var id = $(this).attr("data-id");
		  		var rowINFO 	= grid_obj.getRowData(id);
		  		
		  		
		 		$('#titulo_form_recargos').text("Editando recargo:  "+ rowINFO['Nombre']);

  		  	    var nombre 			= rowINFO['Nombre'];
		  	    var Porcentaje_hora = rowINFO['Porcentaje_hora'];
 		  	    var descripcion 	= rowINFO['Descripcion'];
  		  	    var estado_id 		= rowINFO['Estado_id'];
  		  	    
   				formulario.find('.ibox-content:not(:visible)').prev().find('a').trigger('click');
   				formulario.find('#nombre').prop("value", nombre);
   				formulario.find('#porcentaje_hora').prop("value", Porcentaje_hora);
   				formulario.find('#descripcion').prop("value", descripcion);
   				formulario.find('#id_recargo').prop("value", id);
  				formulario.find('#estado_id option[value="'+ estado_id +'"]').prop('selected', 'selected');
		  	    //Ocultar ventana
		   	    $('#opcionesModal').modal('hide');
		 	});
			
			$(botones.cancelar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
			 	limpiarRecargo();
 			});
			$(botones.guardar).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
		 		 
				agregarRecargo();
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
	var agregarRecargo = function(){

		if(formulario.validate().form() == true )
		{
			$.ajax({
				url: phost() + 'configuracion_planilla/ajax-crear-recargo',
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
						 limpiarRecargo();
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

tablaRecargosGrid.init();





