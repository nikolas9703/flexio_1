//Modulo Tabla de Entrega de Inventario
var tablaDescuentos = (function(){

	//var evaluacion_id = '';
	var url = 'descuentos/ajax-listar';
	var grid_id = "descuentosgrid";
	var grid_obj = $("#descuentosgrid");
	var opcionesModal = $('#opcionesModal');
	var formularioBusqueda = '#buscarDescuentosForm';
	var formularioInventarioModal = $('#entregaInventarioModal');

	var botones = {
		opciones: ".viewOptions",
		editar: ".editarInventarioBtn",
		reemplazar: ".reemplazarInventarioBtn",
		descargar: ".descargarInventarioBtn",
		detalle: ".verDetalleDescuento",
        exportar: "#exportarDescuentoLnk",
		limpiar: "#clearBtn",
		buscar: "#searchBtn",
		descargar: ".descargarAdjuntoBtn",
	};

	var tabla = function(){

		var colaboradorid = "";
		if(typeof colaborador_id != "undefined"){
			colaboradorid = $.parseJSON(colaborador_id);
			ocultar_opciones = true;
		}

		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
				'No.',
				'Tipo de descuento',
				'Acreedor',
				'Colaborador',
				'C&eacute;dula',
				'Fecha de Inicio',
				'Monto por Ciclo',
				'Estado',
				'',
                '',
                '',
				'',
			],
		   	colModel:[
				{name:'numero', index:'numero', width:20, align:'left'},
				{name:'tipo_descuento', index:'tipo_descuento', width:30},
				{name:'acreedor', index:'acreedor', width:35},
		   		{name:'colaborador', index:'colaborador', width: 35},
                {name:'cedula', index:'cedula', width: 25},
		   		{name:'fecha_inicio', index:'fecha_inicio', width: 35},
		   		{name:'monto_ciclo', index:'monto_ciclo', width: 25},
		   		{name:'estado', index:'estado', align:'center', width: 35},
				{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
				{name:'options', index:'options', hidedlg:true, hidden: true},
				{name:'archivo_ruta', index:'archivo_ruta', hidedlg:true, hidden: true},
				{name:'archivo_nombre', index:'archivo_nombre', hidedlg:true, hidden: true},
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn,
		   		colaborador_id: colaboradorid
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
				sortorder: "DESC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
		        $(this).closest("div.ui-jqgrid-view").find("#descuentosgrid").css("text-align", "center");},
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){

				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron descuentos.').css({"color":"#868686","padding":"30px 0 0"}).show();
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

         //Boton de Exportar Descuento
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
                                        console.log(ids);
			        $('form#exportarDescuentos').submit();
			        $('body').trigger('click');
				}
	        }
		});

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
		    opcionesModal.find('.modal-title').empty().append('Opciones: ' + rowINFO["numero"]);
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});

		//Ver Detalle desde Colaboradores
		$(opcionesModal).on("click", botones.detalle, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Cerrar modal de opciones
			opcionesModal.modal('hide');

			var descuento_id = $(this).attr("data-id");

			//Before using local storage, check browser support for localStorage and sessionStorage
			if(typeof(Storage) !== "undefined") {

				//Grabar id de la accion
				localStorage.setItem('descuento_id', descuento_id);
			}

			//Verificar si existe o no variable
			//colaborador_id
			if(typeof colaborador_id != 'undefined'){

				//Verificar si el formulario esta siendo usado desde
				//Ver Detalle de Colaborador
				if(window.location.href.match(/(colaboradores)/g)){

					var scope = angular.element('[ng-controller="formularioDescuentoController"]').scope();
					scope.popularFormulario();

				}
			}
		});

		//Boton de Descargar Evaluacion
		opcionesModal.on("click", botones.descargar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var descuento_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(descuento_id);

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
		   // var departamento_id = rowINFO["departamento_id"];

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

		//jQuery Daterange
		$(formularioBusqueda).find("#fecha_desc_desde").datepicker({
			defaultDate: "+1w",
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
				$('#buscarAccionPersonalForm').find("#fecha_ap_hasta").datepicker( "option", "minDate", selectedDate );
			}
		});
		$(formularioBusqueda).find("#fecha_desc_hasta").datepicker({
			defaultDate: "+1w",
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
				$(formularioBusqueda).find("#fecha_desc_desde").datepicker( "option", "maxDate", selectedDate );
		    }
		});
	};

	//Boton de Buscar Evaluacion
	$(formularioBusqueda).on("click", botones.buscar, function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		buscarDescuentos();
	});

	//Boton de Reiniciar jQgrid
	$(formularioBusqueda).on("click", botones.limpiar, function(e){
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
				numero: '',
				cedula: '',
				tipo_descuento: '',
				nombre_colaborador: '',
				acreedor: '',
				estado_id: '',
				fecha_desde: '',
				fecha_hasta: '',
			}
		}).trigger('reloadGrid');

	};

	//Buscar Entrega de Inventario en jQgrid
	var buscarDescuentos = function(){

           // console.log("llegaste");

		var numero 	=               $(formularioBusqueda).find('#numero').val();
		var cedula              = $(formularioBusqueda).find('#cedula').val();
		var tipo_descuento 	= $(formularioBusqueda).find('#tipo_descuento').val();
		var nombre_colaborador              = $(formularioBusqueda).find('#nombre_colaborador').val();
		var acreedor             = $(formularioBusqueda).find('#acreedor').val();
		var estado_id            = $(formularioBusqueda).find('#estado_id').val();
		var fecha_desde 		= $(formularioBusqueda).find('#fecha_desc_desde').val();
		var fecha_hasta 		= $(formularioBusqueda).find('#fecha_desc_hasta').val();

		if(numero != "" || cedula != "" || tipo_descuento != "" || acreedor != "" || estado_id != "" || nombre_colaborador != "")
		{
                    //console.log(tipo_descuento);
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					numero: numero,
					cedula: cedula,
					tipo_descuento: tipo_descuento,
					nombre_colaborador: nombre_colaborador,
					acreedor: acreedor,
					estado_id: estado_id,
					fecha_desde: fecha_desde,
					fecha_hasta: fecha_hasta,
				}
			}).trigger('reloadGrid');
		}
	};

	//Limpiar campos de busqueda
	var limpiarCampos = function(){

           // console.log("llegastelimpiando");
		$(formularioBusqueda).find('input[type="text"]').prop("value", "");
		$('#tipo_descuento').val('').trigger('chosen:updated');
                $('#acreedor').val('').trigger('chosen:updated');
                $('#estado_id').val('').trigger('chosen:updated');

	};

	return{
		init: function() {
			tabla();
		//	init();
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


function descargar()
{

  var descuento_id = $(this).attr("data-id");

  $.ajax({
    url: phost() + "descuentos/ajax-descargar",
    type:"POST",
    data:{
    erptkn:tkn,
    descuento_id:descuento_id
    },
    dataType:"json",
    success: function(json){

    $.each(json, function(i, value) {
          //  $('#disponible').val(value.capacidad);
        });

    }
    });

}

tablaDescuentos.init();
