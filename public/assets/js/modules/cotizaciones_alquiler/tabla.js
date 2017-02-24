//modulo clientes
var tablaCotizaciones = (function () {
    if (typeof cliente_id === 'undefined') {
        cliente_id = "";
    }
    var tablaUrl = phost() + 'cotizaciones_alquiler/ajax-listar';
    var gridId = "tablaCotizacionesAlquilerGrid";
    var gridObj = $("#tablaCotizacionesAlquilerGrid");
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
            colNames: ['', 'No. Cotización', 'Cliente', 'Fecha de emisión', 'Válido hasta', 'Centro contable', 'Creado por', 'Estado', '', ''],
            colModel: [
                {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                {name: 'codigo', index: 'codigo', width: 40, sortable: true},
                {name: 'cliente', index: 'cliente', width: 70, sortable: true},
                {name: 'fecha_desde', index: 'fecha_desde', width: 40, sortable: false, },
                {name: 'fecha_hasta', index: 'fecha_hasta', width: 40, sortable: false, },
                {name: 'centro_contable_id', index: 'centro_contable_id', width: 50, sortable: false, },
                {name: 'vendedor', index: 'vendedor', width: 50, sortable: false},
                {name: 'estado', index: 'estado', width: 30, sortable: false},
                {name: 'options', index: 'options', width: 40},
                {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
            ],
            postData: {
                erptkn: tkn,
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
                $(this).closest("div.ui-jqgrid-view").find("#tablaCotizacionesAlquilerGrid_cb, #jqgh_tablaCotizacionesAlquilerGrid_link").css("text-align", "center");
            },
            loadComplete: function (data, status, xhr) {

                if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Cotizaciones.').css({"color": "#868686", "padding": "30px 0 0"}).show();
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
		
		$("#exportarLnk").on("click",function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			if($("#"+gridId).is(':visible') == true){
				//Exportar Seleccionados del jQgrid
				var ids = [];
				ids = gridObj.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(ids.length > 0){
					console.log(ids);
					$('#ids').val(ids);
					$('form#exportarCotizaciones').submit();
					$('body').trigger('click');
				}
	        }
		});

    };
    $(botones.limpiar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#buscarCotizacionesAlquilerForm').find('input[type="text"]').prop("value", "");
        $('#buscarCotizacionesAlquilerForm').find('select.chosen-select').prop("value", "");
        $('#buscarCotizacionesAlquilerForm').find('select').prop("value", "");
        $(".chosen-select").trigger("chosen:updated");

        recargar();
    });
    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var no_cotizacion = $('#codigo').val();
        var cliente = $('#cliente_id').val();
        var desde = $('#fecha_desde').val();
        var hasta = $('#fecha_hasta').val();
        var etapa = $('#etapa').val();
        var vendedor = $('#creado_por').val();
        var centro_contable_id = $('#centro_contable_id').val();

        if (no_cotizacion !== '' || cliente !== "" || desde !== "" || hasta !== "" || etapa !== "" || vendedor !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    campo:{
                    codigo: no_cotizacion,
                    cliente: cliente,
                    fecha_min: desde,
                    fecha_max: hasta,
                    estado: etapa,
                    centro_contable_id: centro_contable_id,
                    creado_por: vendedor},
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
                campo:{
                codigo: '',
                cliente: '',
                fecha_min: '',
                fecha_max: '',
                estado: '',
                creado_por: ''},
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
