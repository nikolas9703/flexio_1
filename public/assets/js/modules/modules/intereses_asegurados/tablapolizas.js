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
var tablaPolizas = (function () {
    if (typeof uuid_cotizacion === 'undefined') {
        uuid_cotizacion = "";
    }
    var tablaUrl = phost() + 'intereses_asegurados/ajax_listar_polizas';
    var gridId = "PolizasGrid";
    var gridObj = $("#PolizasGrid");
    var opcionesModal = $('#opcionesPolizasModal');
    var formularioBuscar = '';
    var grid_obj = $("#PolizasGrid");
	
	var id_interes=$('input[name="campo[uuid]').val();
	
    var botones = {
        opciones: ".viewOptions",
        exportar: "#exportarPolizasBtn"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
			datatype: "json",
			colNames: ['','No. Póliza','Cliente','Aseguradora','Ramo','Inicio de vigencia','Fin de vigencia','Estado','Opciones','',''],
			colModel: [
						{name:'id', index:'id', hidedlg:true,key: true, hidden: true },
						{name:'pol_polizas.numero', index:'pol_polizas.numero',sorttype:"text",sortable:true,width:150},
						{name:'cli_clientes.nombre', index:'cli_clientes.nombre',sorttype:"text",sortable:true,width:180},
						{name:'seg_aseguradoras.nombre', index:'seg_aseguradoras.nombre',sorttype:"text",sortable:true,width:180},
						{name:'pol_polizas.ramo', index:'pol_polizas.ramo',sorttype:"text",sortable:true,width:130},
						{name:'inicio_vigencia', index:'inicio_vigencia',sorttype:"text",sortable:true,width:185},
						{name:'fin_vigencia', index:'fin_vigencia',sorttype:"text",sortable:true,width:160},
						{name:'estado', index:'estado',sorttype:"text",sortable:true,width:110, search:false },
						{name:'opciones', index:'opciones', sortable:false, align:'center',width:150, search:false},
						{name:'link', index:'link', hidedlg:true, hidden: true},
						{name:'linkEstado', index:'linkEstado', hidedlg:true, hidden: true}
			],
			mtype: "POST",
			postData: {
				id_interes:id_interes,
				erptkn:tkn
			},
			height: "auto",
			autowidth: true,
			rowList: [10, 20,50, 100],
			rowNum: 10,
			page: 1,
			pager: "#pager_polizas",
			loadtext: '<p>Cargando...',
			hoverrows: false,
			viewrecords: true,
			multiselect: true,
			refresh: true,
			search:true,
			gridview: true,
			sortname: 'fin_vigencia',
			sortorder: "DESC",
			beforeRequest: function(data, status, xhr){
				//$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#jqgh_PolizasGrid_cb").css("text-align", "center");
			},
			loadComplete: function(data){
				//---------
				// Cargar plugin jquery Sticky Objects
				//----------
				//add class to headers
				$("#PolizasGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
				$("#PolizasGrid").find('div.tree-wrap').children().removeClass('ui-icon');
				//floating headers
				$('#gridHeader').sticky({
					getWidthFrom: '.ui-jqgrid-view',
					className:'jqgridHeader'
				});
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			}
        });
        //Al redimensionar ventana
        $(window).resizeEnd(function(){
            tablaPolizas.redimencionar_tabla();
        });
		
		grid_obj.jqGrid('filterToolbar', { stringResult: true, searchOnEnter: false, defaultSearch: "cn" });
		gridObj.jqGrid('setGridWidth', "100%");
		//$(".clearsearchclass").hide();
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
		
		//Exportar Seleccionados del jQgrid
		var ids = [];
		ids = gridObj.jqGrid('getGridParam', 'selarrrow');
		//Verificar si hay seleccionados
		if (ids.length > 0) {

			$('#ids').val(ids);
			$('#interes_exp').val(id_interes);
			$('form#exportarPolizasIntereses').submit();
			$('body').trigger('click');
		}
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
    tablaPolizas.init();
    $("#jqgh_PolizasGrid_cb span").removeClass("s-ico");
    $('#jqgh_PolizasGrid_options span').removeClass("s-ico");
	
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