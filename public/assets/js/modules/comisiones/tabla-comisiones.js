$(function(){
	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		tablaComisiones.redimensionar();
	});
});

//Modulo Tabla de Comisiones
var tablaComisiones = (function(){

	var url = 'comisiones/ajax-listar-comisiones';
	var grid_id = "tablaComisionesGrid";
	var grid_obj = $("#tablaComisionesGrid");
	var opcionesModal = $('#opcionesModal');
	var formulario = $('#buscarComisionForm');
	var campo_centro = $(formulario).find('#id_centro_contable');
	var campo_estado = $(formulario).find('#id_estado');

	var botones = {
		opciones: ".viewOptions",
 		anular: "#confirmAnular",
		pagarPagoExtraordinario: "#pagarPagoExtraordinario",
 		exportar: "#ExportarBtnComision",
		buscar: "#searchBtn",
		limpiar: "#limpiarComisionBtn"
	};

 	var tabla = function(){

 		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'No. de pago extraordinario ',
				'Centro Contable',
				'Detalle',
				'Fecha programada de pago',
				'Fecha de pago',
				'Colaboradores',
				'Monto total',
				'Estado',
				'',
				''
			],
		   	colModel:[
				{name:'Codigo', index:'nombre', width:50},
				{name:'Centro contable', index:'centro_contable', width:50},
				{name:'Detalle', index:'Descripcion', width:70},
				{name:'Fecha programada de pago', index:'fecha_programada_pago', width:50},
				{name:'Fecha de pago', index:'fecha_aplicar', width:50},
		   		{name:'Colaboradores', index:'colaboradores',  sortable:false,width: 50},
 		   		{name:'Monto total', index:'monto', width: 50 },
		   		{name:'Estado', index:'estado_id', width: 50 },
				{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
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
		    multiselect: true,
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
		    	$(this).closest("div.ui-jqgrid-view").find("#tablaComisionesGrid_cb, #jqgh_tablaComisionesGrid_link").css("text-align", "center");
 		    },
  		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){

				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron pagos extraordinarios.').css({"color":"#868686","padding":"30px 0 0"}).show();
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

 	//Inicializacion de Campos de Busqueda
	var campos = function(){
		//Init Bootstrap Calendar Plugin
		$(formulario).find('#fecha1').daterangepicker({
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
		$(formulario).find('#fecha2').daterangepicker({
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


	    $(campo_centro).chosen({width: '100%'}).trigger('chosen:updated');

   	};


 	var limpiarCampos = function(){
 		formulario.find('input[type="text"]').prop("value", "");
		formulario.find('select').val('');
		formulario.find('#id_centro_contable').chosen({width: '100%'}).trigger('chosen:updated');
  	};
	//Buscar cargo en jQgrid
	var buscarComision = function(){
  		var id_centro_contable 	= $('#id_centro_contable').val();
  		var estado_id 			= $('#estado_id').val();
  		var fecha1 				= $('#fecha1').val();
   		var fecha2 				= $('#fecha2').val();

		if(  id_centro_contable != "" != "" || estado_id != ""   || ( fecha1 != "" && fecha2 != "" ))
		{
 			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
 					centro_contable: id_centro_contable,
 					estado_id: estado_id,
 					fecha1: fecha1,
 					fecha2: fecha2,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};

	$(botones.exportar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		exportarTabla();
	});

	$(botones.buscar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
 		buscarComision();
	});

	$(botones.limpiar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		limpiarCampos();
		recargar();
	});



	var recargar = function(){
		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
 				centro_contable: '',
 				estado_id: '',
 				fecha1: '',
 				fecha2: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');

	};

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
			    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["Codigo"] +'');
			    opcionesModal.find('.modal-body').empty().append(options);
			    opcionesModal.find('.modal-footer').empty();
			    opcionesModal.modal('show');
			});



		 	opcionesModal.on("click", botones.pagarPagoExtraordinario, function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				var id_comision = $(this).attr('data-id');
				var salario = $(this).attr('data-salario');
				var fecha_pago = $(this).attr('data-fecha');
				var disabled = mensaje_pendiente = '';


						//$("#pagarExtraordinario").prop("disabled",true);

				//Init boton de opciones
			opcionesModal.find('.modal-title').empty().append('Confirme');
			opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea pagar este pago extraordinario?'+"</BR></BR>");
			opcionesModal.find('.modal-body').append('<b>Monto Total:</b> '+salario);

			if(fecha_pago == 'Pendiente'){
				disabled = 'disabled';
 				opcionesModal.find('.modal-body').append('</br></br><span style="color:red;">No existe fecha programada</span> ');
			}

 			opcionesModal.find('.modal-footer')
 					.empty()
					.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
					.append('<button id="pagarExtraordinario" '+disabled+' data-id="'+ id_comision +'"  class="btn btn-w-m btn-primary" type="button">Por Pagar</button>');
			});

			//Opcion: Desactivar Usuario
		opcionesModal.on("click", "#pagarExtraordinario", function(e){
					e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

				var comision_id = $(this).attr('data-id');
						//Guardar Formulario
				$.ajax({
					url: phost() + 'comisiones/ajax-por-aprobar',
					data: {
						comision_id: comision_id,
						erptkn: tkn
					},
					type: "POST",
					dataType: "json",
					cache: false,
				}).done(function(json) {

					 //Check Session
					if( $.isEmptyObject(json.session) == false){
						window.location = phost() + "login?expired";
					}
					//If json object is empty.
					if($.isEmptyObject(json) == true){
						return false;
					}
					//Mostrar Mensaje
					if(json.response == "" || json.response == undefined || json.response == 0){
						toastr.error(json.mensaje);
					}else{
						toastr.success(json.mensaje);
					}
					//Ocultar Modal
					opcionesModal.modal('hide');
					recargar();
				});
		});

		 	opcionesModal.on("click", botones.anular, function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				var id_comision = $(this).attr('data-id');

			    //Init boton de opciones
				opcionesModal.find('.modal-title').empty().append('Confirme');
				opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea anular esta comisi&oacute;n?');
				opcionesModal.find('.modal-footer')
					.empty()
					.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
					.append('<button id="anularComision" data-id="'+ id_comision +'" class="btn btn-w-m btn-primary" type="button">Anular</button>');
			});

			 	//Opcion: Desactivar Usuario
			opcionesModal.on("click", "#anularComision", function(e){
						e.preventDefault();
					e.returnValue=false;
					e.stopPropagation();

					var id_comision = $(this).attr('data-id');
					   	//Guardar Formulario
					$.ajax({
						url: phost() + 'comisiones/ajax-anular-comision',
						data: {
							id_comision: id_comision,
			                erptkn: tkn
						},
						type: "POST",
						dataType: "json",
						cache: false,
					}).done(function(json) {

						 //Check Session
						if( $.isEmptyObject(json.session) == false){
							window.location = phost() + "login?expired";
						}
						//If json object is empty.
						if($.isEmptyObject(json) == true){
							return false;
						}
		 				//Mostrar Mensaje
						if(json.response == "" || json.response == undefined || json.response == 0){
							toastr.error(json.mensaje);
						}else{
							toastr.success(json.mensaje);
						}
		 				//Ocultar Modal
						opcionesModal.modal('hide');
						recargar();
					});
			});
	};

	var exportarTabla = function(){

 		var comisiones = [];

		comisiones = $("#tablaComisionesGrid").jqGrid('getGridParam','selarrrow');

		var obj = new Object();
		obj.count = comisiones.length;

		if(obj.count) {
 			obj.items = new Array();
 			for(elem in comisiones) {
				//console.log(proyectos[elem]);
				var comision = $("#tablaComisionesGrid").getRowData(comisiones[elem]);

				//Remove objects from associative array
				delete comision['options'];
	 			delete comision['link'];

				//Push to array
				obj.items.push(comision);
			}

			var json = JSON.stringify(obj);
			var csvUrl = JSONToCSVConvertor(json);
			var filename = 'comision_'+ Date.now() +'.csv';

			//Ejecutar funcion para descargar archivo
			downloadURL(csvUrl, filename);

			$('body').trigger('click');
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

tablaComisiones.init();
