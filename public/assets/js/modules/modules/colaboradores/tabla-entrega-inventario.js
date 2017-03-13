//Modulo Tabla de Entrega de Inventario
var tablaInventario = (function(){

	var evaluacion_id = '';
	var url = 'colaboradores/ajax-listar-entrega-inventario';
	var grid_id = "tablaEntregaInventarioGrid";
	var grid_obj = $("#tablaEntregaInventarioGrid");
	var opcionesModal = $('#opcionesModal');
	var formularioBusqueda = '#buscarEntregaInventarioForm';
	var formularioInventarioModal = $('#entregaInventarioModal');
	
	var botones = {
		opciones: ".viewOptions",
		editar: ".editarInventarioBtn",
		reemplazar: ".reemplazarInventarioBtn",
		descargar: ".descargarInventarioBtn",
		limpiar: "#clearInventarioBtn",
		buscar: "#searchInventarioBtn"
	};
	
	var init = function(){
		
		//Plugin Datepicker
		$('#fecha_evaluacion').daterangepicker({
			singleDatePicker: true,
			autoUpdateInput: false,
			format: 'MM-DD-YYYY',
		    showDropdowns: true,
		    opens: "left",
		    locale: {
		    	applyLabel: 'Seleccionar',
		        cancelLabel: 'Cancelar',
		    	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
		        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		        firstDay: 1
		    }
		}).on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD/MM/YYYY'));
		});
		
		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			tablaInventario.redimensionar();
		});
	};
	
	var tabla = function(){
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'Cantidad',
				'Item',
				'C&oacute;digo',
				'Fecha de entrega',
				'Duraci&oacute;n',
				'Pr&oacute;xima Entrega',
				'Entregado por',
				'Reemplazo',
				'Acci&oacute;n',
				'',
				'',
				'',
				'',
			],
		   	colModel:[
				{name:'cantidad', index:'cantidad', width:20},
				{name:'item', index:'item', width:60},
				{name:'codigo', index:'codigo', width:40},
		   		{name:'fecha_entrega', index:'fecha_entrega', width: 25},
		   		{name:'duracion', index:'duracion', width: 35},
		   		{name:'proxima_entrega', index:'proxima_entrega', width: 25},
		   		{name:'entregado_por', index:'entregado_por', width: 35},
		   		{name:'reemplazo', index:'reemplazo', width: 45},
				{name:'link', index:'link', width:35, sortable:false, resizable:false, hidedlg:true, align:"center"},
				{name:'options', index:'options', hidedlg:true, hidden: true},
				{name:'archivo_ruta', index:'archivo_ruta', hidedlg:true, hidden: true},
				{name:'archivo_nombre', index:'archivo_nombre', hidedlg:true, hidden: true},
				{name:'departamento_id', index:'departamento_id', hidedlg:true, hidden: true},
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn,
		   		colaborador_id: colaborador_id
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
		    sortname: 'id',
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
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron entrega de inventario.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
		    opcionesModal.find('.modal-title').empty().append('Opciones: Entrega/'+ rowINFO["item"]);
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
		
		//Boton de Descargar de Entrega de Inventario
		opcionesModal.on("click", botones.descargar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var entrega_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(entrega_id);
			
			var archivo_nombre = rowINFO["archivo_nombre"];
	    	var archivo_ruta = rowINFO["archivo_ruta"];
	    	var fileurl = phost() + archivo_ruta +'/'+ archivo_nombre;
	    	
	    	if(archivo_nombre == '' || archivo_nombre == undefined){
	    		return false;
	    	}
	    	
	    	//Descargar archivo
	    	downloadURL(fileurl, archivo_nombre);
			
		    //Ocultar modal
			opcionesModal.modal('hide');
		});
		
		//Boton de Editar Entrega de Inventario
		opcionesModal.on("click", botones.editar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var entrega_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(colaborador_id);
		    var departamento_id = rowINFO["departamento_id"];
			
			//Inicializar opciones del Modal
			formularioInventarioModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			formularioInventarioModal.attr("data-uuid", colaborador_uuid);
			formularioInventarioModal.attr("data-departamento-id", departamento_id);
			formularioInventarioModal.find('.modal-title').empty().append('Editar Entrega de Inventario');
			formularioInventarioModal.find('input[id="campo[colaborador_id]"]').val(colaborador_id);
			formularioInventarioModal.find('#departamento_id').find('option[value="'+ departamento_id +'"]').prop('selected', 'selected');
			formularioInventarioModal.find('input[id="campo[id]"]').val(entrega_id);
			formularioInventarioModal.find('#tipo_reemplazo_id').find('option:eq(0)').prop('selected', 'selected').closest('.form-group').addClass('hide');
			formularioInventarioModal.find('#departamento_id, #categoria_id, #item_id').removeAttr("disabled");
			formularioInventarioModal.modal('show');
		});
		
		//Boton de Editar Entrega de Inventario
		opcionesModal.on("click", botones.reemplazar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var entrega_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(colaborador_id);
		    var departamento_id = rowINFO["departamento_id"];
			
			//Inicializar opciones del Modal
			formularioInventarioModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			formularioInventarioModal.attr("data-uuid", colaborador_uuid);
			formularioInventarioModal.attr("data-departamento-id", departamento_id);
			formularioInventarioModal.find('.modal-title').empty().append('Reemplazo Inventario');
			formularioInventarioModal.find('input[id="campo[colaborador_id]"]').val(colaborador_id);
			formularioInventarioModal.find('#departamento_id').find('option[value="'+ departamento_id +'"]').prop('selected', 'selected');
			formularioInventarioModal.find('input[id="campo[id]"]').val(entrega_id);
			formularioInventarioModal.find('#tipo_reemplazo_id').find('option:eq(0)').prop('selected', 'selected').closest('.form-group').removeClass('hide');
			formularioInventarioModal.find('#departamento_id, #bodega_uuid, #categoria_id, #item_id').attr("disabled", "disabled");
			formularioInventarioModal.modal('show');
		});
	};
	
	//Boton de Buscar Evaluacion
	$(botones.buscar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		buscarEntregaInventario();
	});
	
	//Boton de Reiniciar jQgrid
	$(botones.limpiar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		recargar();
		limpiarCampos();
	});
	
	//Reload al jQgrid
	var recargar = function(){
		
		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				nombre_item: '',
				codigo_item: '',
				duracion_id: '',
				fecha_entrega_desde: '',
				fecha_entrega_hasta: '',
				entregado_por: '',
				tipo_reemplazo_id: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
	};
	
	//Buscar Entrega de Inventario en jQgrid
	var buscarEntregaInventario = function(){

		var nombre_item 	= $(formularioBusqueda).find('#nombre_item').val();
		var codigo_item 	= $(formularioBusqueda).find('#codigo_item').val();
		var duracion_id 	= $(formularioBusqueda).find('#duracion_id').val();
		var fecha_entrega_desde = $(formularioBusqueda).find('#fecha_entrega_desde').val();
		var fecha_entrega_hasta = $(formularioBusqueda).find('#fecha_entrega_hasta').val();
		var entregado_por 		= $(formularioBusqueda).find('#entregado_por').val();
		var tipo_reemplazo_id 	= $(formularioBusqueda).find('#tipo_reemplazo_id').val();

		if(nombre_item != "" || codigo_item != "" || duracion_id != "" || fecha_entrega_desde != "" || fecha_entrega_hasta != "" || entregado_por != "" || tipo_reemplazo_id != "")
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					nombre_item: nombre_item,
					codigo_item: codigo_item,
					duracion_id: duracion_id,
					fecha_entrega_desde: fecha_entrega_desde,
					fecha_entrega_hasta: fecha_entrega_hasta,
					entregado_por: entregado_por,
					tipo_reemplazo_id: tipo_reemplazo_id,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
	
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$(formularioBusqueda).find('input[type="text"]').prop("value", "");
		$(formularioBusqueda).find('select').find('option:eq(0)').prop("selected", "selected");
	};

	return{	    
		init: function() {
			tabla();
			init();
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

tablaInventario.init();

