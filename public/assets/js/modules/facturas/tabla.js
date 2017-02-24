//modulo clientes

var multiselect = window.location.pathname.match(/facturas/g) ? true : false;

var tablaFacturas = (function () {

    if (typeof cliente_id === 'undefined') {
        cliente_id = "";
    }
	
    var tablaUrl = phost() + 'facturas/ajax-listar';
    var gridId = "tablaFacturasGrid";
    var gridObj = $("#tablaFacturasGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = '';
    var documentosModal = $('#documentosModal');

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarListaFacturas",
        subirDocumento: ".subirArchivoBtn"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'No.Factura', 'Cliente', 'Fecha de emisión', 'Fecha de vencimiento', 'Estado', 'Monto', 'Saldo por cobrar', 'Vendedor', '', ''],
            colModel: [
                {name: 'uuid', index: 'uuid' , hidedlg: true, hidden: true},
                {name: 'codigo', index: 'codigo', sortable: true},
                {name: 'cliente', index: 'cliente', sortable: true},
                {name: 'fecha_desde', index: 'fecha_desde', sortable: false, },
                {name: 'fecha_hasta', index: 'fecha_hasta',  sortable: false, },
                {name: 'estado', index: 'estado',  sortable: false},
                {name: 'monto', index: 'monto',  sortable: false},
                {name: 'saldo', index: 'saldo',  sortable: false},
                {name: 'vendedor',align: "center", index: 'vendedor',  sortable: false},
                {name: 'options', index: 'options'},
                {name: 'link', index: 'link',  align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
            ],
            postData: {
                erptkn: tkn,
                cliente_id: cliente_id,
                contrato_alquiler_id:(typeof contrato_alquiler_id === 'undefined' || _.toString(window.contrato_alquiler_id) == "[object HTMLInputElement]") ? '' : contrato_alquiler_id,
                contrato_id:(typeof sp_contrato_id === 'undefined') ? '' : sp_contrato_id,
                orden_alquiler_id:(typeof sp_orden_alquiler_id === 'undefined') ? '' : sp_orden_alquiler_id,
                ms_selected: typeof(Storage) !== "undefined" ? localStorage.getItem("ms-selected") : "",
                campo: typeof window.campo !== 'undefined' ? window.campo : {}
            },
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
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaClientesGrid_cb, #jqgh_tablaClientesGrid_link").css("text-align", "center");
            },
            loadComplete: function (data, status, xhr) {

                if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Facturas.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                }

                //Boton de Exportar Facturas
                $(botones.exportar).on("click", function (e) {

                    e.preventDefault();
                    e.returnValue = false;
                    e.stopPropagation();

                    if ($('#tabla').is(':visible') == true) {

                        //Exportar Seleccionados del jQgrid
                        var ids = [];

                        ids = gridObj.jqGrid('getGridParam', 'selarrrow');

                        //Verificar si hay seleccionados
                        if (ids.length > 0) {

                            $('#ids').val(ids);
                            console.log(ids);
                            $('form#exportarFacturas').submit();
                            $('body').trigger('click');
                        }
                    }
                });

                if(multiselect)
                {
                    //header flotante
                    gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                    //floating headers
                    $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view',
                        className: 'jqgridHeader'
                    });
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

            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.codigo).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });
		
		opcionesModal.on("click",".imprimirFactura",function(e){
			e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
			var id = $(this).attr("data-id");
			location.href = phost() + "facturas/imprimirFactura/" + id;
			return false;
		});

    };
    $(botones.limpiar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#buscarFacturasForm').find('input[type="text"]').prop("value", "");
        $('#buscarFacturasForm').find('select.chosen-select').prop("value", "");
        $('#buscarFacturasForm').find('select').prop("value", "");
        $(".chosen-select").trigger("chosen:updated");

        recargar();
    });
    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var no_factura = $('#no_factura').val();
        var cliente = $('#cliente').val();
        var desde = $('#fecha1').val();
        var hasta = $('#fecha2').val();
        var etapa = $('#etapa').val();
        var vendedor = $('#vendedor').val();

        if (no_factura !== "" || cliente !== "" || desde !== "" || hasta !== "" || etapa !== "" || vendedor !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    no_factura: no_factura,
                    cliente: cliente,
                    desde: desde,
                    hasta: hasta,
                    etapa: etapa,
                    vendedor: vendedor,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }


    });

     opcionesModal.on("click", botones.subirDocumento, function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        opcionesModal.modal('hide');

        var factura = $(this).attr("data-id");
         var factura_code = $(this).attr("data-codigo");
        // console.log(factura_id);
        	//Inicializar opciones del Modal
			documentosModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});

			var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		    scope.safeApply(function(){
		    	scope.campos.numero_factura = factura_code;
                scope.campos.factura_id = factura;

		    });
			documentosModal.modal('show');
    });
    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                no_factura: '',
                cliente: '',
                desde: '',
                hasta: '',
                etapa: '',
                vendedor: '',
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
    return{
        init: function () {
            tabla();
            eventos();
            redimencionar_tabla();
        }
    };

})();

$(function () {

    tablaFacturas.init();
});
