$(function () {

	if (typeof id_cliente === 'undefined') {
		id_cliente = "";
	}

	//verificar si la url actual es contactos
	//de lo contrario no mostrar multiselect del jqgrid
	var multiselect = window.location.pathname.match(/contactos/g) ? true : false;

	//Init Contactos Grid
	$("#contactosGrid").jqGrid({
		url: phost() + 'contactos/ajax-listar-contactos',
		datatype: "json",
		colNames: [
			'Principal',
			'Nombre',
			'Cargo',
			'Correo',
			'Celular',
			'Tel&eacute;fono',
			'&Uacute;ltimo Contacto',
			'',
			''
		],
		colModel: [{
			name: 'principal',
			index: 'principal',
			width: 25,
			align: "center",
			editable: true,
			edittype: 'checkbox',
			editoptions: {
				value: "True:False"
			},
			formatter: cboxFormatter,
			formatoptions: {
				disabled: false
			},
			classes: 'check'
		}, {
			name: 'nombre',
			index: 'nombre',
			width: 70
		}, {
			name: 'Cargo',
			index: 'cargo',
			width: 70
		}, {
			name: 'correo',
			index: 'correo',
			width: 50,
			sortable: false
		}, {
			name: 'celular',
			index: 'celular',
			width: 50,
			sortable: false
		}, {
			name: 'telefono',
			index: 'telefono',
			width: 50,
			sortable: false
		}, {
			name: 'ultimo_contacto',
			width: 50,
			sortable: false
		}, {
			name: 'link',
			index: 'link',
			width: 50,
			align: "center",
			sortable: false,
			resizable: false
		}, {
			name: 'options',
			index: 'options',
			hidden: true
		}],
		mtype: "POST",
		postData: {
			erptkn: tkn,
			id_cliente: id_cliente,
			campo: typeof window.campo !== 'undefined' ? window.campo : {}
		},
		height: "auto",
		autowidth: true,
		rowList: [10, 20, 50, 100],
		rowNum: 10,
		page: 1,
		pager: "#contactosGridPager",
		loadtext: '<p>Cargando Contactos...',
		hoverrows: false,
		viewrecords: true,
		refresh: true,
		gridview: true,
		//multiselect: multiselect,
		sortname: 'nombre',
		sortorder: "ASC",
		beforeProcessing: function (data, status, xhr) {
			//Check Session
			if ($.isEmptyObject(data.session) === false) {
				window.location = phost() + "login?expired";
			}
		},
		loadBeforeSend: function () {
			$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
			$(this).closest("div.ui-jqgrid-view").find("#jqgh_contactosGrid_cb, #jqgh_contactosGrid_link").css("text-align", "center");
		},
		beforeRequest: function (data, status, xhr) {
			$('.jqgrid-overlay').show();
			$('#load_contactosGrid').addClass('ui-corner-all');
		},
		loadComplete: function (data) {

			$('.jqgrid-overlay').hide();

			//check if isset data
			if ($("#contactosGrid").getGridParam('records') === 0) {
				$('#gbox_contactosGrid').hide();
				$('#contactosGridNoRecords').empty().append('No se encontraron Contactos.').css({
					"color": "#868686",
					"padding": "30px 0 0"
				}).show();
			} else {
				$('#gbox_contactosGrid').show();
				$('#contactosGridNoRecords').empty();
			}

			if (multiselect == true) {
				//add class to headers
				$("#contactosGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");

				//floating headers
				$('#gridHeader').sticky({
					getWidthFrom: '.ui-jqgrid-view',
					className: 'jqgridHeader'
				});
				//Arreglar tamaño de TD de los checkboxes
				//$("#contactosGrid_cb").css("width","50px");
				//  $("#contactosGrid tbody tr").children().first("td").css("margin-left","10px");
			}
		},
		onSelectRow: function (id) {
			$(this).find('tr#' + id).removeClass('ui-state-highlight');
		},
	});

	//Boton de opciones
	$("#contactosGrid").on("click", ".viewOptions", function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		var id_contacto = $(this).attr("data-contacto");

		var rowINFO = $("#contactosGrid").getRowData(id_contacto);
		var nombre = $(this).attr("data-nombre");
		var options = rowINFO["options"];
		//Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones: ' + nombre);
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});
	//Resize grid, on window resize end
	$(window).resizeEnd(function () {
		$(".ui-jqgrid").each(function () {
			var w = parseInt($(this).parent().width()) - 6;
			var tmpId = $(this).attr("id");
			var gId = tmpId.replace("gbox_", "");
			$("#" + gId).setGridWidth(w);
		});
	});

	$("#iconGrid").on("click", ".viewOptionsGrid", function (e) {

		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		//Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones: ' + $(this).closest(".chat-element").find("input[type='checkbox']").data("nombre"));
	});

	//Estas funciones aplican cuando se carga la tabla de contactos
	//desde modulo de clientes.
	if (multiselect == false) {

		//-----------------------------
		// Accciones para modo: Subpanel
		//-----------------------------
		//Abrir ventana de Crear contacto
		$("#optionsModal").on("click", "#verContacto", function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();

			if ($(this).attr('href') == "#") {

				var id_contacto = $(this).attr('data-contacto');

				//ocultar vista de cliente
				$('.editarFormularioClientes').addClass('hide');

				//mostrar formulario de editar contacto
				$('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="editarContactos"]').trigger('click');

				//ocultar modal
				$('#optionsModal').modal('hide');

				popular_detalle_contacto(id_contacto);
			}
		});

        var ajax_asignar_contacto_principal = function(uuid_contacto)
        {
            $.ajax({
				url: phost() + 'contactos/ajax-asignar-contacto-principal',
				data: {uuid_contacto: uuid_contacto, erptkn: tkn},
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function (json) {

				//Recargar tabla de contactos
				$("#contactosGrid").trigger('reloadGrid');

			});
        };

        $('table#contactosGrid').on('click', '.principal', function (e) {
    		e.preventDefault();
    		e.returnValue = false;
    		e.stopPropagation();
    		var uuid_contacto = $(this).data('rowid');
			ajax_asignar_contacto_principal(uuid_contacto);
    	});

		//Asignar un contacto como principal
		$("#optionsModal").on("click", "#asignarContactoPrincipalBtn", function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();
			var uuid_contacto = $(this).attr('data-contacto');
			ajax_asignar_contacto_principal(uuid_contacto);
			$('#optionsModal').modal('hide');
		});
	}

	function cboxFormatter(cellvalue, options, rowObject) {
		return '<input type="checkbox"' + (cellvalue == "1" ? ' checked="checked" disabled ' : '') +
			'data-rowId="' + options.rowId + '" value="' + cellvalue + '" class="principal"/>';
	}

});
