$(function () {

	if (typeof id_cliente === 'undefined') {
		id_cliente = "";
	}

	//Init Contactos Grid
	$("#tablaClientesCentrosFacturacionGrid").jqGrid({
		url: phost() + 'clientes/ajax-listar-centros-facturacion',
		datatype: "json",
		colNames: ['Principal','Nombre','Provincia','Distrito','Corregimiento','Direcci&oacute;n','',''],
		colModel: [
            {name: 'principal',index: 'principal',width: 25,align: "center",editable: true,edittype: 'checkbox',editoptions: {value: "True:False"},formatter: cboxFormatter,formatoptions: {disabled: false},classes: 'check'},
            {name: 'nombre',index: 'nombre', width: 70},
            {name: 'provincia',index: 'provincia',width: 50,sortable: false},
            {name: 'distrito',index: 'distrito', width: 50, sortable: false},
            {name: 'corregimiento', index: 'corregimiento', width: 50,sortable: false},
            {name: 'direccion',index: 'direccion',width: 120,sortable: false},
            {name: 'link',index: 'link',width: 50,align: "center",sortable: false,resizable: false},
            {name: 'options',index: 'options',hidden: true}
        ],
		mtype: "POST",
		postData: {
			erptkn: tkn,
			campo: typeof window.campo !== 'undefined' ? window.campo : {}
		},
		height: "auto",
		autowidth: true,
		rowList: [10, 20, 50, 100],
		rowNum: 10,
		page: 1,
		pager: "#tablaClientesCentrosFacturacionGridPager",
		loadtext: '<p>Cargando Centros de Facturaci&oacute;n...',
		hoverrows: false,
		viewrecords: true,
		refresh: true,
		gridview: true,
		multiselect: false,
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
			$(this).closest("div.ui-jqgrid-view").find("#jqgh_tablaClientesCentrosFacturacionGrid_cb, #jqgh_tablaClientesCentrosFacturacionGrid_link").css("text-align", "center");
		},
		beforeRequest: function (data, status, xhr) {
			$('.jqgrid-overlay').show();
			$('#load_tablaClientesCentrosFacturacionGrid').addClass('ui-corner-all');
		},
		loadComplete: function (data) {

			$('.jqgrid-overlay').hide();

			//check if isset data
			if ($("#tablaClientesCentrosFacturacionGrid").getGridParam('records') === 0) {
				$('#gbox_tablaClientesCentrosFacturacionGrid').hide();
				$('#tablaClientesCentrosFacturacionGridNoRecords').empty().append('No se encontraron centros de facturaci&oacute;n.').css({
					"color": "#868686",
					"padding": "30px 0 0"
				}).show();
			} else {
				$('#gbox_tablaClientesCentrosFacturacionGrid').show();
				$('#tablaClientesCentrosFacturacionGridNoRecords').empty();
			}

		},
		onSelectRow: function (id) {
			$(this).find('tr#' + id).removeClass('ui-state-highlight');
		},
	});

	//Boton de opciones
	$("#tablaClientesCentrosFacturacionGrid").on("click", ".viewOptions", function (e) {
        e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		var centro_facturacion_id = $(this).attr("data-centro");

		var rowINFO = $("#tablaClientesCentrosFacturacionGrid").getRowData(centro_facturacion_id);
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
	if (1) {

		var ajax_asignar_centro_principal = function (centro_facturacion_id) {
			$.ajax({
				url: phost() + 'clientes/ajax-asignar-centro-principal',
				data: {
					centro_facturacion_id: centro_facturacion_id,
					erptkn: tkn
				},
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function (json) {

				//Recargar tabla de contactos
				$("#tablaClientesCentrosFacturacionGrid").trigger('reloadGrid');

			});
		};

		$('table#tablaClientesCentrosFacturacionGrid').on('click', '.principal2', function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();
			var centro_facturacion_id = $(this).data('rowid');
			ajax_asignar_centro_principal(centro_facturacion_id);
		});

		//Asignar un contacto como principal
		$("#optionsModal").on("click", "#asignarCentroPrincipalBtn", function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();
			var centro_facturacion_id = $(this).attr('data-id');
			ajax_asignar_centro_principal(centro_facturacion_id);
			$('#optionsModal').modal('hide');
		});
	}

	function cboxFormatter(cellvalue, options, rowObject) {
		return '<input type="checkbox"' + (cellvalue == "1" ? ' checked="checked" disabled ' : '') +
			'data-rowId="' + options.rowId + '" value="' + cellvalue + '" class="principal2"/>';
	}

});
