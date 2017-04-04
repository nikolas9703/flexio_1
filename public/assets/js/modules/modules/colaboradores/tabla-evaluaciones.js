//Modulo Tabla de Evaluaciones
var tablaEvaluaciones = (function(){

	var evaluacion_id = '';
	var url = 'colaboradores/ajax-listar-evaluaciones';
	var grid_id = "tablaEvaluacionesGrid";
	var grid_obj = $("#tablaEvaluacionesGrid");
	var opcionesModal = $('#opcionesModal');
	var formularioBusqueda = '#buscarEvaluacionForm';
	var formularioEvaluacionModal = $('#formularioEvaluacionModal');
	
	var botones = {
		opciones: ".viewOptions",
		editar: ".editarEvaluacionBtn",
		descargar: ".descargarEvaluacionBtn",
		limpiar: "#clearEvaluacionBtn",
		buscar: "#searchEvaluacionBtn"
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
			tablaEvaluaciones.redimensionar();
		});
	};
	
	var tabla = function(){
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'No',
				'Fecha',
				'Tipo de evaluaci&oacute;n',
				'C.Contable',
				'&Aacute;rea de negocio',
				'Cargo',
				'Creado por',
				'Observaciones',
				'Calificaci&oacute;n',
				'Resultado',
				'Acci&oacute;n',
				'',
				'',
				'',
			],
		   	colModel:[
				{name:'numero', index:'numero', width:20},
				{name:'fecha', index:'fecha', width:30},
				{name:'tipo_evaluacion', index:'tipo_evaluacion_id', width:50},
		   		{name:'centro_contable', index:'centro_contable', width: 40},
		   		{name:'area_negocio', index:'departamento', width: 40},
		   		{name:'cargo', index:'cargo', width: 30},
		   		{name:'creado_por', index:'creado_por', width: 30},
				{name:'observaciones', index:'observaciones', width:75 },
				{name:'calificacion', index:'calificacion', width:25 },
				{name:'resultado', index:'resultado_id', width:25 },
				{name:'link', index:'link', width:35, sortable:false, resizable:false, hidedlg:true, align:"center"},
				{name:'options', index:'options', hidedlg:true, hidden: true},
				{name:'archivo_ruta', index:'archivo_ruta', hidedlg:true, hidden: true},
				{name:'archivo_nombre', index:'archivo_nombre', hidedlg:true, hidden: true},
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
		    sortname: 'fecha',
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
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron evaluaciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
		    opcionesModal.find('.modal-title').empty().append('Opciones: Evaluacion '+ rowINFO["numero"]);
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
		
		//Boton de Descargar Evaluacion
		opcionesModal.on("click", botones.descargar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var evaluacion_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(evaluacion_id);
			
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
		
		//Boton de Editar Evaluacion
		opcionesModal.on("click", botones.editar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			evaluacion_id = $(this).attr("data-id");
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			//Inicializar opciones del Modal
			formularioEvaluacionModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			formularioEvaluacionModal.attr("data-uuid", colaborador_uuid);
			formularioEvaluacionModal.find('.modal-title').empty().append('Editar Evaluaci&oacute;n');
			formularioEvaluacionModal.find('input[id="campo[colaborador_id]"]').val(colaborador_id);
			formularioEvaluacionModal.find('input[id="campo[id]"]').val(evaluacion_id);
			formularioEvaluacionModal.modal('show');
		});
	};
	
	//Boton de Buscar Evaluacion
	$(botones.buscar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		buscarEvaluacion();
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
				tipo_evaluacion_id: '',
				centro_contable_id: '',
				resultado_id: '',
				fecha_evaluacion: '',
				usuario_id: '',
				numero_evaluacion: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
		
	};
	
	//Buscar evaluacion en jQgrid
	var buscarEvaluacion = function(){

		var tipo_evaluacion_id 	= $(formularioBusqueda).find('#tipo_evaluacion_id').val();
		var centro_contable_id 	= $(formularioBusqueda).find('#centro_contable_id').val();
		var resultado_id 		= $(formularioBusqueda).find('#resultado_id').val();
		var fecha_evaluacion 	= $(formularioBusqueda).find('#fecha_evaluacion').val();
		var usuario_id 			= $(formularioBusqueda).find('#usuario_id').val();
		var numero_evaluacion 	= $(formularioBusqueda).find('#numero_evaluacion').val();

		if(tipo_evaluacion_id != "" || centro_contable_id != "" || resultado_id != "" || fecha_evaluacion != "" || usuario_id != "" || numero_evaluacion != "")
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					tipo_evaluacion_id: tipo_evaluacion_id,
					centro_contable_id: centro_contable_id,
					resultado_id: resultado_id,
					fecha_evaluacion: fecha_evaluacion,
					usuario_id: usuario_id,
					numero_evaluacion: numero_evaluacion,
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

tablaEvaluaciones.init();

