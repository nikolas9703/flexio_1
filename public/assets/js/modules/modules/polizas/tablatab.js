//Modulo
var tablaPolizas = (function(){
	
	var uuid_agente=$('input[name="campo[uuid]').val();
	
	var uuid_asegura=$('input[name="campo[uuid_aseguradora]').val();

	var url = 'polizas/ajax_listar_polizas_agt';
	var grid_id = "tablaPolizasGrid";
	var grid_obj = $("#tablaPolizasGrid");
	var opcionesModal = $('#opcionesModal');	
	
	var botones = {
		opciones: ".viewOptions",
		editar: "",		
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
		modalstate : "span.estadoPolizas",
		exportarPolizas: "#exportarBtn",
	};
	
	var tabla = function(){		
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'',
		   	    'No. Póliza',
				'Cliente',
				'Aseguradora',
				'Ramo',
				'Inicio Vigencia',
				'Fin Vigencia',
				'Fecha de creación',
				'Estado',			
				'',
				'',
			],
		   	colModel:[
				{name:'id', index:'id', width: 48, hidden: true},
		   	    {name:'numero', index:'numero', width: 48},
				{name:'cliente', index:'cliente', width: 70},  
				{name:'aseguradora', index:'aseguradora', width:40},
				{name:'ramo', index:'ramo', width:40},
				{name:'vigencia_desde', index:'pol_polizas.inicio_vigencia', width: 30},
				{name:'vigencia_hasta', index:'pol_polizas.fin_vigencia', width: 40 },
		   		{name:'Fecha creación', index:'fecha_creacion', width: 44},
		   		{name:'Estado', index:'estado', width: 40 },
				{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center", search:false},
				{name:'options', index:'options', hidedlg:true, hidden: true, search:false , hidedlg:true}		
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn,
				uuid_agente: uuid_agente,
				uuid_asegura: uuid_asegura
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
		    sortname: 'fecha_creacion',
		    sortorder: "DESC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {
				$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#"+grid_id+"_cb, #jqgh_tablaPolizasGrid_link").css("text-align", "center");
			}, 
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
		});
		
		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			tablaPolizas.redimensionar();
		});
		
		grid_obj.jqGrid('navGrid',grid_id,{del:false,add:false,edit:false,search:true});
		grid_obj.jqGrid('filterToolbar',{searchOnEnter : false});
	};
	
	//Inicializar Eventos de Botones
	var eventos = function(){		
		
		//Boton de Buscar Colaborador
		$(botones.buscar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			buscarPolizas();
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

		//Boton de opciones
		grid_obj.on("click", botones.opciones, function (e) {          
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");  
            var rowINFO = $.extend({}, grid_obj.getRowData(id));
            var options = rowINFO.options;
            console.log(id);
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.numero).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

        //Boton de estado
        grid_obj.on("click", botones.modalstate, function (e) {          
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");  
            console.log(id);
            var rowINFO = $.extend({}, grid_obj.getRowData(id));
            var options = rowINFO.modalstate;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.numero).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

	}; 

	//Boton de Exportar contacto
	$(botones.exportarPolizas).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation(); 
		if($('#id_tab_polizas').is(':visible') == true){			
			//Exportar Seleccionados del jQgrid
			var ids = [];
			ids = grid_obj.jqGrid('getGridParam','selarrrow');
			//Verificar si hay seleccionados
			if(ids.length > 0){
				console.log(ids);
				$('#ids_polizas').val(ids);
				$('form#exportarPolizas').submit();
				$('body').trigger('click');
				
				if($("#cb_"+grid_id).is(':checked')) {
					$("#cb_"+grid_id).trigger('click');
				}
				else
				{
					$("#cb_"+grid_id).trigger('click');
					$("#cb_"+grid_id).trigger('click');
				}
			}
		}
	});
	
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
				vigencia_desde: '',
				vigencia_hasta: '',
				fecha_creacion: '',
				estado: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};
	
	//Buscar cargo en jQgrid
	var buscarPolizas = function(){
            
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
					vigencia_desde: vigencia_desde,
					vigencia_hasta: vigencia_hasta,
					fecha_creacion: fecha_creacion,
					estado: estado,					
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
	
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarPolizasForm').find('input[type="text"]').prop("value", "");
		$('#buscarPolizasForm').find('.chosen-select').val('').trigger('chosen:updated');
		$('#buscarPolizasForm').find('.ramo').val(' ').trigger('change.select2');
       	$('#buscarPolizasForm').find('#fecha_creacion').prop("value", "");
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

tablaPolizas.init();

