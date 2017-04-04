//modulo clientes
var tablaAcreedores = (function () {
    if (typeof uuid_cotizacion === 'undefined') {
        uuid_cotizacion = "";
    }
    var tablaUrl = phost() + 'acreedores/ajax-listar';
    var gridId = "tablaAcreedoresGrid";
    var gridObj = $("#tablaAcreedoresGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = '';

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarLnk",
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'Nombre', 'Tel&eacute;fono', 'E-mail', 'Tipo de acreedor', 'Descuentos a colaboradores', 'Total a pagar', '', ''],
            colModel: [
                {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                {name: 'nombre', index: 'nombre', width: 55, sortable: true},
                {name: 'telefono', index: 'telefono', width: 50, sortable: false},
                {name: 'email', index: 'email', width: 70, sortable: false, },
                {name: 'tipo', index: 'tipo', width: 55, sortable: false},
                {name: 'descuentos', index: 'descuentos', width: 70, sortable: false},
                {name: 'total', index: 'total', width: 70, sortable: false},
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
            refresh: true,
            gridview: true,
            multiselect: true,
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
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron acreedores.').css({"color": "#868686", "padding": "30px 0 0"}).show();
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

        //Boton de Exportar Colaborador
		$(botones.exportar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			if($('#tabla').is(':visible') == true){

				//Exportar Seleccionados del jQgrid
				var ids = [];
					ids = gridObj.jqGrid('getGridParam','selarrrow');

				//Verificar si hay seleccionados
				if(ids.length > 0){

					$('#ids').val(ids);
			        $('form#exportar').submit();
			        $('body').trigger('click');
				}
	        }
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
    tablaAcreedores.init();
});
