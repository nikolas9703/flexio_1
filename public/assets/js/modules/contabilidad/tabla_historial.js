var tablaHistorial={};
var historial={
    settings: {
        url: phost() + 'contabilidad/ajax_historial/'+uuid_cuenta,
        gridId : "#historialGrid",
        gridObj : $("#historialGrid"),
        exportarLista: $("exportarTablaHistorial"),
        opcionesModal:  $('#opcionesModal')
    },
    botones:{
        opciones: "button.viewOptions",
        limpiar: $("#clearBtn"),
        buscar: $("#searchBtn")
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
            historial.recargar();
        });

        this.botones.buscar.click(function(e) {
            console.log("asdasdad");

            var nombre = $('#nombre').val();
            var fecha1 = $('#start').val();
            var fecha2 = $('#end').val();

            if (nombre !== "" || fecha1 !== "" || fecha2 !== "") {
                //Reload Grid
                tablaHistorial.gridObj.setGridParam({
                    url: tabla.url,
                    datatype: "json",
                    postData: {
                        nombre: nombre,
                        fecha1: fecha1,
                        fecha2: fecha2,
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
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
            console.log(tablaHistorial.opcionesModal);
        });

        tablaHistorial.exportarLista.on('click',this.botones.exportarLista,function(e){
            console.log("asdasd");
            e.preventDefault();
            e.stopPropagation();
            var uuid = $(this).data("id");
            $('#historial_exportar').val(uuid);
            $('#formularioExportarLista').submit();
            tablaHistorial.opcionesModal.modal('hide');
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
            colNames: ['','No. Transacci&oacute;n','Fecha','Transacci&oacute;n','D&eacute;bito','Cr&eacute;dito','',''],
            colModel: [
                {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
                {name:'no_transaccion',index:'no_transaccion', sortable:false},
                {name:'fecha',index:'fecha', sortable:false},
                {name:'nombre', index:'nombre', formatter: 'text', sortable:false},
                {name:'debito', index:'debito', formatter: 'text', sortable:false},
                {name:'credito', index:'credito', formatter: 'text', sortable:false, align:'center'},
                {name:'opciones', index:'opciones', sortable:false, align:'center'},
                {name:'link', index:'link', hidedlg:true, hidden: true}
            ],
            mtype: "POST",
            postData: { erptkn:tkn},
            sortorder: "asc",
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
        tablaHistorial.gridObj.setGridParam({
            url: tablaHistorial.url,
            datatype: "json",
            postData: {
                nombre: '',
                fecha1:'',
                fecha2:'',
                erptkn: tkn
            }
        }).trigger('reloadGrid');

    }
};

$(document).ready(function(){
    historial.init();
});
