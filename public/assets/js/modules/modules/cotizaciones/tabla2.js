//modulo clientes
var tablaCotizaciones = (function () {
    
    var tablaUrl = phost() + 'cotizaciones/ajax-listar2';
    var gridId = "tablaCotizacionesGrid";
    var gridObj = $("#tablaCotizacionesGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = '';

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'No. Cotización', 'Fecha de emisión', 'Válido hasta', 'Estado', 'Vendedor', '', ''],
            colModel: [
                {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                {name: 'codigo', index: 'codigo', width: 60, sortable: true},
                {name: 'fecha_desde', index: 'fecha_desde', width: 40, sortable: false, },
                {name: 'fecha_hasta', index: 'fecha_hasta', width: 40, sortable: false, },
                {name: 'estado', index: 'estado', width: 40, sortable: false, align:'center'},
                {name: 'vendedor', index: 'vendedor', width: 40, sortable: false},
                {name: 'options', index: 'options', width: 40, align:'center'},
                {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
            ],
            postData: {
                erptkn: tkn,
                cliente_id: (typeof cliente_id === 'undefined' || !($.isNumeric(cliente_id))) ? '' : cliente_id,
                oportunidad_id: (typeof window.oportunidad_id === 'undefined') ? '' : window.oportunidad_id
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
            multiselect: true,
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
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Cotizaciones.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                }
                
                gridObj.find('td').css('vertical-align', 'middle');

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className: 'jqgridHeader'
                });
                
                $('#jqgh_' + gridId + "_cb").css("text-align", "center");
                
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

    };
    $(botones.limpiar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#buscarCotizacionesForm').find('input[type="text"]').prop("value", "");
        $('#buscarCotizacionesForm').find('select.chosen-select').prop("value", "");
        $('#buscarCotizacionesForm').find('select').prop("value", "");
        $(".chosen-select").trigger("chosen:updated");

        recargar();
    });
    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var no_cotizacion = $('#no_cotizacion').val();
        var cliente = $('#cliente').val();
        var desde = $('#fecha1').val();
        var hasta = $('#fecha2').val();
        var etapa = $('#etapa').val();
        console.log("estado: "+etapa);
        var vendedor = $('#vendedor').val();

        if (no_cotizacion !== '' || cliente !== "" || desde !== "" || hasta !== "" || etapa !== "" || vendedor !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    no_cotizacion: no_cotizacion,
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
    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                no_cotizacion: '',
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
    //Al redimensionar ventana

    tablaCotizaciones.init();
// $(window).resizeEnd(function() {
// 	tablaColaboradores.redimencionar_tabla();
// });
});
