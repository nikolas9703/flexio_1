var tablaHistorial={};
var historial={
    settings: {
        url: phost() + 'contabilidad/ajax_historial',
        gridId : "#historialGrid",
        gridObj : $("#historialGrid"),
        exportarLista: $("exportarTablaHistorial"),
        opcionesModal:  $('#opcionesModal')
    },
    botones:{
        opciones: "button.viewOptions",
        limpiar: $("#clearBtn"),
        buscar: $("#searchBtn"),
        exportar: $("#exportarTablaHistorial"),
    },
    init:function(){
        tablaHistorial = this.settings;
        this.tablaGrid();
        this.redimencionar();
        this.eventos();
    },
    eventos:function(){
        this.botones.limpiar.click(function(e) {
            $('#buscarHistorialForm').find('input[type="text"]').prop("value", "");
            $('#buscarHistorialForm').find('select').prop("value", "");
            $("#centro_contable").val(null).trigger("change");
            historial.recargar();
        });

        this.botones.buscar.click(function(e) {

            var nombre = $('#nombre').val();
            var fecha1 = $('#start').val();
            var fecha2 = $('#end').val();
            var centros = $("#centro_contable").val();
            var myPostData = tablaHistorial.gridObj.jqGrid('getGridParam', 'postData');
            delete myPostData.campo.centro_contable;
            if (nombre !== "" || fecha1 !== "" || fecha2 !== "" || centros !=="") {
                //Reload Grid
                tablaHistorial.gridObj.setGridParam({
                    url: tabla.url,
                    datatype: "json",
                    postData: {
                        campo:{
                            codigo: nombre,
                            centro_contable: centros,
                            fecha_min: fecha1,
                            fecha_max: fecha2,
                        },
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
            }
        });

        this.botones.exportar.click(function(e){
            //e.preventDefault();
            //e.stopPropagation();

             if ($('#tabla').is(':visible') === true) {
                 var ids = [];
                 ids = tablaHistorial.gridObj.jqGrid('getGridParam', 'selarrrow');

                 //Verificar si hay seleccionados
                 if (ids.length > 0) {
                     $('#ids').val(ids);
                 }else{
                     var centros = $('#centro_contable').val();
                     var fecha_min = $('#start').val();
                     var fecha_max = $('#end').val();
                     var codigo = $('#nombre').val();

                     
                     $('#exportar_centro_contable').val(centros);
                     $('#exportar_fecha_min').val(fecha_min);
                     $('#exportar_fecha_max').val(fecha_max);
                     $('#exportar_transaccion').val(codigo);
                     $('#cuenta_ids').val(window.campo.cuenta_ids);
                 }
                 $('#exportar_historial_cuenta').submit();
                 $('body').trigger('click');
             }
        });

        tablaHistorial.gridObj.on("click", this.botones.opciones, function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            var id = $(this).data("id");

            var rowINFO = $.extend({},tablaHistorial.gridObj.getRowData(id));

            var options = rowINFO.link;
            tablaHistorial.opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO.no_transaccion);
            tablaHistorial.opcionesModal.find('.modal-body').empty().append(options);
            tablaHistorial.opcionesModal.find('.modal-footer').empty();
            tablaHistorial.opcionesModal.modal('show');

        });

    },
    redimencionar:function(){
        $(window).resizeEnd(function() {
            $(".ui-jqgrid").each(function(){
                var w = parseInt( $(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_","");
                $("#"+gId).setGridWidth(w);
            });
        });
    },
    tablaGrid:function(){
        tablaHistorial.gridObj.jqGrid({
            url: tablaHistorial.url,
            datatype: "json",
            colNames: ['','No. Transacci&oacute;n','Fecha','Centro contable','Transacci&oacute;n','D&eacute;bito','Cr&eacute;dito','',''],
            colModel: [
                {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
                {name:'no_transaccion',index:'no_transaccion',width: 30, sortable:false},
                {name:'fecha',index:'fecha', sortable:false,width: 20},
                {name:'centro_contable',index:'centro_contable',width: 30, sortable:false},
                {name:'transaccion', index:'transaccion', formatter: 'text',width: 30, sortable:false},
                {name:'debito', index:'debito', formatter: 'text',width: 20, sortable:false, align:'center'},
                {name:'credito', index:'credito', formatter: 'text',width: 20, sortable:false, align:'center'},
                {name:'opciones', index:'opciones', sortable:false, width: 30,align:'center'},
                {name:'link', index:'link', hidedlg:true, hidden: true}
            ],
            mtype: "POST",
            postData: {
                campo: typeof window.campo !== 'undefined' ? window.campo : {},
                erptkn:tkn
            },

            hiddengrid: false,
            loadtext: '<p>Cargando...</p>',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: true,
            height: 'auto',
            page: 1,
            pager : tablaHistorial.gridId+"Pager",
            rowNum:10,
            autowidth: true,
            rowList:[10,20,30],
            sortname: 'id',
            sortorder: "desc",
            beforeProcessing: function(data, status, xhr){
                //Check Session
                if( $.isEmptyObject(data.session) === false){
                    window.location = phost() + "login?expired";
                }},
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaHistorialGrid_cb, #jqgh_tablaHistorialGrid_link").css("text-align", "center");
            },
            beforeRequest: function(data, status, xhr){},
            loadComplete: function(data, status, xhr){

                if($("#historialGrid").getGridParam('records') === 0 ){
                    $('#gbox_historialGrid').hide();
                    $('#historialGridNoRecords').empty().append('No se encontraron Transacciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    $('#gbox_historialGrid').show();
                    $('#historialGridNoRecords').empty();
                }

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                tablaHistorial.gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
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
    },
    recargar: function() {

        //Reload Grid
        var myPostData = tablaHistorial.gridObj.jqGrid('getGridParam', 'postData');
        delete myPostData.campo.centro_contable;
        tablaHistorial.gridObj.setGridParam({
            url: tablaHistorial.url,
            datatype: "json",
            postData: {
                campo:{
                    centro_contable:[],
                    codigo:'',
                    fecha_min:'',
                    fecha_max:'',
                },

                erptkn: tkn
            }
        }).trigger('reloadGrid');

    }
};

$(document).ready(function(){
    historial.init();
    $("#centro_contable").select2({
        width:'100%'
    });
    $("#start").datepicker({
		//defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#end").datepicker( "option", "minDate", selectedDate );
		}
	});
	$("#end").datepicker({
		//defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#start").datepicker( "option", "maxDate", selectedDate );
	    }
	});
});
