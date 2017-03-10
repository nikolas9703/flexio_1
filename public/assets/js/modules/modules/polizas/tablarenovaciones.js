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
var tablaRenovaciones = (function () {
    if (typeof uuid_cotizacion === 'undefined') {
        uuid_cotizacion = "";
    }
    var tablaUrl = phost() + 'polizas/ajax_listar_renovaciones';
    var gridId = "tablaRenovacionesGrid";
    var gridObj = $("#tablaRenovacionesGrid");
    var opcionesModal = $('#opcionesRenovacionesModal');
    var formularioBuscar = '';
    var grid_obj = $("#tablaRenovacionesGrid");
	
	var uuid_poliza=$('input[name="campo[uuid]').val();
	
    var botones = {
        opciones: ".viewOptions",
        exportar: "#exportarPBtn"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'No. de póliza', 'Inicio de vigencia', 'Fin de vigencia', 'Fecha de renovación', 'Usuario', '', '', '', ''],
            colModel: [
                {name: 'id', index: 'id', width: 30, align: "center", sortable: false, hidden: true},
                {name: 'pol_polizas.numero', index: 'pol_polizas.numero', width: 30},
                {name: 'pol_polizas.inicio_vigencia', index: 'pol_polizas.inicio_vigencia', width: 30},
                {name: 'pol_polizas.fin_vigencia', index: 'pol_polizas.fin_vigencia', sortable:true, width: 30 },
                {name: 'pol_polizas.updated_at', index: 'pol_polizas.updated_at', sortable:true, width: 30 },
                {name: 'usuarios.nombre', index: 'usuario.nombre', width: 30},
                {name: 'options', index: 'options', width: 30, align: "center", sortable: false, search:false},
                {name: 'link', index: 'link', width: 30, align: "center", sortable: false, hidden: true},
                {name: 'modalstate', index: 'modalstate', width: 30, align: "center", sortable: false, hidden: true},
                {name: 'massState', index: 'massState', width: 30, align: "center", sortable: false, hidden: true}
            ],
            postData: {
                erptkn: tkn,
				uuid_poliza: uuid_poliza
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: "#RenovacionesPager",
            loadtext: '<p>Cargando...</p>',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            sortname: "numero",
            sortorder: "DESC",
            multiselect: true,

            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaRenovacionesGrid_cb, #jqgh_tablaRenovacionesGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data, status, xhr) {

                /*if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Renovaciones asegurados.').css({"color": "#868686", "padding": "30px 0 0"}).show();
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
            tablaRenovaciones.redimencionar_tabla();
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
            console.log(id);
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.numero).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

    };
	
    //Boton de Exportar Renovaciones
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

				$('#idsrenovaciones').val(ids);
				$('#solicitud_exp').val(uuid_poliza);
				$('form#exportarRenovacionesPolizas').submit();
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
                numero: '',
                tipo: '',
                identificacion: '',
                estado: '',
				solicitud: solicitud,
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
    tablaRenovaciones.init();
    $("#jqgh_tablaRenovacionesGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaRenovacionesGrid_options span').removeClass("s-ico");
	
});

$('#tipo').on("change", function () {
    var tipo = $(this).val();
    tipo === '1' ? $('#identificacion_label').text('Identificación') : '';
    tipo === '2' ? $('#identificacion_label').text('No. de liquidación') : '';
    tipo === '3' || tipo === '4' || tipo === '8' ? $('#identificacion_label').text('No. de serie') : '';
    tipo === '5' ? $('#identificacion_label').text('No. de cédula') : '';
    tipo === '6' ? $('#identificacion_label').text('No. de orden o contrato') : '';
    tipo === '7' ? $('#identificacion_label').text('Dirección') : '';
});

if (typeof validarenovacion!="undefined" && validarenovacion==0) {
    $("#id_tab_renovaciones").hide();
}