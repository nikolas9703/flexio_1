$(document).ready(function () {

    $('#searchBtn').bind('click');


    $(function () {

        var grid = $("#AseguradorasGrid");
        grid.jqGrid({
            url: phost() + 'aseguradoras/ajax-listar',
            datatype: "json",
            colNames: ['', 'Nombre', 'RUC', 'Teléfono', 'E-mail', 'Dirección', '', ''],
            colModel: [
                {name: 'id', index: 'id', hidedlg: true, key: true, hidden: true},
                {name: 'nombre', index: 'nombre', sorttype: "text", sortable: true, width: 150},
                {name: 'ruc', index: 'ruc', sorttype: "text", sortable: true, width: 150},
                {name: 'telefono', index: 'telefono', sorttype: "text", sortable: true, width: 150},
                {name: 'email', index: 'email', sorttype: "text", sortable: true, width: 150},
                {name: 'direccion', index: 'direccion', sorttype: "text", sortable: true, width: 150},
                {name: 'opciones', index: 'opciones', sortable: false, align: 'center'},
                {
                    name: 'link',
                    index: 'link',
                    align: "center",
                    sortable: false,
                    resizable: false,
                    hidden: true,
                    hidedlg: true
                }
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: "#pager_aseguradoras",
            loadtext: '<p>Cargando...',
            hoverrows: false,
            viewrecords: true,
            multiselect: true,
            refresh: true,
            gridview: true,
            sortname: 'nombre',
            sortorder: "ASC",
            beforeRequest: function (data, status, xhr) {
            },
            loadComplete: function (data) {

                //check if isset data
                if (data.total == 0) {
                    $('#gbox_AseguradorasGrid').hide();
                    $('.NoRecordsEmpresa').empty().append('No se encontraron Aseguradoras.').css({
                        "color": "#868686",
                        "padding": "30px 0 0"
                    }).show();
                }
                else {
                    $('.NoRecords').hide();
                    $('#gbox_AseguradorasGrid').show();
                }

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                $("#AseguradorasGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                $("#AseguradorasGrid").find('div.tree-wrap').children().removeClass('ui-icon');
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
//-------------------------
        // Redimensioanr Grid al cambiar tamaño de la ventanas.
        //-------------------------
        $(window).resizeEnd(function () {
            $(".ui-jqgrid").each(function () {
                var w = parseInt($(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_", "");
                $("#" + gId).setGridWidth(w);
            });
        });

        $("#AseguradorasGrid").on("click", ".viewOptions", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");
            console.log("id -" + id);
            var rowINFO = $("#AseguradorasGrid").getRowData(id);
            console.log(rowINFO);
            var options = rowINFO["link"];
            //Init boton de opciones
            $('#opcionesModal').find('.modal-title').empty().html('Opciones: ' + rowINFO["nombre"] + '');
            $('#opcionesModal').find('.modal-body').empty().html(options);
            $('#opcionesModal').find('.modal-footer').empty();
            $('#opcionesModal').modal('show');
        });


    });


    $('#searchBtn').on("click", function (e) {

        var nombre = $('#nombre').val();
        var ruc = $('#ruc').val();
        var telefono = $('#telefono').val();
        var email = $('#email').val();
        if (nombre != "" || ruc != "" || telefono != "" || email != "") {
            //Reload Grid
            $("#AseguradorasGrid").setGridParam({
                url: phost() + 'aseguradoras/ajax-listar',
                datatype: "json",
                postData: {
                    nombre: nombre,
                    ruc: ruc,
                    telefono: telefono,
                    email: email,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
        else {
            $("#AseguradorasGrid").setGridParam({
                url: phost() + 'aseguradoras/ajax-listar',
                datatype: "json",
                postData: {
                    nombre: "",
                    ruc: "",
                    telefono: "",
                    email: "",
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    });
    $('#exportarAseguradoraLnk').on("click", function (e) {

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        console.log("exportar");
         if($('#tabla').is(':visible') == true) {

         //Exportar Seleccionados del jQgrid
         var ids = [];
         ids = $("#AseguradorasGrid").jqGrid('getGridParam', 'selarrrow');

         //Verificar si hay seleccionados
         if(ids.length > 0){

         $('#ids').val(ids);
         $('form#exportarAseguradoras').submit();
         $('body').trigger('click');
         }
         }

    });


});


$('#clearBtn').on("click", function (e) {
    e.preventDefault();

    $("#AseguradorasGrid").setGridParam({
        url: phost() + 'aseguradoras/ajax-listar',
        datatype: "json",
        postData: {
            nombre: '',
            ruc: '',
            telefono: '',
            email: '',
            erptkn: tkn
        }
    }).trigger('reloadGrid');

    //Reset Fields
    $('#nombre, #ruc, #telefono, #email').val('');
});

