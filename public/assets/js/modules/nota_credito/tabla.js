var tablaNotaCredito = (function () {

    if (typeof cliente_id === 'undefined') {
        cliente_id = "";
    }

    var tablaUrl = phost() + 'notas_creditos/ajax-listar';
    var gridId = "tablaNotaCreditoGrid";
    var gridObj = $("#tablaNotaCreditoGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = '';

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarNotaCredito"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'No. Notas de Cr&eacute;dito', 'Cliente', 'Fecha de emisión', 'Monto de cr&eacute;dito', 'Vendedor','Estado', '', ''],
            colModel: [
                {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                {name: 'codigo', index: 'codigo', width: 30, sortable: true},
                {name: 'cliente_id', index: 'cliente_id', width: 70, sortable: true},
                {name: 'fecha', index: 'fecha', width: 50, sortable: false, },
                {name: 'monto', index: 'monto', width: 30, sortable: false},
                {name: 'vendedor', index: 'vendedor', width: 30, sortable: false},
                {name: 'estado', index: 'estado', width: 30, sortable: false},
                {name: 'options', index: 'options', width: 40},
                {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
            ],
            postData: {
                erptkn: tkn,
                cliente_id: cliente_id
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
            sortorder: "ASC",
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
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Notas de Cr&eacute;ditos.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                }

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
        $('#buscarNotaCreditoForm').find('input[type="text"]').prop("value", "");
        $('#buscarNotaCreditoForm').find('select.chosen-select').prop("value", "");
        $('#buscarNotaCreditoForm').find('select').prop("value", "");
        $(".chosen-select").trigger("chosen:updated");
        recargar();
    });
    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var cliente = $('#cliente').val();
        var desde = $('#fecha_desde').val();
        var hasta = $('#fecha_hasta').val();
        var etapa = $('#etapa').val();
        var vendedor = $('#vendedor').val();

        if (cliente !== "" || desde !== "" || hasta !== "" || etapa !== "" || vendedor !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
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
    //Expotar a CSV.
    $(botones.exportar).click(function (e) {
        //console.log("exportar");
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        if ($('#tabla').is(':visible') == true) {
            console.log("exportar");
            //Desde la Tabla
            exportarjQgrid();

        }
    });
    function exportarjQgrid() {
        //Exportar Seleccionados del jQgrid
        var registros_jqgrid = [];

        registros_jqgrid = $("#tablaNotaCreditoGrid").jqGrid('getGridParam', 'selarrrow');
        //console.log(registros_jqgrid);
        var obj = new Object();
        obj.count = registros_jqgrid.length;

        if (obj.count) {

            obj.items = new Array();

            for (elem in registros_jqgrid) {
                console.log(elem);

               
                if (elem === 0) {
                    var registro_jqgrid = $("#tablaNotaCreditoGrid").getRowData(registros_jqgrid[elem]);
                    console.log(registro_jqgrid);
                }else{
                     var registro_jqgrid = $("#tablaNotaCreditoGrid").getRowData(registros_jqgrid[elem]);
                      console.log(registro_jqgrid);
                }
                //Rename column object
                registro_jqgrid['No. Notas de Credito'] = registro_jqgrid['codigo'];
                 delete registro_jqgrid['codigo'];
                 registro_jqgrid['Cliente'] = registro_jqgrid['cliente_id'];
                 delete registro_jqgrid['cliente_id'];
                 registro_jqgrid['Fecha de emision'] = registro_jqgrid['fecha'];
                 delete registro_jqgrid['fecha'];
                 registro_jqgrid['Monto de credito'] = registro_jqgrid['monto'];
                 delete registro_jqgrid['monto'];
                 registro_jqgrid['Estado'] = registro_jqgrid['estado'];
                 delete registro_jqgrid['estado'];
                  registro_jqgrid['Vendedor'] = registro_jqgrid['vendedor'];
                 delete registro_jqgrid['vendedor'];
                //Remove objects from associative array
                delete registro_jqgrid['uuid'];
                delete registro_jqgrid['link'];
                delete registro_jqgrid['options'];

                //Push to array
                obj.items.push(registro_jqgrid);
            }
          //  console.log(obj);

            var json = JSON.stringify(obj);
            var csvUrl = JSONToCSVConvertor(json);
            var filename = 'Nota_Credito' + Date.now() + '.csv';

            //Ejecutar funcion para descargar archivo
            downloadURL(csvUrl, filename);

            $('body').trigger('click');
        }
    }
    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
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
    tablaNotaCredito.init();
});
