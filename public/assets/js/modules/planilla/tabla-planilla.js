
$(function(){
	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		tablaPlanilla.redimensionar();
	});
});

 var tablaPlanilla = (function(){

	var url = 'planilla/ajax-listar-planilla';
	var url_editar = 'comisiones/ajax-listar-editar-monto';
	var grid_id = "tablaPlanillaGrid";
	var grid_obj = $("#tablaPlanillaGrid");
	var opcionesModal = $('#opcionesModal');
	var formulario = $('#buscarPlanillaForm');

	var botones = {
		opciones: ".viewOptions",
		editar: ".editarComisionBtn",
		comentarios: ".agregarBtnComentario",
		eliminar: "#EliminarBtnComisionColaborador",
		anular: "#confirmAnular",
		cerrarPlanilla: "#cerrarPlanilla",
		pagarPlanilla: "#pagarPlanilla",
 		exportar: "#ExportarBtnComision",
		buscar: "#searchBtn",
		limpiar: "#clearBtn"
	};

 		var lastsel;

		var tabla = function(){
	 		grid_obj.jqGrid({
			   	url: phost() + url,
			   	datatype: "json",
			   	colNames:[
			   	    'id',
					'N&uacute;mero',
					'Tipo de planilla',
	 				'Fecha de pago',
	 				'Rango de fechas',
	 				'Centro Contable',
 	 				'Colaboradores',
 	 				'Estado',
 	 				'',
	 				'',
 	 			],
			   	colModel:[
			   	    {name:'id', index:'id', width:50, hidden: true},
	 				{name:'Numero', index:'secuencial',sortable:true, width:50},
	 				{name:'Tipo de planilla', index:'tipo_planilla', width:70},
	 				{name:'Fecha de Pago', index:'fecha_pago', width:70},
	 				{name:'Rango de Fechas', index:'rango',  sortable:false,width: 70},
	 		   		{name:'Centro Contable', index:'centro',  sortable:false,width: 70},
	 		   		{name:'Colaboradores', index:'colaboradores', sortable:false,width: 50 },
	 		   		{name:'Estado', index:'estado',  width: 50},
 	 		   		{name:'opciones', index:'accion',  width: 50},
 	 		   		{name:'options', index:'options', hidedlg:true, hidden: true},
  	  		   	],
				mtype: "POST",
			   	postData: {
//			   		comision_id: comision_id,
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
			    sortname: 'secuencial',
			    sortorder: "DESC",

			    onSelectRow: function(id){
 			    	var parameter = {erptkn: tkn};
					if(id && id!==lastsel){
						grid_obj.jqGrid('restoreRow',lastsel);
						grid_obj.jqGrid('editRow', id, true, false,  false , false, parameter);
 						lastsel=id;
					}
			 	 },
  				editurl: phost() + url_editar,
 			    beforeProcessing: function(data, status, xhr){
			    	//Check Session
					if( $.isEmptyObject(data.session) == false){
						window.location = phost() + "login?expired";
					}
			    },

			    loadBeforeSend: function () {//propiedadesGrid_cb
			    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
			    	$(this).closest("div.ui-jqgrid-view").find("#tablaPlanillaGrid_cb, #jqgh_tablaPlanillaGrid_link").css("text-align", "center");
	 		    },
	  		    beforeRequest: function(data, status, xhr){},
				loadComplete: function(data){
					 //$("#cb_" + this.id).click();
					//check if isset data
					if( data['total'] == 0 ){
						$('#boton_guardar').hide();
						$('#gbox_'+ grid_id).hide();
						$('#'+ grid_id +'NoRecords').empty().append('No se han encontrado planillas').css({"color":"#868686","padding":"30px 0 0"}).show();
					}
					else{
						$('#boton_guardar').show();
						$('#'+ grid_id +'NoRecords').hide();
						$('#gbox_'+ grid_id).show();
					}
				},
	 		});
		};

 	//Inicializacion de Campos de Busqueda
	var campos = function(){
		//Init Bootstrap Calendar Plugin
		var fecha1 = $(formulario).find('#fecha1');
		var fecha2 = $(formulario).find('#fecha2');
		var centro_contable_id = $(formulario).find('#centro_contable_id');
		var campo_departamento_id = $(formulario).find('#departamento_id');
 		var estado_id = $(formulario).find('#estado_id');

 		fecha1.daterangepicker({
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
 		fecha2.daterangepicker({
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
 		fecha1.val("");
 		fecha2.val("");

 		$(centro_contable_id).chosen({width: '100%'}).trigger('chosen:updated');
 		$(estado_id).chosen({width: '100%'}).trigger('chosen:updated');

  		$(campo_departamento_id).prop("disabled",true);
  		$(campo_departamento_id).chosen({width: '100%'}).trigger('chosen:updated');

		$(formulario).on("change", '#centro_contable_id', function(e){
 			 e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var centro_id = $(this).find('option:selected').val();
 			var campo_departamento = $(formulario).find('#departamento_id');

 			//Mensaje de Loading
			$('.departamento-loader').remove();
			$(campo_departamento).closest('div').append('<div class="departamento-loader"><small class="text-success">Buscando &Aacute;reas de Negocio... <i class="fa fa-circle-o-notch fa-spin"></i></small></div>');

			$(campo_departamento_id).prop("disabled",true);
 	  		$(campo_departamento_id).chosen({width: '100%'}).trigger('chosen:updated');

 			popular_departamento({centro_id: centro_id}).done(function(json){

     				if( $.isEmptyObject(json.session) == false){
    					window.location = phost() + "login?expired";
    				}
     				$(campo_departamento).empty();
      				$(campo_departamento).empty().append('<option value="">Seleccione</option>').removeAttr('disabled');

     				if($.isEmptyObject(json['result']) == true){

     					$('.departamento-loader').remove();


    					return false;
    				}else{
         				$.each(json['result'], function(i, result){
           					$(campo_departamento).append('<option value="'+ result.id +'">'+ result.nombre +'</option>');
        				});
    				}
     				$('.departamento-loader').remove();
     				$(campo_departamento_id).prop("disabled",false);
     				$(campo_departamento_id).chosen({width: '100%'}).trigger('chosen:updated');
   			});

 		});
   	};

   	/**
	 * Popular dropdown departamento/area de negocio
	 * segun centro contable
	 * seleccionado.
	 */
	var popular_departamento = function(parametros)
	{
		if(parametros == ""){
			return false;
		}

		return $.ajax({
			url: phost() + 'colaboradores/ajax-lista-departamentos-asociado-centro',
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
	};
   	$(botones.exportar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		exportarTabla();
	});
	$(botones.eliminar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		 removerColaboradoresComision();
	});
	$(botones.buscar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
 		buscarPlanilla();
	});

	$(botones.limpiar).on("click", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
	 	limpiarCampos();
	 	recargar();
	});

 	opcionesModal.on("click", botones.editar, function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
	});


 	//Limpiar campos de busqueda
	var limpiarCampos = function(){

		formulario.find('input[type="text"]').prop("value", "");
		formulario.find('select').val('');

		$(formulario).find('#departamento_id').empty();
		$(formulario).find('#departamento_id').prop("disabled",true);

  		$(formulario).find('#centro_contable_id, #departamento_id, #estado_id').chosen({width: '100%'}).trigger('chosen:updated');
	};
	//Buscar cargo en jQgrid
	var buscarPlanilla = function(){
 		var id_codigo 			= $('#id_codigo').val();
 		var fecha1 				= $('#fecha1').val();
		var fecha2 				= $('#fecha2').val();
		var centro_contable_id 	= $('#centro_contable_id').val();
		var estado_id 			= $('#estado_id').val();
 		var area_negocio 			= $('#departamento_id').val();

		if(id_codigo != "" ||  centro_contable_id !=''  || estado_id !='' || area_negocio!='' || (fecha1!='' && fecha2!=''))
		{
 			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					id_codigo: id_codigo,
					fecha1: fecha1,
					fecha2: fecha2,
					centro_contable_id: centro_contable_id,
					estado_id: estado_id,
					area_negocio: area_negocio,
 					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};

	var removerColaboradoresComision = function(){

			var colaboradoresComision = [];

			colaboradoresComision = grid_obj.jqGrid('getGridParam','selarrrow');


			if(colaboradoresComision.length==0){
				return false;
			}
		 var mensaje = (colaboradoresComision.length > 1)?'Esta seguro que desea eliminar estos Colaboradores de esta comisi&oacute;n?':'Esta seguro que desea eliminar este colaborador?';

		 var footer_buttons = ['<div class="row">',
			   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
			   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
			   '</div>',
			   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
			   		'<button id="eliminarColaboradorBtn" class="btn btn-w-m btn-success btn-block" type="button">Confirmar</button>',
			   '</div>',
			   '</div>'
			].join('\n');
		 opcionesModal.find('.modal-title').empty().append('Confirme');
		 opcionesModal.find('.modal-body').empty().append(mensaje);
		 opcionesModal.find('.modal-footer').empty().append(footer_buttons);
		 opcionesModal.modal('show');
	};
	$('#opcionesModal').on("click", "#eliminarColaboradorBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var colaboradoresComision = [];

		colaboradoresComision = grid_obj.jqGrid('getGridParam','selarrrow');


		$.ajax({
			url: phost() + 'comisiones/ajax-eliminar-colaborador',
			data: {
				erptkn: tkn,
				colaboradoresComision: colaboradoresComision
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

 				opcionesModal.modal('hide');
				recargar();

		});

	    //Ocultar ventana
	    $('#opcionesModal').modal('hide');
	});

	opcionesModal.on("click", botones.cerrarPlanilla, function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		var id_planilla = $(this).attr('data-id');

	    //Init boton de opciones
		opcionesModal.find('.modal-title').empty().append('Confirme');
		opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea cerrar esta planilla?');
		opcionesModal.find('.modal-footer')
			.empty()
			.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
			.append('<button id="cerrarPlanillaAccion" data-id="'+ id_planilla +'" class="btn btn-w-m btn-primary" type="button">Cerrar</button>');
	 });

	 opcionesModal.on("click", botones.pagarPlanilla, function(e){
 		e.preventDefault();
 		e.returnValue=false;
 		e.stopPropagation();
 		var id_planilla = $(this).attr('data-id');
 		var salario = $(this).attr('data-salario');

 	    //Init boton de opciones
 		opcionesModal.find('.modal-title').empty().append('Confirme');
 		opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea pagar esta planilla?'+"</BR></BR>");
 		opcionesModal.find('.modal-body').append('<b>Monto Total:</b> '+salario);
 		opcionesModal.find('.modal-footer')
 			.empty()
 			.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
 			.append('<button id="pagarPlanillaAccion" data-id="'+ id_planilla +'" class="btn btn-w-m btn-primary" type="button">Pagar</button>');
 	 });

 	opcionesModal.on("click", botones.anular, function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		var id_planilla = $(this).attr('data-id');

	    //Init boton de opciones
		opcionesModal.find('.modal-title').empty().append('Confirme');
		opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea anular esta planilla?');
		opcionesModal.find('.modal-footer')
			.empty()
			.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
			.append('<button id="anularPlanilla" data-id="'+ id_planilla +'" class="btn btn-w-m btn-primary" type="button">Anular</button>');
	 });

  	opcionesModal.on("click", botones.comentarios, function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
 	 });
 	//Opcion: pagar Planlla desde accion

opcionesModal.on("click", "#pagarPlanillaAccion", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

				var id_planilla = $(this).attr('data-id');

				$("div.modal-content").find('#pagarPlanillaAccion').attr('disabled', true);

				$.ajax({
								url: phost() + 'planilla/ajax-crear-pagos-planilla',
								data: {
									planilla_id: id_planilla,
									erptkn: tkn,
								},
								type: "POST",
								dataType: "json",
								cache: false,
				}).done(function(json) {

							if( $.isEmptyObject(json.session) == false){
													 window.location = phost() + "login?expired";
							}

							if(json.response == true){
											 toastr.success(json.mensaje);
											 opcionesModal.modal('hide');
											 recargar();
							}
							 else{
											 toastr.error(json.mensaje);
											 opcionesModal.modal('hide');
							 }
						 });

});

	//Opcion: cerrar Planlla desde accion
	opcionesModal.on("click", "#cerrarPlanillaAccion", function(e){
				 	e.preventDefault();
			 		e.returnValue=false;
			 		e.stopPropagation();

	 				var id_planilla = $(this).attr('data-id');

					$("div.modal-content").find('#closeModal').attr('disabled', true);
					$("div.modal-content").find('#cerrarPlanillaAccion').attr('disabled', true);
					$("div.modal-content").find('#cerrarPlanillaAccion').text("Un momento...");
					$.ajax({
				          url: phost() + 'planilla/ajax-pagar-planilla',
				          data: {
				          	planilla_id: id_planilla,
				  					erptkn: tkn,
				  	 			},
				          type: "POST",
				          dataType: "json",
				          cache: false,
				  }).done(function(json) {

				        if( $.isEmptyObject(json.session) == false){
				                     window.location = phost() + "login?expired";
				        }

				        if(json.response == true){
				                 toastr.success(json.mensaje);
												 opcionesModal.modal('hide');
												 recargar();
								}
								 else{
				                 toastr.error(json.mensaje);
												 opcionesModal.modal('hide');
                 }
							 }); 

	});

	 	//Opcion: Desactivar Usuario
		opcionesModal.on("click", "#anularPlanilla", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var id_planilla = $(this).attr('data-id');
			   	//Guardar Formulario
			$.ajax({
				url: phost() + 'planilla/ajax-anular-planilla',
				data: {
					id_planilla: id_planilla,
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

	//Reacarga la Tabla Principal de Comisiones
	var recargar = function(){
		//Reload Grid
 		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				id_codigo: '',
				fecha1: '',
				fecha2: '',
				centro_contable_id: '',
				area_negocio:'',
				estado_id: '',
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

			    console.log(rowINFO)

		 	    //Init Modal
			    opcionesModal.find('.modal-title').empty().append('Opciones: '+rowINFO['Numero']);
			    opcionesModal.find('.modal-body').empty().append(options);
			    opcionesModal.find('.modal-footer').empty();
			    opcionesModal.modal('show');
			});


			opcionesModal.on("click", botones.comentarios, function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
				console.log("PANAMA PRIMERO..");
			});

	};

 	var actualizar_chosen = function(){
  		$(formulario).find('.chosen-select').chosen({
			width: '100%',
        }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
        	$('#id_nombre').closest('div.table-responsive').css("overflow", "visible");
        }).on('chosen:hiding_dropdown', function(evt, params) {
        	$('#id_nombre').closest('div.table-responsive').css({'overflow-x':'auto !important'});
        });
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
		},
		/*lista_departamentos: function(parametros){


			return ajax('colaboradores/ajax-lista-departamentos-asociado-centros', parametros);
		}*/
	};
})();

//
tablaPlanilla.init();

function remover() {
	//Exportar Seleccionados del jQgrid
	var comisiones = [];

	comisiones = $("#tablaPlanillaGrid").jqGrid('getGridParam','selarrrow');

	var obj = new Object();
	obj.count = comisiones.length;

	if(obj.count) {

		obj.items = new Array();

		for(elem in comisiones) {
			//console.log(proyectos[elem]);
			var comision = $("#tablaPlanillaGrid").getRowData(comisiones[elem]);

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
}

function exportarTabla() {
	//Exportar Seleccionados del jQgrid
	var planillas = [];

	planillas = $("#tablaPlanillaGrid").jqGrid('getGridParam','selarrrow');

	var obj = new Object();
	obj.count = planillas.length;

	if(obj.count) {

		obj.items = new Array();

		for(elem in planillas) {
			//console.log(proyectos[elem]);
			var planilla = $("#tablaPlanillaGrid").getRowData(planillas[elem]);

			//Remove objects from associative array
			delete planilla['id'];
			delete planilla['opciones'];
			delete planilla['options'];
 			delete planilla['link'];

			//Push to array
			obj.items.push(planilla);
		}

		var json = JSON.stringify(obj);
		var csvUrl = JSONToCSVConvertor(json);
		var filename = 'planilla_'+ Date.now() +'.csv';

		//Ejecutar funcion para descargar archivo
		downloadURL(csvUrl, filename);

		$('body').trigger('click');
	}
}
