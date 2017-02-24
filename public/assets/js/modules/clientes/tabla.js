//modulo clientes
var tablaClientes = (function () {

    var tablaUrl = phost() + 'clientes/ajax-listar';
    var gridId = "tablaClientesGrid";
    var gridObj = $("#tablaClientesGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = '';
    var crearCotizacionesForm = $("#crearCotizacionesForm");

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarClienteBtn",
        cotizaciones: ".cotizaciones"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'No. Cliente', 'Nombre', 'Teléfono', 'E-mail', 'Crédito a favor', 'Saldo por cobrar', 'Estado', '', ''],
            colModel: [
                {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                {name: 'codigo', index: 'codigo', width: 30, sortable: true},
                {name: 'nombre', index: 'nombre', width: 70, sortable: true},
                {name: 'telefono', index: 'telefono', width: 50, sortable: false},
                {name: 'correo', index: 'correo', width: 70, sortable: false},
                {name: 'credito', index: 'credito', width: 30, sortable: false},
                {name: 'saldo', index: 'saldo', width: 30, sortable: false},
                {name: 'estado', index: 'estado', align:"center", width: 30, sortable: true},
                {name: 'options', index: 'options', width: 40},
                {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
            ],
            postData: {
                erptkn: tkn
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
            loadonce:false,
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
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Clientes.').css({"color": "#868686", "padding": "30px 0 0"}).show();
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

    //Documentos Modal
    $("#optionsModal").on("click", ".subirArchivoBtn", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            //Cerrar modal de opciones
            $("#optionsModal").modal('hide');
            var clientes_id = $(this).attr("data-id");

            //Inicializar opciones del Modal
            $('#documentosModal').modal({
                    backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                    show: false
            });

            //$('#pedido_id').val(pedido_id);
            var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();

        scope.safeApply(function(){
            scope.campos.cliente_id = clientes_id;
        });
            $('#documentosModal').modal('show');
    });

    $(botones.limpiar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#buscarClientesForm').find('input[type="text"]').prop("value", "");
        $('#buscarClientesForm').find('#tipo').prop("value", "");
        $('#buscarClientesForm').find('#categoria').prop("value", "");
        $('#buscarClientesForm').find('#estado').prop("value", "");
        $('#buscarClientesForm').find('#identificacion').prop("value", "");
        $('#buscarClientesForm').find('#telefono').prop("value", "");
        $('#buscarClientesForm').find('#email').prop("value", "");
        recargar();
    });
    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var nombre_cliente = $('#nombre_cliente').val();
        var telefono = $('#telefono').val();
        var correo = $('#email').val();
        var tipo = $('#tipo').val();
        var categoria = $('#categoria').val();
        var estado = $('#estado').val();
        var identificacion = $('#identificacion').val();

        if (nombre_cliente !== "" || telefono !== "" || correo !== "" || tipo !== "" || categoria !== "" || estado !== "" || identificacion !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    nombre: nombre_cliente,
                    telefono: telefono,
                    correo: correo,
                    tipo: tipo,
                    categoria: categoria,
                    estado: estado,
                    identificacion: identificacion,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }


    });
    $(botones.exportar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        //  if ($('#tabla').is(':visible') == true) {
        // console.log('Exportar JS');
        //Exportar Seleccionados del jQgrid
        var ids = [];
        ids = $('#tablaClientesGrid').jqGrid('getGridParam', 'selarrrow');

        //Verificar si hay seleccionados
        if (ids.length > 0) {

            $('#ids').val(ids);
            $('form#exportarClientes').submit();
            $('body').trigger('click');
        }

    });
    //Nueva cotizacion
    $(opcionesModal).on("click", botones.cotizaciones, function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        //Cerrar modal de opciones
        opcionesModal.modal('hide');

        var cliente_id = $(this).attr("data-id");
       // console.log(cliente_id);

        //Limpiar formulario
        crearCotizacionesForm.append('<input type="hidden" name="cliente_id" value="'+ cliente_id +'" />');
        //Enviar formulario
        crearCotizacionesForm.submit();
    $('body').trigger('click');
    });
    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                nombre: '',
                telefono: '',
                correo: '',
                tipo:'',
                categoria:'',
                estado: '',
                identificacion: '',
                erptkn: tkn
            }
        }).trigger('reloadGrid');

    };
    return{
        init: function () {
            tabla();
            eventos();

        },
        redimencionar: function () {
            redimencionar_tabla();
        }
    };

})();

$(function () {
    tablaClientes.init();
    tablaClientes.redimencionar();
});
