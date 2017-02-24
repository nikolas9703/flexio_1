var multiselect = window.location.pathname.match(/facturas_compras/g) ? true : false;
var operacion_type = window.location.pathname.match(/subcontratos/g) ? '' : 18;
operacion_type = window.location.pathname.match(/facturas_compras_contratos/g) ? 19 : operacion_type;

var tablaFacturasCompras = (function () {

	var tablaUrl = phost() + 'facturas_compras/ajax-listar';
	var gridId = "tablaFacturasComprasGrid";
	var gridObj = $("#tablaFacturasComprasGrid");
	var opcionesModal = $('#optionsModal, #opcionesModal');
	var mdModal = $('#mdModal');
	var formularioBuscar = '';

	var botones = {
		opciones: ".viewOptions",
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
		refacturar: "#refacturar",
		irPagos: ".irPagos",
		documentosFiltro: ".documentosFiltro",
		aplicarCreditoFavor: ".aplicar-credito-favor",
		confirmarCreditoFavor: ".confirmar-credito-favor",
		guardarCreditoFavor: ".guardar-credito-favor",
		changeStateBtn: ".change-state-btn",
		changeStateMultipleBtn: "#change-state-multiple-btn"
	};

	var getParametrosFiltroInicial = function () {

		var scaja_id = '';
		if (typeof caja_id != 'undefined') {
			scaja_id = caja_id;
		}
		var pedidosid = '';
		if (typeof pedidos_id != 'undefined') {
			pedidosid = pedidos_id;
		}

		//Parametros default
		var data = {
			erptkn: tkn,
			tipo: operacion_type,
			caja_id: scaja_id,
			item_id: (typeof item_id !== 'undefined') ? _.toString(item_id) : '',
			pedidos_id: pedidosid,
			pedido_id: (typeof window.sp_pedido_id !== 'undefined') ? window.sp_pedido_id : '', //from subpanels ver pedido
			proveedor: (typeof proveedor_id !== 'undefined') ? _.toString(proveedor_id) : '',
			creado_por: '',
			categoria_id: '',
			orden_compra_id: (typeof orden_compra_id !== 'undefined') ? _.toString(orden_compra_id) : '',
			subcontrato_id: (typeof subcontrato_id !== 'undefined') ? _.toString(subcontrato_id) : '',
			campo: typeof window.campo !== 'undefined' ? window.campo : {},
		};

		//Parametros guardados en localStorage
		if (typeof (Storage) !== "undefined") {
			if (typeof localStorage.fc_numero_factura != "undefined" && localStorage.fc_numero_factura != "null" && localStorage.fc_numero_factura != "") {
				data.numero_factura = localStorage.fc_numero_factura;
			}
			if (typeof localStorage.fc_fecha1 != "undefined" && localStorage.fc_fecha1 != "null" && localStorage.fc_fecha1 != "") {
				data.fecha1 = localStorage.fc_fecha1;
			}
			if (typeof localStorage.fc_fecha2 != "undefined" && localStorage.fc_fecha2 != "null" && localStorage.fc_fecha2 != "") {
				data.fecha2 = localStorage.fc_fecha2;
			}
			if (typeof localStorage.fc_proveedor != "undefined" && localStorage.fc_proveedor != "null" && localStorage.fc_proveedor != "") {
				data.proveedor = localStorage.fc_proveedor;
			}
			if (typeof localStorage.fc_centro_contable != "undefined" && localStorage.fc_centro_contable != "null" && localStorage.fc_centro_contable != "") {
				data.centro_contable = localStorage.fc_centro_contable;
			}
			if (typeof localStorage.fc_estado != "undefined" && localStorage.fc_estado != '' && localStorage.fc_estado != "null") {

				if (localStorage.fc_estado.match(/,/gi)) {
					data.estado = [];
					$.each(localStorage.fc_estado.split(","), function (i, estado) {
						data.estado[i] = estado;
					});

				} else {
					data.estado = localStorage.fc_estado;
				}
			}
			if (typeof localStorage.fc_monto1 != "undefined" && localStorage.fc_monto1 != "null" && localStorage.fc_monto1 != "") {
				data.monto1 = localStorage.fc_monto1;
			}
			if (typeof localStorage.fc_monto2 != "undefined" && localStorage.fc_monto2 != "null" && localStorage.fc_monto2 != "") {
				data.monto2 = localStorage.fc_monto2;
			}
			if (typeof localStorage.fc_creado_por != "undefined" && localStorage.fc_creado_por != "null" && localStorage.fc_creado_por != "") {
				data.creado_por = localStorage.fc_creado_por;
			}
			if (typeof localStorage.fc_categoria_id != "undefined" && localStorage.fc_categoria_id != '' && localStorage.fc_categoria_id != "null") {
				data.categoria_id = localStorage.fc_categoria_id;
			}
		}

		var monto1 = $('#monto1').val();
		var monto2 = $('#monto2').val();
		var centro_contable = $('#centro_contable').val();
		//var tipo = $('#tipo').val();
		var creado_por = $('#creado_por').val();
		var categoria_id = $('#categoria_id').val();

		return data;
	};

	//Mostrar en los campos de busqueda los valores guardados
	//en localStorage
	var setBusquedaDeLocalStorage = function () {
		if (typeof (Storage) == "undefined") {
			return false;
		}
		var haybusqueda = 0;

		if (typeof localStorage.fc_numero_factura != "undefined" && localStorage.fc_numero_factura != '') {
			$('#numero_factura').val(localStorage.fc_numero_factura);
			haybusqueda += 1;
		}
		if (typeof localStorage.fc_fecha1 != "undefined" && localStorage.fc_fecha1 != '') {
			$('#fecha1').prop('value', localStorage.fc_fecha1);
			haybusqueda += 1;
		}
		if (typeof localStorage.fc_fecha2 != "undefined" && localStorage.fc_fecha2 != '') {
			$('#fecha2').prop('value', localStorage.fc_fecha2);
			haybusqueda += 1;
		}
		if (typeof localStorage.fc_centro_contable != "undefined" && localStorage.fc_centro_contable != '') {
			$('#centro_contable').find('option[value="' + localStorage.fc_centro_contable + '"]').attr("selected", "selected");
			haybusqueda += 1;
		}
		if (typeof localStorage.fc_estado != "undefined" && localStorage.fc_estado != '') {
			//verificar si hay varios estados seleccionados
			if (localStorage.fc_estado.match(/,/gi)) {
				$.each(localStorage.fc_estado.split(","), function (i, estado) {
					$('#estado').find('option[value="' + estado + '"]').attr("selected", "selected");
				});

			} else {
				$('#estado').find('option[value="' + localStorage.fc_estado + '"]').attr("selected", "selected");
			}

			haybusqueda += 1;
		}
		if (typeof localStorage.fc_monto1 != "undefined" && localStorage.fc_monto1 != '') {
			$('#monto1').val(localStorage.fc_monto1);
			haybusqueda += 1;
		}
		if (typeof localStorage.fc_monto2 != "undefined" && localStorage.fc_monto2 != '') {
			$('#monto2').val(localStorage.fc_monto2);
			haybusqueda += 1;
		}
		if (typeof localStorage.fc_creado_por != "undefined" && localStorage.fc_creado_por != '') {
			$('#creado_por').find('option[value="' + localStorage.fc_creado_por + '"]').attr("selected", "selected");
			haybusqueda += 1;
		}
		if (typeof localStorage.fc_categoria_id != "undefined" && localStorage.fc_categoria_id != '') {
			//verificar si hay varios estados seleccionados
			if (localStorage.fc_categoria_id.match(/,/gi)) {
				$.each(localStorage.fc_categoria_id.split(","), function (i, estado) {
					$('#categoria_id').find('option[value="' + estado + '"]').attr("selected", "selected");
				});

			} else {
				$('#categoria_id').find('option[value="' + localStorage.fc_categoria_id + '"]').attr("selected", "selected");
			}
			haybusqueda += 1;
		}
		if (typeof localStorage.fc_proveedor != "undefined" && localStorage.fc_proveedor != '') {
			$("#proveedor3").append('<option value="' + localStorage.fc_proveedor + '" selected="selected">' + localStorage.fc_proveedor_nombre + '</option>');
			haybusqueda += 1;
		}

		//si existe parametros en localStorage
		//mostrar el panel de busqueda abierto.
		if (haybusqueda > 0) {
			$('#numero_factura').closest('.ibox-content').removeAttr("style");
		}

		$("select").trigger("chosen:updated");
		$("#proveedor3").trigger('change');
	};

	var guardarBusquedaLocalStorage = function (dom) {
		localStorage.setItem("fc_fecha1", $('#fecha1').val());
		localStorage.setItem("fc_fecha2", $('#fecha2').val());
		localStorage.setItem("fc_centro_contable", $('#centro_contable').val());
		localStorage.setItem("fc_estado", $('#estado').val());
		localStorage.setItem("fc_proveedor", $('#proveedor3').val());
		localStorage.setItem("fc_proveedor_nombre", $("#proveedor3").find('option:selected').text());
		localStorage.setItem("fc_monto1", $('#monto1').val());
		localStorage.setItem("fc_monto2", $('#monto2').val());
		localStorage.setItem("fc_creado_por", $('#creado_por').val());
		localStorage.setItem("fc_categoria_id", $('#categoria_id').val());
		localStorage.setItem("fc_numero_factura", $('#numero_factura').val());
	};

	var limpiarBusquedaLocalStorage = function () {
		if (typeof (Storage) == "undefined") {
			return false;
		}
		localStorage.removeItem("fc_fecha1");
		localStorage.removeItem("fc_fecha2");
		localStorage.removeItem("fc_centro_contable");
		localStorage.removeItem("fc_estado");
		localStorage.removeItem("fc_proveedor");
		localStorage.removeItem("fc_proveedor_nombre");
		localStorage.removeItem("fc_monto1");
		localStorage.removeItem("fc_monto2");
		localStorage.removeItem("fc_creado_por");
		localStorage.removeItem("fc_categoria_id");
		localStorage.removeItem("fc_numero_factura");
	};

	var tabla = function () {

		gridObj.jqGrid({
			url: tablaUrl,
			mtype: "POST",
			datatype: "json",
			colNames: ['No. Factura', 'Fecha', 'Proveedor', 'Monto', 'Saldo por pagar', 'Cantidad de pagos', 'Centro contable', 'Documentos', 'Creado por', 'Estado', '', ''],
			colModel: [{
				name: 'No. Factura',
				index: 'factura_proveedor',
				width: 50,
				sortable: true
			}, {
				name: 'Fecha',
				index: 'created_at',
				width: 50,
				sortable: true
			}, {
				name: 'Proveedor',
				index: 'proveedor_id',
				width: 70,
				sortable: true,
			}, {
				name: 'Monto',
				index: 'total',
				width: 50,
				sortable: true
			}, {
				name: 'Saldo',
				index: 'saldo',
				width: 50,
				sortable: false
			}, {
				name: 'Pagos',
				index: 'pagos',
				width: 50,
				sortable: false
			}, {
				name: 'Centro',
				index: 'centro_contable_id',
				width: 70,
				sortable: true
			}, {
				name: 'Documentos',
				index: 'documento',
				width: 70,
				sortable: true
			}, {
				name: 'Creado por',
				index: 'created_by',
				width: 70,
				sortable: true
			}, {
				name: 'Estado',
				index: 'estado_id',
				width: 55,
				sortable: true
			}, {
				name: 'options',
				index: 'options',
				width: 40
			}, {
				name: 'link',
				index: 'link',
				width: 50,
				align: "center",
				sortable: false,
				resizable: false,
				hidden: true,
				hidedlg: true
			}],
			postData: getParametrosFiltroInicial(),
			height: "auto",
			autowidth: true,
			rowList: [10, 20, 50, 100],
			rowNum: 10,
			page: 1,
			pager: gridId + "Pager",
			loadtext: '<p>Cargando...',
			hoverrows: false,
			viewrecords: true,
			refresh: true,
			gridview: true,
			multiselect: multiselect,
			sortname: 'codigo',
			sortorder: "DESC",
			beforeProcessing: function (data, status, xhr) {
				if ($.isEmptyObject(data.session) === false) {
					window.location = phost() + "login?expired";
				}
			},
			loadBeforeSend: function () { //propiedadesGrid_cb
				$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
				$(this).closest("div.ui-jqgrid-view").find("#tablaClientesGrid_cb, #jqgh_tablaClientesGrid_link").css("text-align", "center");
			},
			loadComplete: function (data, status, xhr) {

				if (gridObj.getGridParam('records') === 0) {
					$('#gbox_' + gridId).hide();
					$('#' + gridId + 'NoRecords').empty().append('No se encontraron Facturas.').css({
						"color": "#868686",
						"padding": "30px 0 0"
					}).show();
				} else {
					$('#gbox_' + gridId).show();
					$('#' + gridId + 'NoRecords').empty();
				}

				if (multiselect == true) {
					gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
					$('#gridHeader').sticky({
						getWidthFrom: '.ui-jqgrid-view',
						className: 'jqgridHeader'
					});
					$('#jqgh_' + gridId + "_cb").css("text-align", "center");
				}

			},
			onSelectRow: function (id) {
				$(this).find('tr#' + id).removeClass('ui-state-highlight');
			}
		});
	};

	var eventos = function () {
		//Bnoton de Opciones
		gridObj.on("click", botones.opciones, function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();

			var id = $(this).attr("data-id");

			var rowINFO = gridObj.getRowData(id);
			var options = rowINFO.link;

			//Init Modal
			opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO["No. Factura"]).text() + '');
			opcionesModal.find('.modal-body').empty().append(options);
			opcionesModal.find('.modal-footer').empty();
			opcionesModal.modal('show');
		});

	};
	$(botones.limpiar).click(function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();

		//limpiar localStorage
		limpiarBusquedaLocalStorage();

		$('#buscarFacturasComprasForm').find('input[type="text"]').prop("value", "");
		$('#buscarFacturasComprasForm').find('select.chosen-select').prop("value", "");
		$('#buscarFacturasComprasForm').find('select').prop("value", "");
		$('#buscarFacturasComprasForm').find('select[id="categoria_id"]').find('option').removeAttr("selected");
		$("#buscarFacturasComprasForm").find('#categoria_id').chosen({
			width: '100%'
		}).trigger('chosen:updated');
		$(".chosen-select").trigger("chosen:updated");
		$("#proveedor3").val(null).trigger("change");
		recargar();
	});
	$(botones.buscar).click(function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();

		var numero_factura = $('#numero_factura').val();
		var fecha1 = $('#fecha1').val();
		var fecha2 = $('#fecha2').val();
		var proveedor = proveedor3.value;
		var estado = $('#estado').val();
		var monto1 = $('#monto1').val();
		var monto2 = $('#monto2').val();
		var centro_contable = $('#centro_contable').val();
		//var tipo = $('#tipo').val();
		var creado_por = $('#creado_por').val();
		var categoria_id = $('#categoria_id').val();

		if (numero_factura !== "" || fecha1 !== "" || fecha2 !== "" || proveedor !== "" || estado !== "" || monto1 !== "" || monto2 !== "" || centro_contable !== "" || creado_por !== "" || categoria_id !== "") {
			//Reload Grid

			if (typeof (Storage) !== "undefined") {
				guardarBusquedaLocalStorage();
			}

			gridObj.setGridParam({
				postData: null
			});
			gridObj.setGridParam({
				url: tablaUrl,
				datatype: "json",
				postData: {
					tipo: operacion_type,
					numero_factura: numero_factura,
					fecha1: fecha1,
					fecha2: fecha2,
					proveedor: proveedor,
					estado: estado,
					monto1: monto1,
					monto2: monto2,
					centro_contable: centro_contable,
					categoria_id: categoria_id,
					creado_por: creado_por,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	});

	opcionesModal.on("click", botones.aplicarCreditoFavor, function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		//aplicar_credito.js
		aplicar_credito().m.run($(this));
	});

	mdModal.on("click", botones.confirmarCreditoFavor, function (e) {
		e.preventDefault();
		//aplicar_credito.js
		aplicar_credito().m.showSummit();
	});

	mdModal.on("change", '.total', function (e) {
		aplicar_credito().m.updateTotal($(this));
	});

	mdModal.on('submit', "#aplicarCreditoForm", function (e) {
		e.preventDefault();
		//aplicar_credito.js
		aplicar_credito().m.summit($(this));
	});

	//change state
	opcionesModal.on("click", botones.changeStateBtn, function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		//aplicar_credito.js
		change_state_factura_compra().m.run($(this));
	});

	gridObj.on("click", botones.changeStateBtn, function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		//aplicar_credito.js
		opcionesModal.modal('show');
		change_state_factura_compra().m.run($(this));
	});

	$("#moduloOpciones").on("click", botones.changeStateMultipleBtn, function (e) {
		//aplicar_credito.js
		opcionesModal.modal('show');
		change_state_factura_compra().m.run($(this));
	});

	opcionesModal.on('click', ".state-btn", function (e) {
		e.preventDefault();
		//aplicar_credito.js
		change_state_factura_compra().m.summit($(this));
	});

	//Documentos Modal
	$("#optionsModal").on("click", ".subirDocumento", function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();

		//Cerrar modal de opciones
		$("#optionsModal").modal('hide');
		var factura_compra_id = $(this).attr("data-id");

		//Inicializar opciones del Modal
		$('#documentosModal').modal({
			backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
			show: false
		});

		var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		scope.safeApply(function () {
			scope.campos.factura_id = factura_compra_id;
		});
	});

	gridObj.on("click", botones.documentosFiltro, function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		//Cerrar modal de opciones
		var factura_compra_id = $(this).attr("data-id");
		var url = phost() + "documentos/listar";

		var form = $(
			'<form action="' + url + '" method="post" style="display:none;">' +
			'<input type="hidden" name="erptkn" value="' + tkn + '">' +
			'<input type="hidden" name="numero_documento" value="' + factura_compra_id + '">' +
			'<input type="submit">' +
			'</form>'
		);
		$('body').append(form);
		form.submit();

		$('#documentosModal').modal('show');
	});

	//Ir al pago filtrando por factura
	$("#optionsModal").on("click", botones.irPagos, function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		//Cerrar modal de opciones
		var factura_compra_id = $(this).attr("data-id");
		var url = phost() + "pagos/listar";
		var form = $(
			'<form action="' + url + '" method="post" style="display:none;">' +
			'<input type="hidden" name="erptkn" value="' + tkn + '">' +
			'<input type="hidden" name="numero_documento" value="' + factura_compra_id + '">' +
			'<input type="submit">' +
			'</form>'
		);
		$('body').append(form);
		form.submit();

	});
	//Ir al pago filtrando por factura desde el modal
	gridObj.on("click", botones.irPagos, function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();
		//Cerrar modal de opciones
		var factura_compra_id = $(this).attr("data-id");
		var url = phost() + "pagos/listar";
		var form = $(
			'<form action="' + url + '" method="post" style="display:none;">' +
			'<input type="hidden" name="erptkn" value="' + tkn + '">' +
			'<input type="hidden" name="numero_documento" value="' + factura_compra_id + '">' +
			'<input type="submit">' +
			'</form>'
		);
		$('body').append(form);
		form.submit();

	});

	$(botones.refacturar).click(function () {
		var ids = gridObj.jqGrid('getGridParam', 'selarrrow');

		if (!_.isEmpty(ids)) {
			$('#items_facturados').val(ids);
			$('#refacturaForm').submit();
		} else {
			swal("Seleccione las facturas para refacturar");
			return false;
		}
	});

	var redondeo = function (value) {
		return accounting.formatNumber(value, 2, ",");
	};

	var recargar = function () {
		/*$('#buscarFacturasComprasForm').find('select[name="tipo"]').prop("value", "18");
		$("#buscarFacturasComprasForm").find('#tipo').chosen({width: '100%'}).trigger('chosen:updated');*/

		//Reload Grid
		gridObj.setGridParam({
			url: tablaUrl,
			datatype: "json",
			postData: {
				tipo: operacion_type,
				numero_factura: '',
				fecha1: '',
				fecha2: '',
				proveedor: '',
				estado: '',
				monto1: '',
				monto2: '',
				centro_contable: '',
				categoria_id: '',
				creado_por: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');

	};

	var redimencionar_tabla = function () {
		$(window).resizeEnd(function () {
			$(".ui-jqgrid").each(function () {
				var w = parseInt($(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_", "");
				$("#" + gId).setGridWidth(w);
			});
		});
	};
	var campos = function () {
		$('#buscarFacturasComprasForm').find('select[name="tipo"]').prop("value", "18");
		$("#buscarFacturasComprasForm").find('#categoria_id').chosen({
			width: '100%'
		}).trigger('chosen:updated');

	};
	return {
		init: function () {
			campos();
			setBusquedaDeLocalStorage();
			tabla();
			eventos();
			redimencionar_tabla();
		}
	};

})();

tablaFacturasCompras.init();
