
$(function(){
	//Al redimensionar ventana
	$(window).resizeEnd(function() {
		tablaDecimo.redimensionar();
	});
});

 var tablaDecimo = (function(){

	var url = 'planilla/ajax-listar-decimo';
	var grid_id = "tablaDecimoGrid";
	var grid_obj = $("#tablaDecimoGrid");
	var opcionesModal = $('#opcionesModal');
    var formulario = $('#crearPlanilla');
	var planillaRegularModal = $('#pagoEspecialModal');

	var botones = {
		opciones: ".viewOptions",
 		eliminar: "#EliminarBtnComisionColaborador",
		cerrarPlanillaModal: "#pagarPlanilla"
	};

 		var lastsel;

		var tabla = function(){

 	 		grid_obj.jqGrid({
			   	url: phost() + url,
			   	datatype: "json",
			   	colNames:[
			   	    'id',
					'No. de colaborador',
					'Nombre',
	 				'Centro contable',
	 				'Departamento',
	 				'Cargo',
  	 				'',
	 				'',
 	 			],
			   	colModel:[
			   	    {name:'id', index:'id', width:50, hidden: true},
	 				{name:'No. de Colaborador', index:'numero',sortable:true, width:50},
	 				{name:'Nombre', index:'nombre', width:70},
	 				{name:'Centro Contable', index:'centro_contable', width:70},
	 				{name:'Departamento', index:'departamento',  sortable:false,width: 70},
	 		   		{name:'Cargo', index:'cargo',  sortable:false,width: 70},
 	 		   		{name:'opciones', index:'accion',  width: 50},
 	 		   		{name:'options', index:'options', hidedlg:true, hidden: true},
  	  		   	],
				mtype: "POST",
			   	postData: {
			   		planilla_id: planilla_id,
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
			    sortname: 'id',
			    sortorder: "DESC",

			    onSelectRow: function(id){
 			    	var parameter = {erptkn: tkn};
					if(id && id!==lastsel){
						grid_obj.jqGrid('restoreRow',lastsel);
						grid_obj.jqGrid('editRow', id, true, false,  false , false, parameter);
 						lastsel=id;
					}
			 	 },
  			    beforeProcessing: function(data, status, xhr){
			    	//Check Session
					if( $.isEmptyObject(data.session) == false){
						window.location = phost() + "login?expired";
					}
			    },

			    loadBeforeSend: function () {//propiedadesGrid_cb
			    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
			    	$(this).closest("div.ui-jqgrid-view").find("#tablaDecimoGrid_cb, #jqgh_tablaDecimoGrid_link").css("text-align", "center");
	 		    },
	  		    beforeRequest: function(data, status, xhr){},
				loadComplete: function(data){
 					if( data['total'] == 0 ){
						$('#boton_guardar').hide();
						$('#gbox_'+ grid_id).hide();
						$('#'+ grid_id +'NoRecords').empty().append('No se han encontrado colaboradores').css({"color":"#868686","padding":"30px 0 0"}).show();
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
			//var estado_planilla = estado_planilla;
			var fecha1 = $(formulario).find('#rango_fecha1');
			var fecha2 = $(formulario).find('#rango_fecha2');


			fecha1.daterangepicker({
		      	  singleDatePicker: true,
		          showDropdowns: true,
		          opens: "left",
		          startDate: rango1,
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
		          startDate: rango2,
		          locale: {
		          	 format: 'DD/MM/YYYY',
		          	 applyLabel: 'Seleccionar',
		             cancelLabel: 'Cancelar',
		          	 daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
		             monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		             firstDay: 1
		          }
		      });



			/*$.each(JSON.parse(acumulados), function(i,acumulado) {
	  			 $(formulario).find('select[name="acumulados[acumulados][]"] option[value="'+acumulado.acumulado_id +'"]') .prop('selected', 'selected');
			});
	  			$.each(JSON.parse(deducciones), function(i,deduccion) {
					 $(formulario).find('select[name="deducciones[deducciones][]"] option[value="'+deduccion.deduccion_id +'"]') .prop('selected', 'selected');
			    });

*/

			$(formulario).find('#tipo_id').attr( "disabled", true );
	//		$(formulario).find('#ciclo_id, #pasivo_id,select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').chosen({width: '100%'}).trigger('chosen:updated');

	 		if(permiso_editar == 0 )
			{
			    	//$(formulario).find('select, input, button, textarea').prop("disabled", "disabled");
			    	//$(formulario).find('select[name="pasivo_id"], select[name="ciclo_id"], select[name="deducciones[deducciones][]"], select[name="acumulados[acumulados][]"]').chosen({width: '100%'}).trigger('chosen:updated');
			}

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


  	//Limpiar campos de busqueda
	var limpiarCampos = function(){

		formulario.find('input[type="text"]').prop("value", "");
		formulario.find('select').val('');

		$(formulario).find('#departamento_id').empty();
		$(formulario).find('#departamento_id').prop("disabled",true);

  		$(formulario).find('#centro_contable_id, #departamento_id, #estado_id').chosen({width: '100%'}).trigger('chosen:updated');
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



 	/*opcionesModal.on("click", botones.anular, function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		alert("d");
		var id_planilla = $(this).attr('data-id');

	    //Init boton de opciones
		opcionesModal.find('.modal-title').empty().append('Confirme');
		opcionesModal.find('.modal-body').empty().append('Est&aacute; seguro que desea anular esta planilla?');
		opcionesModal.find('.modal-footer')
			.empty()
			.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
			.append('<button id="anularPlanilla" data-id="'+ id_planilla +'" class="btn btn-w-m btn-primary" type="button">Anular</button>');
	 });
	*/
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
			    opcionesModal.find('.modal-title').empty().append('Opciones: '+rowINFO['Nombre']);
			    opcionesModal.find('.modal-body').empty().append(options);
			    opcionesModal.find('.modal-footer').empty();
			    opcionesModal.modal('show');
			});


			$(botones.cerrarPlanillaModal).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

		 		opcionesModal.modal('hide');

		 		$.ajax({
		              url: phost() + 'planilla/ajax-detalles-pago-decimo',
		              data: {
		            	  	planilla_id: planilla_id,
		            	  	//cantidad_semanas:cantidad_semanas,
							erptkn: tkn,
			 			},
		              type: "POST",
		              dataType: "json",
		              cache: false,
		          }).done(function(json) {
		              //Check Session

		              if( $.isEmptyObject(json.session) == false){
		                  window.location = phost() + "login?expired";
		              }
		              if(json.response == true){
		            	  planillaRegularModal.find('#total_colaboradores').text(json.cantidad_colaboradores);
		            	  planillaRegularModal.find('#salario_bruto').text(json.salario_bruto);

		            	  planillaRegularModal.find('#salario_neto').text(json.salario_neto);
		            	  planillaRegularModal.find('#salario_neto_porcentaje').text(json.salario_neto_porcentaje);
		            	  planillaRegularModal.find('#salario_neto_progress_bar').width(json.salario_neto_progress_bar);


		            	  planillaRegularModal.find('#deducciones').text(json.deducciones);
		            	  planillaRegularModal.find('#deducciones_porcentaje').text(json.deducciones_porcentaje);
		            	  planillaRegularModal.find('#deducciones_progress_bar').width(json.deducciones_progress_bar);
		               }else{
		                  toastr.error(json.mensaje);
		               }

		          });

				planillaRegularModal.modal('show');

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
tablaDecimo.init();

function remover() {
	//Exportar Seleccionados del jQgrid
	var comisiones = [];

	comisiones = $("#tablaDecimoGrid").jqGrid('getGridParam','selarrrow');

	var obj = new Object();
	obj.count = comisiones.length;

	if(obj.count) {

		obj.items = new Array();

		for(elem in comisiones) {
			//console.log(proyectos[elem]);
			var comision = $("#tablaDecimoGrid").getRowData(comisiones[elem]);

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

	planillas = $("#tablaDecimoGrid").jqGrid('getGridParam','selarrrow');

	var obj = new Object();
	obj.count = planillas.length;

	if(obj.count) {

		obj.items = new Array();

		for(elem in planillas) {
			//console.log(proyectos[elem]);
			var planilla = $("#tablaDecimoGrid").getRowData(planillas[elem]);

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
