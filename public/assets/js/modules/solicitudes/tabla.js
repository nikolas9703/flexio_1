//Modulo
var tablaSolicitudes = (function(){

	var url = 'solicitudes/ajax-listar';
	var grid_id = "tablaSolicitudesGrid";
	var grid_obj = $("#tablaSolicitudesGrid");
	var opcionesModal = $('#opcionesModal');	
	
	var botones = {
		opciones: ".viewOptions",
		editar: "",		
		buscar: "#searchBtn",
		limpiar: "#clearBtn"
	};
	
	var tabla = function(){		
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	    'No. Solicitud',
				'Cliente',
				'Aseguradora',
				'Ramo',
				'Tipo',
				'D&iacute;as transcurridos',
				'Fecha de creaci&oacute;n',
				'Usuario',
				'Estado',			
				'',
				'',
			],
		   	colModel:[
		   	    {name:'numero', index:'numero', width: 50},
				{name:'cliente', index:'cliente', width:70, sortable:false},
				{name:'aseguradora', index:'aseguradora', width:40, sortable:false},
				{name:'ramo', index:'ramo', width:50, sortable:false},
				{name:'tipo', index:'tipo', width: 60, sortable:false },
				{name:'Dias transcurridos', index:'transcurridos', width: 40, sortable:false},
		   		{name:'Fecha de creacion', index:'fecha_creacion', width: 40, sortable:false},
		   		{name:'Usuario', index:'usuario', width: 40, sortable:false},
		   		{name:'Estado', index:'estado', width: 30, sortable:false },
				{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
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
		    multiselect: true,
		    sortname: 'numero',
		    sortorder: "ASC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {}, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				
				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron solicitudes.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
		
		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			tablaSolicitudes.redimensionar();
		});
	};
	
	//Inicializar Eventos de Botones
	var eventos = function(){		
		
		//Boton de Buscar Colaborador
		$(botones.buscar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			buscarSolicitudes();
		});
		
		//Boton de Reiniciar jQgrid
		$(botones.limpiar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			recargar();
			limpiarCampos();
		});
		
		//Boton de Activar Colaborador
		$(botones.activar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
		
			if($('#tabla').is(':visible') == true){
				
				//Exportar Seleccionados del jQgrid
				var colaboradores = [];
					colaboradores = grid_obj.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(colaboradores.length > 0){
					//Cambiar Estado
					toggleColaborador({colaboradores: colaboradores, estado_id: 1});
				}
	        }
		});
	};
	
	//Reload al jQgrid
	var recargar = function(){
		
		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				numero: '',
				cliente: '',
				aseguradora: '',
				ramo: '',
				tipo: '',
				fecha_creacion: '',
				usuario: '',
				estado: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};
	
	//Buscar cargo en jQgrid
	var buscarSolicitudes = function(){
            
		var numero 		= $('#no_solicitud').val();
		var cliente 		= $('#cliente').val();
		var aseguradora		= $('#aseguradora').val();
                var ramo = [];
		var ramo 		= $('.ramo').val();               
		var tipo                = $('#tipo_solicitud').val();
		var fecha_creacion = $('#fecha_creacion').val();
		var usuario = $('#usuario').val();
		var estado = $('#estado_id').val();		

		if(numero != "" || cliente != "" || aseguradora != "" || ramo != "" || tipo != "" || fecha_creacion != "" || usuario != "" || estado != "")
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					numero: numero,
					cliente: cliente,
					aseguradora: aseguradora,
					ramo: ramo,
					tipo: tipo,
					fecha_creacion: fecha_creacion,
					usuario: usuario,
					estado: estado,					
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
	
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarSolicitudesForm').find('input[type="text"]').prop("value", "");
		$('#buscarSolicitudesForm').find('.chosen-select').val('').trigger('chosen:updated');
		$('#buscarSolicitudesForm').find('.ramo').val(' ').trigger('change.select2');
                $('#buscarSolicitudesForm').find('#fecha_creacion').prop("value", "");
	};
	
	return{	    
		init: function() {
			tabla();
			eventos();
		},
		recargar: function(){
			//reload jqgrid
			recargar();
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

tablaSolicitudes.init();