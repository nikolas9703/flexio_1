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
var tablaInteresesAsegurados = (function () {
    if (typeof uuid_cotizacion === 'undefined') {
        uuid_cotizacion = "";
    }
    var tablaUrl = phost() + 'polizas/ajax_listar_intereses';
    var gridId = "tablaInteresesAseguradosGrid";
    var gridObj = $("#tablaInteresesAseguradosGrid");
    var opcionesModal = $('#opcionesInteresesModal');
    var formularioBuscar = '';
    var grid_obj = $("#tablaInteresesAseguradosGrid");
	
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
            colNames: ['', 'No. de interés asegurado', 'Tipo de interés', 'Fecha inclusión', 'Fecha exclusión', 'Usuario', 'Estado', '', '', '', ''],
            colModel: [
                {name: 'id', index: 'id', width: 30, align: "center", sortable: false, hidden: true},
                {name: 'numero', index: 'numero', width: 30},
                {name: 'seg_ramos_tipo_interes.nombre', index: 'seg_ramos_tipo_interes.nombre', width: 35},
                {name: 'int_intereses_asegurados_detalles.fecha_inclusion', index: 'int_intereses_asegurados_detalles.fecha_inclusion', sortable:true, width: 16 },
                {name: 'int_intereses_asegurados_detalles.fecha_exclusion', index: 'int_intereses_asegurados_detalles.fecha_exclusion', sortable:true, width: 16 },
                {name: 'usuarios.nombre', index: 'usuario.nombre', width: 35},
                {name: 'estado', index: 'estado', width: 15, search:false },
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
            pager: "#InteresesPager",
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
                $(this).closest("div.ui-jqgrid-view").find("#tablaInteresesAseguradosGrid_cb, #jqgh_tablaInteresesAseguradosGrid_link").css("text-align", "center");
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
            tablaInteresesAsegurados.redimencionar_tabla();
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

				$('#ids_intereses').val(ids);
                console.log(ids);
				$('#solicitud_exp').val(uuid_poliza);
				$('form#exportarInteresesPolizas').submit();
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
    tablaInteresesAsegurados.init();
    $("#jqgh_tablaInteresesAseguradosGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaInteresesAseguradosGrid_options span').removeClass("s-ico");
	
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