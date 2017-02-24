//Modulo Tabla Transferencias
var tablaTransferencias = (function () {

    var url = 'cajas/ajax-listar-transferencias';
    var grid_id = "transferenciasGrid";
    var grid_obj = $("#transferenciasGrid");
    var opcionesModal = $('#opcionesModal');

    var botones = {
        opciones: ".viewOptionsss",
        editar: "",
        duplicar: "",
        desactivar: "",
        buscar: "#searchBtn",
        limpiar: "#clearBtn"
    };

    var tabla = function () {

        var scaja_id = '';
        if (typeof caja_id != 'undefined') {
            scaja_id = caja_id;
        }

        //inicializar jqgrid
        grid_obj.jqGrid({
            url: phost() + url,
            datatype: "json",
            colNames: [
                'No. Transferencia',
                'Fecha',
                'Cuenta de Origen',
                'Monto',
                'Metodo de pago',
                'Estado',
                'Opciones',
                ''
            ],
            colModel: [
                {name: 'Transferencia', index: 'numero', width: 30, align: 'left'},
                {name: 'Fecha', index: 'fecha', width: 30, sortable: false, align: 'left'},
                {name: 'Cuenta de Origen', index: 'cuenta', width: 60, sortable: false, align: 'left'},
                {name: 'Monto', index: 'monto', width: 40, sortable: false, align: 'left'},
                {name: 'Metodo de pago', index: 'metodo', width: 40, sortable: false, align: 'left'},
                {name: 'Estado', index: 'estado', width: 40, sortable: false, align: 'left'},
                {name: 'options', index: 'options', width: 20},
                {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
                caja_id: scaja_id
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: "#" + grid_id + "Pager",
            loadtext: '<p>Cargando...</p>',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: false,
            sortname: 'numero',
            sortorder: "DESC",
            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {},
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data) {

                //check if isset data
                if (data['total'] == 0) {
                    $('#gbox_' + grid_id).hide();
                    $('#' + grid_id + 'NoRecords').empty().append('No se encontraron datos de transferencias.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#' + grid_id + 'NoRecords').hide();
                    $('#gbox_' + grid_id).show();
                }
            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            },
        });

        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaTransferencias.redimensionar();
        });
    };

    //Inicializar Eventos de Botones
    var eventos = function () {

        //Boton de Opciones
        grid_obj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");
            //console.log(id);
            //var rowINFO = grid_obj.getRowData(id);
           var rowINFO =  $.extend({}, grid_obj.getRowData(id));
           // var option = rowINFO["link"];
                        var options = rowINFO.link;
           // options = option.replace(/0000/gi, id);

            //evento para boton collapse sub-menu Accion Personal
            opcionesModal.on('click', 'a[href="#collapse' + id + '"]', function () {
                opcionesModal.find('#collapse' + id).collapse();
            });
            $('#id').val(id);
            $('form#transferirForm').submit();
            $('body').trigger('click');
           // var nombre =  rowINFO["No. Transferencia"];
             var nombre =  $(rowINFO.Transferencia).text();
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + nombre + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');

        });

        //Boton de Buscar
        $(botones.buscar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            buscar();
        });

        //Boton de Reiniciar jQgrid
        $(botones.limpiar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            recargar();
            limpiarCampos();
        });
    };

    //Buscar
    var buscar = function () {

        var nombre = $('#nombre').val();
        var centro = $('#centro').val();
        var limite = $('#limite').val();
        var responsable_id = $('#responsable_id').val();

        if (nombre != "" || centro != "" || limite != "" || responsable_id != "")
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + url,
                datatype: "json",
                postData: {
                    nombre: nombre,
                    centro_id: centro,
                    limite: limite,
                    responsable_id: responsable_id,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };

    //Reload al jQgrid
    var recargar = function () {

        //Reload Grid
        grid_obj.setGridParam({
            url: phost() + url,
            datatype: "json",
            postData: {
                nombre: '',
                centro_id: '',
                limite: '',
                responsable_id: '',
                erptkn: tkn
            }
        }).trigger('reloadGrid');
    };


    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        $('#buscarCajasForm').find('input[type="text"]').prop("value", "");
        $('#buscarCajasForm').find('select').find('option:eq(0)').prop('selected', 'selected');
    };

    return{
        init: function () {
            tabla();
            eventos();
        },
        recargar: function () {
            //reload jqgrid
            recargar();
        },
        redimensionar: function () {
            //Al redimensionar ventana
            $(".ui-jqgrid").each(function () {
                var w = parseInt($(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_", "");
                $("#" + gId).setGridWidth(w);
            });
        }
    };

})();

tablaTransferencias.init();
