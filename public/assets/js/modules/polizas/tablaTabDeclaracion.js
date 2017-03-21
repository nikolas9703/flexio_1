function removeA(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax = arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}
var tablaDeclaraciones = (function () {

    var tablaUrl = phost() + 'endosos/ajax_listar_endosos';
    var gridId = "tablaDeclaracionesGrid";
    var gridObj = $("#tablaDeclaracionesGrid");
    var opcionesModal = $('#opcionesDeclaracionesModal');
    var formularioBuscar = '';
    var grid_obj = $("#tablaDeclaracionesGrid");
	
	var id_poliza = $('input[name="campo[id]').val();
	
    var botones = {
        opciones: ".viewOptions",
        exportar: "#exportarPBtn"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames:['','No. endoso','Cliente','Aseguradora','Ramo/Riesgo','No. póliza','Fecha de creación','Tipo','Estado','','','',],
            colModel:[
            {name:'id',index:'id', width: 0, align: "center", sortable: false, hidden: true},
            {name:'endoso', index:'endoso', width:10},
            {name:'cliente', index:'cliente_id', width:10 },
            {name:'aseguradora', index:'aseguradora_id', width:10 },
            {name:'ramo', index:'id_ramo', width:10 },
            {name:'poliza', index:'id_poliza', width: 10  },
            {name:'fecha', index:'fecha_creacion', width: 10  },
            {name:'tipo', index:'tipo', width: 10 },
            {name:'estado', index:'estado', width: 10 },
            {name:'link', index:'link', width:10, align:"center", sortable:false, resizable:false},
            {name:'options', index:'options', width: 10, align: "center", sortable: false, hidden: true},
            {name:'modalstate', index:'modalstate', width: 10, align: "center", sortable: false, hidden: true},
            ],
            postData: {
                erptkn: tkn,
				id_poliza: id_poliza,
                modulo: 'Polizas',
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: "#DeclaracionesPager",
            loadtext: '<p>Cargando...</p>',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            sortname: "estado",
            sortorder: "ASC",
            multiselect: true,

            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaDeclaracionesGrid_cb, #jqgh_tablaDeclaracionesGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data, status, xhr) {

                /*if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron intereses asegurados.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                }*/

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                //gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className: 'jqgridHeader'
                });

                //Arreglar tamaño de TD de los checkboxes
                //FALTA ADAPTAR EL CODIGO PARA QUE LOS CHECKBOX SE VEAN BIEN
                $('#jqgh_' + gridId + "_cb").css("text-align", "center");
                $('.s-ico').removeAttr('style');
            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            }
        });
        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaDeclaraciones.redimencionar_tabla();
        });
		
		grid_obj.jqGrid('filterToolbar', { stringResult: true, searchOnEnter: false, defaultSearch: "cn" });
    };



    var eventos = function () {
        //Bnoton de Opciones
		gridObj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            var nombre = $(this).attr("data-nombre")
            var rowINFO = $.extend({}, gridObj.getRowData(id));

            console.log(rowINFO.modalstate);
            var options = rowINFO.options;

            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + nombre+ '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

    };
	
    //Boton de Exportar Intereses
    $(botones.exportar).on("click", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
		if($('#id_tab_polizas').is(':visible') == true){
			//Exportar Seleccionados del jQgrid
			var ids = [];
			ids = gridObj.jqGrid('getGridParam', 'selarrrow');
			//Verificar si hay seleccionados
			if (ids.length > 0) {

                $("#ids_declaraciones").val(ids);
                console.log(ids);
                $("form#exportarDeclaraciones").submit();
                $('body').trigger('click');

				
				if($("#cb_"+gridId).is(':checked')) {
					$("#cb_"+gridId).trigger('click');
				}
				else
				{
					$("#cb_"+gridId).trigger('click');
					$("#cb_"+gridId).trigger('click');
				}
			}
		};
    });
    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
				id_poliza: id_poliza,
                modulo: 'Polizas',
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
    tablaDeclaraciones.init();
    $("#jqgh_tablaDeclaracionesGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaDeclaracionesGrid_options span').removeClass("s-ico");
	
});

/*
$('#tipo').on("change", function () {
    var tipo = $(this).val();
    tipo === '1' ? $('#identificacion_label').text('Identificación') : '';
    tipo === '2' ? $('#identificacion_label').text('No. de liquidación') : '';
    tipo === '3' || tipo === '4' || tipo === '8' ? $('#identificacion_label').text('No. de serie') : '';
    tipo === '5' ? $('#identificacion_label').text('No. de cédula') : '';
    tipo === '6' ? $('#identificacion_label').text('No. de orden o contrato') : '';
    tipo === '7' ? $('#identificacion_label').text('Dirección') : '';
});*/