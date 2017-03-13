//modulo clientes
var tablaColaboradores = (function () {
    if (typeof acreedor_id === 'undefined') {
        acreedor_id = "";
    }
    var tablaUrl = phost() + 'acreedores/ajax-listar-colaboradores';
    var gridId = "tablaColaboradoresGrid";
    var gridObj = $("#tablaColaboradoresGrid");
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
            colNames: ['', 'Colaborador', 'C&oacute;digo', 'C&eacute;dula', 'C. Contable', 'Cargo', 'Ciclo', 'Monto total', 'Monto por ciclo', 'Pendiente'],
            colModel: [
                {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                {name: 'colaborador', index: 'nombre', width: 55, sortable: true},
                {name: 'codigo', index: 'codigo', width: 55, sortable: false},
                {name: 'cedula', index: 'cedula', width: 50, sortable: false},
                {name: 'ccontable', index: 'ccontable', width: 70, sortable: false, },
                {name: 'cargo', index: 'cargo', width: 55, sortable: false},
                {name: 'ciclo', index: 'ciclo', width: 70, sortable: false},
                {name: 'mtotal', index: 'mtotal', width: 70, sortable: false},
                {name: 'mxciclo', index: 'mxciclo', width: 70, sortable: false},
                {name: 'pendiente', index: 'pendiente', width: 70, sortable: false},
            ],
            postData: {
                erptkn: tkn,
                acreedor_id: acreedor_id
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
            multiselect: false,
            sortname: 'nombre',
            sortorder: "asc",
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
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Colaboradores.<br><br>').css({"color": "#868686", "padding": "30px 0 0"}).show();
                }
                else {
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

                //Arreglar tamaño de TD de los checkboxes
                //FALTA ADAPTAR EL CODIGO PARA QUE LOS CHECKBOX SE VEAN BIEN
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
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.nombre).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

    };
    $(botones.limpiar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#buscarAcreedoresForm').find('input[type="text"]').prop("value", "");
        $('#buscarAcreedoresForm').find('select.chosen-select').prop("value", "");
        $('#buscarAcreedoresForm').find('select').prop("value", "");
        $(".chosen-select").trigger("chosen:updated");

        recargar();
    });
    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var nombre = $('#nombre').val();
        var tipo = $('#tipo').val();
        var telefono = $("#telefono").val();

        if (nombre !== "" || tipo !== "" || telefono !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    nombre: nombre,
                    tipo: tipo,
                    telefono: telefono,
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
                nombre: '',
                tipo: '',
                telefono: '',
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
    tablaColaboradores.init();
});
