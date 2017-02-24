
var multiselect = window.location.pathname.match(/entregas_alquiler/g) ? true : false;

var tablaEntregasAlquiler = (function(){

    var tablaUrl = phost() + 'entregas_alquiler/ajax-listar';
    var gridId = "tablaEntregasAlquilerGrid";
    var gridObj = $("#" + gridId);
    var opcionesModal = $('#optionsModal, #opcionesModal');
    var formularioBuscar = $('#buscarEntregasAlquilerForm');

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarEntregasAlquiler"
    };

    var tabla = function(){
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames:['','No. Entrega','Fecha de entrega','No. Contrato','Cliente','Centro de facturaci&oacute;n','Estado','',''],
            colModel:[
                {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
                {name:'codigo', index:'codigo', width:55, sortable:true},
                {name:'fecha_entrega', index:'fecha_entrega', width:50, sortable:true},
                {name:'contrato_id', index:'contrato_id', width:50,  sortable:false, },
                {name:'cliente_id', index:'cliente_id', width:50,  sortable:false, },
                {name:'centro_facturacion_id', index:'centro_facturacion_id', width:50,  sortable:false, },
                {name:'estado_id', index:'estado_id', width:50,  sortable:false, },
                {name:'options', index:'options',width: 40},
                {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
            ],
            postData: {
                erptkn: tkn,
                contrato_alquiler_id:(typeof contrato_alquiler_id === 'undefined' || _.toString(window.contrato_alquiler_id) == "[object HTMLInputElement]") ? '' : window.contrato_alquiler_id,
                item_id:(typeof window.sp_item_id !== 'undefined') ? window.sp_item_id : '',
                campo: typeof window.campo !== 'undefined' ? window.campo : {}
            },
            height: "auto",
            autowidth: true,
            rowList: [10,20,50,100],
            rowNum: 10,
            page: 1,
            pager: gridId+"Pager",
            loadtext: '<p>Cargando...',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: multiselect,
            sortname: 'codigo',
            sortorder: "DESC",
            beforeProcessing: function(data, status, xhr){
                if( $.isEmptyObject(data.session) === false){
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaProveedoresGrid_cb, #jqgh_tablaProveedoresGrid_link").css("text-align", "center");
            },
            loadComplete: function(data, status, xhr){

                if(gridObj.getGridParam('records') === 0 ){
                    $('#gbox_'+gridId).hide();
                    $('#'+gridId+'NoRecords').empty().append('No se encontraron Entregas de alquiler.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    $('#gbox_'+gridId).show();
                    $('#'+gridId+'NoRecords').empty();
                }

                if(multiselect)
                {
                    //header flotante
                    gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
                    //floating headers
                    $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view',
                        className:'jqgridHeader'
                    });
                }
            },
            onSelectRow: function(id){
                $(this).find('tr#'+ id).removeClass('ui-state-highlight');
            }
        });
    };

    var eventos = function(){
        //Bnoton de Opciones
        gridObj.on("click", botones.opciones, function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var id = $(this).attr("data-id");

            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: '+ $(rowINFO.codigo).text() +'');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });
        $(botones.exportar).click(function(){

            //contiene un arreglo con los identificadores de los proveedores (uuid_proveedor)
            var registros_jqgrid = gridObj.jqGrid('getGridParam','selarrrow');

            if(registros_jqgrid.length)
            {
                var url = phost() + "entregas_alquiler/ajax-exportar";
                var vars = "";
                $.each(registros_jqgrid, function(i, val){
                    vars += '<input type="hidden" name="ids[]" value="'+ val +'">';
                });
                var form = $(
                    '<form action="' + url + '" method="post" style="display:none;">' +
                    vars +
                    '<input type="hidden" name="erptkn" value="' + tkn + '">' +
                    '<input type="submit">' +
                    '</form>'
                );
                $('body').append(form);
                form.submit();
            }
        });
        //boton limpiaar
        $(botones.limpiar).click(function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            formularioBuscar.find('input[type="text"]').prop("value", "");
            formularioBuscar.find('select.select2').val('').change();
            formularioBuscar.find('select').prop("value", "");
            recargar();
        });
        //boton Buscar
        $(botones.buscar).click(function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var codigo = $('#codigo').val();
            var no_contrato = $('#no_contrato').val();
            var centro_facturacion_id = $('#centro_facturacion_id').val();
            var cliente_id = $('#cliente_id').val();
            var fecha_desde= $('#fecha_desde').val();
            var fecha_hasta= $('#fecha_hasta').val();
            var estado_id = $('#estado_id').val();

            if (codigo !== "" || cliente_id !== "" || fecha_desde !== "" || fecha_hasta !== "" || estado_id !== "" || no_contrato !== "" || centro_facturacion_id !== "") {
                //Reload Grid
                gridObj.setGridParam({
                    url: tablaUrl,
                    datatype: "json",
                    postData: {
                        codigo: codigo,
                        no_contrato: no_contrato,
                        centro_facturacion_id: centro_facturacion_id,
                        cliente_id: cliente_id,
                        fecha_desde: fecha_desde,
                        fecha_hasta: fecha_hasta,
                        estado_id: estado_id,
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
            }
        });
    };
    var recargar = function(){
        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                codigo: '',
                no_contrato: '',
                centro_facturacion_id: '',
                cliente_id: '',
                fecha_desde: '',
                fecha_hasta: '',
                estado_id: '',
                erptkn: tkn
            }
        }).trigger('reloadGrid');
    };
    var redimencionar_tabla = function(){
        $(window).resizeEnd(function() {
            $(".ui-jqgrid").each(function(){
                var w = parseInt( $(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_","");
                $("#"+gId).setGridWidth(w);
            });
        });
    };

    return{
        init:function(){
            tabla();
            eventos();
            redimencionar_tabla();
        }
    };

})();

$(function(){
    tablaEntregasAlquiler.init();
});
