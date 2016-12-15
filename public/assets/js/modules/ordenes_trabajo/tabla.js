//Tabla Accion de Personal
var tablaAccionPersonal = (function(){

	var url = 'ordenes_trabajo/ajax-listar';
	var grid_id = "tablaOrdenesTrabajoGrid";
	var grid_obj = $("#tablaOrdenesTrabajoGrid");
	var opcionesModal = $('#opcionesModal');
	var formulario = $('#buscarOrdenForm');
	var formularioExportar = $('#exportarOrdenesForm');
	
	var botones = {
		opciones: ".viewOptions",
		detalle: ".verDetalle",
		buscar: "#searchOrdenBtn",
		limpiar: "#clearOrdenBtn",
		descargar: ".descargarAdjuntoBtn",
		exportar: "#exportarLnk",
	};
	
	var equipoid = "";
	
	if(typeof equipoID != "undefined"){
		equipoid = equipoID;
	}
	
	var tabla = function(){
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	    'No. Orden',
				'Cliente',
				'Fecha de inicio',
				'Centro contable',
				'Estado',
				'',
				'',
			],
		   	colModel:[
		   	    {name:'No. Orden', index:'numero', width: 40},
				{name:'Cliente', index:'cliente', width:40},
				{name:'Fecha de Inicio', index:'fecha_inicio', width:15},
				{name:'Centro Contable', index:'centro_id', width:40 },
		   		{name:'Estado', index:'estado', width:10, align: 'center' },
				{name:'link', index:'link', width:20, sortable:false, resizable:false, hidedlg:true, align:"center", hidden: false},
				{name:'options', index:'options', hidedlg:true, hidden: true},
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn,
		   		equipo_id: equipoid
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
		    sortname: 'created_at',
		    sortorder: "DESC",
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
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron datos de Ordenes de Trabajo.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
			tablaAccionPersonal.redimensionar();
		});
	};
	
	//Inicializar Eventos de Botones
	var eventos = function(){
		
		//Boton de Opciones
		grid_obj.on("click", botones.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var id = $(this).attr("data-id");
			
			var rowINFO = grid_obj.getRowData(id);
		    var options = rowINFO["options"];
		    options = options.replace(/0000/gi, id);
		    
	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones');
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
		
		//Ver Detalle
		grid_obj.on("click", botones.detalle, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var formulario = $(this).attr("data-formulario");
			var accion_id = $(this).attr("data-accion-id");
			var modulo_name_id = formulario.replace(/(es|s)$/g, '') + '_id';
			
			//Before using local storage, check browser support for localStorage and sessionStorage
			if(typeof(Storage) !== "undefined") {
				
				//Grabar id de la accion
				localStorage.setItem(modulo_name_id, accion_id);
			}
			
			//Verificar si existe o no variable
			//colaborador_id
			if(typeof colaborador_id != 'undefined'){
				
				//Verificar si el formulario esta siendo usado desde
				//Ver Detalle de Colaborador
				if(window.location.href.match(/(colaboradores)/g)){
				
					var scope = angular.element('[ng-controller="'+ ucFirst(formulario) +'Controller"]').scope();
					scope.popularFormulario();
				}
				
			}else{
				window.location = phost() + 'accion_personal/crear/' + formulario;
			}
		});
		
		//Ver Detalle
		$(opcionesModal).on("click", botones.detalle, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var formulario = $(this).attr("data-formulario");
			var accion_id = $(this).attr("data-accion-id");
			var modulo_name_id = formulario.replace(/(es|s)$/g, '') + '_id';
			
			//Before using local storage, check browser support for localStorage and sessionStorage
			if(typeof(Storage) !== "undefined") {
				
				//Grabar id de la accion
				localStorage.setItem(modulo_name_id, accion_id);
			}
			
			//Verificar si existe o no variable
			//colaborador_id
			if(typeof colaborador_id != 'undefined'){
				
				//Verificar si el formulario esta siendo usado desde
				//Ver Detalle de Colaborador
				if(window.location.href.match(/(colaboradores)/g)){
				
					var scope = angular.element('[ng-controller="'+ ucFirst(formulario) +'Controller"]').scope();
					scope.popularFormulario();
				
					//Activar Tab
					//$('#moduloOpciones').find('ul').find("a:contains('"+ formulario.replace(/(es|s)$/g, '') +"')").trigger('click');
					$('#moduloOpciones').find('ul').find('a[href*="'+ formulario.replace(/(es|s)$/g, '') +'"]').trigger('click');
					//console.log( formulario.replace(/(es|s)$/g, '') );
				}
				
			}else{
				window.location = phost() + 'accion_personal/crear/' + formulario;
			}
		});
		
		//Boton de Buscar
		formulario.on("click", botones.buscar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			buscar();
		});
		
		//Boton de Reiniciar jQgrid
		formulario.on("click", botones.limpiar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			recargar();
			limpiarCampos();
		});
		
		//Boton de Exportar Colaborador
		$(botones.exportar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Exportar Seleccionados del jQgrid
			var ids = [];
				ids = grid_obj.jqGrid('getGridParam','selarrrow');
			
			//Verificar si hay seleccionados
			if(ids.length > 0){
				
				$('#ids').val(ids);
				formularioExportar.submit();
		        $('body').trigger('click');
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
				no_accion_personal: '',
				no_orden: '',
				cliente: '',
				estado_id: '',
				fecha_desde: '',
				fecha_hasta: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};
	
	//Buscar cargo en jQgrid
	var buscar = function(){
		
		var no_orden 	= formulario.find('#no_orden').val();
		var cliente 	= formulario.find('#cliente').val();
		var fecha_desde = formulario.find('#fecha_desde').val();
		var fecha_hasta = formulario.find('#fecha_hasta').val();
		var estado_id 	= formulario.find('#estado_id').val();
		
		if(no_orden != "" || cliente  != "" || fecha_desde != "" || fecha_hasta != "" || estado_id != "")
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					no_orden: no_orden,
					cliente: cliente,
					estado_id: estado_id,
					fecha_desde: fecha_desde,
					fecha_hasta: fecha_hasta,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
	
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		formulario.find('input[type="text"]').prop("value", "");
		formulario.find('select').find('option:eq(0)').prop("selected", "selected");
		actualizar_chosen();
	};
	
	var actualizar_chosen = function() {
		//refresh chosen
		setTimeout(function(){
			formulario.find('select.chosen-select').trigger('chosen:updated');
		}, 50);
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

tablaAccionPersonal.init();