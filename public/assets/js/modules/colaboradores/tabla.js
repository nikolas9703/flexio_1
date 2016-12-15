//Modulo
var tablaColaboradores = (function(){

	var url = 'colaboradores/ajax-listar';
	var grid_id = "tablaColaboradoresGrid";
	var grid_obj = $("#tablaColaboradoresGrid");
	var opcionesModal = $('#opcionesModal');
	var documentosModal = $('#documentosModal');
	var formularioInventarioModal = $('#entregaInventarioModal');
	var formularioDescuentoModal = $('#descuentosModal');
	var crearConsumoForm = $("#crearConsumoForm");
	var crearDescuentoForm = $("#crearDescuentoForm");
	var crearPlantillaForm = $("#crearPlantillaForm");
	
	var botones = {
		opciones: ".viewOptions",
		editar: "",
		duplicar: "",
		desactivar: "",
		activar: "#activarColaboradorLnk",
		trasladar: "#trasladarColaboradorLnk",
		liquidar: "#liquidarColaboradorLnk",
		exportar: "#exportarColaboradorLnk",
		subirArchivo: ".subirArchivoBtn",
		entregaInventario: ".nuevaEntregaInventarioBtn",
		descuento: ".nuevoDescuentoBtn",
		accionesPersonal: "ul#accionesPersonal",
		crearDescuentoBtn: ".crearDescuentoBtn",
		crearPlantillaBtn: ".crearPlantillaBtn",
		crearConsumo: ".crearConsumo",
		eliminarDeEquipoTrabajo: "#eliminarDeEquipoTrabajo",
		buscar: "#searchBtn",
		limpiar: "#clearBtn"
	};
	
	var tabla = function(){
		
		//Toggle columns
		var hidecedula = false;
		
		//iDs Modulos
		var equipoid = "";
		if(typeof equipoID != "undefined"){
			equipoid = $.parseJSON(equipoID);
		}
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	    'No. Colaborador',
				'Nombre',
				'Cedula',
				'Fecha de Contrataci&oacute;n',
				 (equipoid != '' ? 'C. Contable - &Aacute;rea de Negocio' : 'C. Contable'),
				'&Aacute;rea de Negocio',
				'Cargo',
				'Tipo de Salario',
				'Estado',
				(equipoid != '' ? '' : 'Acci&oacute;n'),
				'',
				'',
				'',
			],
		   	colModel:[
		   	    {name:'Codigo', index:'a', width: 50, sortable:false},
				{name:'Nombre', index:'nombre', width:70},
				{name:'Cedula', index:'cedula', width:40, hidden:(equipoid != '' ? true : false)},
				{name:'Fecha de Creacion', index:'fecha_creacion', width:50, hidden:(equipoid != '' ? true : false)},
				{name:'Centro Contable', index:'centro_contable', width: 60 },
				{name:'Area de Negocio', index:'departamento', width: 60, hidden:(equipoid != '' ? true : false)},
		   		{name:'Cargo', index:'cargo', width: 40},
		   		{name:'Tipo de Estado', index:'tipo_salario', width: 40, hidden:(equipoid != '' ? true : false)},
		   		{name:'Estado', index:'estado_id', width: 30 },
				{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
				{name:'options', index:'options', hidedlg:true, hidden: true},
				{name:'departamento_id', index:'departamento_id', hidedlg:true, hidden: true},
				{name:'ciclo_id', index:'ciclo_id', hidedlg:true, hidden: true},
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn,
		   		equipoid: equipoid,
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
		    sortname: 'nombre',
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
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron colaboradores.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
			tablaColaboradores.redimensionar();
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
		    var option = rowINFO["options"];
		    options = option.replace(/0000/gi, id);
		    
		    //evento para boton collapse sub-menu Accion Personal
		    opcionesModal.on('click', 'a[href="#collapse'+ id +'"]', function(){
		    	opcionesModal.find('#collapse'+ id ).collapse();
		    });

	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["Nombre"] +'');
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
		
		//Boton de Buscar Colaborador
		$(botones.buscar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			buscarCargo();
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
		
		//Boton de Liquidar Colaborador
		$(botones.liquidar).on("click", function(e){
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
					toggleColaborador({colaboradores: colaboradores, estado_id: 2});
				}
	        }
		});
		
		//Boton de Exportar Colaborador
		$(botones.exportar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			if($('#tabla').is(':visible') == true){
				
				//Exportar Seleccionados del jQgrid
				var ids = [];
					ids = grid_obj.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(ids.length > 0){
					
					$('#ids').val(ids);
			        $('form#exportarColaboradores').submit();
			        $('body').trigger('click');
				}
	        }
		});
		
		//Documentos Modal
		$(opcionesModal).on("click", botones.subirArchivo, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var colaborador_id = $(this).attr("data-id");
			var colaborador_uuid = $(this).attr("data-uuid");
			
			//Inicializar opciones del Modal
			documentosModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			
			var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		    scope.safeApply(function(){
		    	scope.campos.colaborador_id = colaborador_id;
		    	scope.campos.fecha_vencimiento = '';
		    });
			documentosModal.modal('show');
		});
		
		//Nueva Entrega de Inventario Modal
		$(opcionesModal).on("click", botones.entregaInventario, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var colaborador_id = $(this).attr("data-id");
			var colaborador_uuid = $(this).attr("data-uuid");
			
			var rowINFO = grid_obj.getRowData(colaborador_id);
		    var departamento_id = rowINFO["departamento_id"];
		    
			//Inicializar opciones del Modal
			formularioInventarioModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			formularioInventarioModal.attr("data-uuid", colaborador_uuid);
			formularioInventarioModal.attr("data-departamento-id", departamento_id);
			formularioInventarioModal.find('.modal-title').empty().append('Nueva Entrega de Inventario');
			formularioInventarioModal.find('input[id="campo[colaborador_id]"]').val(colaborador_id);
			formularioInventarioModal.find('#departamento_id').find('option[value="'+ departamento_id +'"]').prop('selected', 'selected');
			formularioInventarioModal.modal('show');
		});
		
		//Eliminar colaborador de equipo de trabajo
		$(opcionesModal).on("click", botones.eliminarDeEquipoTrabajo, function(e){
			e.preventDefault();

			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var colaborador_id = $(this).attr("data-id");
			
			$.ajax({
				url: phost() + 'talleres/ajax-eliminar-colaborador',
				data: $.extend({erptkn: tkn}, {equipo_id: equipoID, colaborador_id: colaborador_id}),
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json, textStatus, xhr) {

				//Check Session
				if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				
				if(xhr.status != 200){
					//mensaje error
					toastr.error('Hubo un error.');
				}
				
				//mensaje success
				//toastr.success(json.mensaje);
				
				//Recargar tabla jqgrid
				recargar();
				
			}).fail(function(xhr, textStatus, errorThrown) {
				//mensaje error
				toastr.error('Hubo un error.');
			});
		});
		
		//Formulario de Descuento Modal
		$(opcionesModal).on("click", botones.descuento, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var colaborador_id = $(this).attr("data-id");
			var colaborador_uuid = $(this).attr("data-uuid");
			
			var rowINFO = grid_obj.getRowData(colaborador_id);
		    var ciclo_id = rowINFO["ciclo_id"];
		    var nombre_colaborador = rowINFO["Nombre"];
		    
			//Inicializar opciones del Modal
		    formularioDescuentoModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			formularioDescuentoModal.attr("data-uuid", colaborador_uuid);
			formularioDescuentoModal.attr("data-ciclo-id", ciclo_id);
			formularioDescuentoModal.find('.modal-title').empty().append('Nuevo Descuento: '+ nombre_colaborador);
			formularioDescuentoModal.find('input[id="campo[colaborador_id]"]').val(colaborador_id);
			formularioDescuentoModal.find('#ciclo_id').attr("disabled", "disabled").find('option[value="'+ ciclo_id +'"]').prop('selected', 'selected');
			formularioDescuentoModal.modal('show');
		});
		
		$(opcionesModal).on("click", botones.accionesPersonal +' a', function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var formulario = $(this).attr("data-formulario");
			var colaborador_id = $(this).attr("data-colaborador");
			
			
			//Before using local storage, check browser support for localStorage and sessionStorage
			if(typeof(Storage) !== "undefined") {
				
				//Grabar colaborador id en local storage
				localStorage.setItem('colaborador_id', colaborador_id);
				
				//Cambiar seleccion del menu lateral
				localStorage.setItem('ml-selected', 'Acciones de Personal');
			}
			
			window.location = phost() + 'accion_personal/crear/' + formulario;
		});
		
		$(opcionesModal).on("click", botones.crearConsumo, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var colaborador_id = $(this).attr("data-id");
			var colaborador_uuid = $(this).attr("data-uuid");
			
			//Limpiar formulario
			crearConsumoForm.find('input[name*="colaborador_"]').remove();
			crearConsumoForm.append('<input type="hidden" name="colaborador_id" value="'+ colaborador_id +'" />');
			crearConsumoForm.append('<input type="hidden" name="colaborador_uuid" value="'+ colaborador_uuid +'" />');
			//Enviar formulario
			crearConsumoForm.submit();
	        $('body').trigger('click');
		});
                  
       $(opcionesModal).on("click", botones.crearPlantillaBtn, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var colaborador_id = $(this).attr("data-id");
                       
			//Limpiar formulario
			crearPlantillaForm.find('input[name*="colaborador_"]').remove();
			crearPlantillaForm.append('<input type="hidden" name="colaborador_id" value="'+ colaborador_id +'" />');
                        
			//Enviar formulario
			crearPlantillaForm.submit();
	        $('body').trigger('click');
		});
		
		
		$(opcionesModal).on("click", botones.crearDescuentoBtn, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			
			var colaborador_id = $(this).attr("data-id");
			
			//Limpiar formulario
			crearDescuentoForm.find('input[name*="colaborador_"]').remove();
			crearDescuentoForm.append('<input type="hidden" name="colaborador_id" value="'+ colaborador_id +'" />');
			//Enviar formulario
			crearDescuentoForm.submit();
	        $('body').trigger('click');
		});
	};
	
	//Reload al jQgrid
	var recargar = function(){
		
		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				nombre: '',
				cedula: '',
				fecha: '',
				cargo: '',
				codigo: '',
				estado_id: '',
				departamento_id: '',
				nombre_centro_contable: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};
	
	//Buscar cargo en jQgrid
	var buscarCargo = function(){

		var nombre 		= $('#nombre').val();
		var cedula 		= $('#cedula').val();
		var cargo 		= $('#cargo').val();
		var codigo 		= $('#codigo').val();
		var estado_id 	= $('#estado_id').val();
		var departamento_id = $('#departamento_id').val();
		var nombre_centro_contable = $('#nombre_centro_contable').val();
		var fecha_contratacion_desde = $('#fecha_contratacion_desde').val();
		var fecha_contratacion_hasta = $('#fecha_contratacion_hasta').val();

		if(nombre != "" || cedula != "" || cargo != "" || codigo != "" || estado_id != "" || departamento_id != "" || nombre_centro_contable != "" || fecha_contratacion_desde != "" || fecha_contratacion_hasta  != "")
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					nombre: nombre,
					cedula: cedula,
					cargo: cargo,
					codigo: codigo,
					estado_id: estado_id,
					departamento_id: departamento_id,
					nombre_centro_contable: nombre_centro_contable,
					fecha_contratacion_desde: fecha_contratacion_desde,
					fecha_contratacion_hasta: fecha_contratacion_hasta,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};
	
	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarColaboradorForm').find('input[type="text"]').prop("value", "");
	};
	
	//Funcion Ajax activar/desactivar colaborador
	var toggleColaborador = function(parametros){
		$.ajax({
			url: phost() + 'colaboradores/ajax-toggle-colaborador',
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		}).done(function(json, textStatus, xhr) {

			//Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}
			
			if(xhr.status != 200){
				//mensaje error
				toastr.error('Hubo un error al tratar de cambiar el estado.');
			}
			
			//mensaje success
			toastr.success(json.mensaje);
			
			//Recargar tabla jqgrid
			recargar();
			
		}).fail(function(xhr, textStatus, errorThrown) {
			//mensaje error
			toastr.error('Hubo un error al tratar de cambiar el estado.');
		});
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

tablaColaboradores.init();