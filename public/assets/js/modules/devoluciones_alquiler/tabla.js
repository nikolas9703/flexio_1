
var multiselect = window.location.pathname.match(/devoluciones_alquiler/g) ? true : false;

var tablaDevolucionesAlquiler = (function(){

    var tablaUrl = phost() + 'devoluciones_alquiler/ajax-listar';
    var gridId = "tablaDevolucionesAlquilerGrid";
    var gridObj = $("#" + gridId);
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = $('#buscarDevolucionesAlquilerForm');

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarDevolucionesAlquiler"
    };

    var tabla = function(){
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames:['','No. Retorno','Fecha de retorno','No. Contrato','Cliente','Centro de facturaci&oacute;n','Estado','',''],
            colModel:[
                {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
                {name:'codigo', index:'codigo', width:55, sortable:true},
                {name:'fecha_retorno', index:'fecha_retorno', width:50, sortable:true},
                {name:'no_contrato', index:'no_contrato', width:50,  sortable:false, },
                {name:'cliente_id', index:'cliente_id', width:50,  sortable:false, },
                {name:'centro_facturacion_id', index:'centro_facturacion_id', width:50,  sortable:false, },
                {name:'estado_id', index:'estado_id', width:40,  sortable:false, align:'center', classes:'estado'},
                {name:'options', index:'options',width: 40, align:'center'},
                {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
            ],
            postData: {
                erptkn: tkn,
                contrato_alquiler_id:(typeof contrato_alquiler_id === 'undefined') ? '' : contrato_alquiler_id,
                entrega_alquiler_id:(typeof window.sp_entrega_alquiler_id === 'undefined') ? '' : window.sp_entrega_alquiler_id,
                item_id: typeof window.sp_item_id !== 'undefined' ? window.sp_item_id : ''
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
                    $('#'+gridId+'NoRecords').empty().append('No se encontraron Retornos.').css({"color":"#868686","padding":"30px 0 0"}).show();
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

                    $('#jqgh_' + gridId + "_cb").css("text-align", "center");
                }

                $('#tablaDevolucionesAlquilerGrid').find('td').css('vertical-align','middle');

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
                var url = phost() + "devoluciones_alquiler/ajax-exportar";
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
            var fecha_desde= $('#fecha_desde').val();
            var fecha_hasta= $('#fecha_hasta').val();
            var no_contrato= $('#no_contrato').val();
            var cliente_id = $('#cliente_id').val();
            var centro_facturacion_id = $('#centro_facturacion_id').val();
            var estado_id = $('#estado_id').val();

            if (codigo !== "" || fecha_desde !== "" || fecha_hasta !== "" || no_contrato !== "" || cliente_id !== "" || centro_facturacion_id !=="" || estado_id !== "") {
                //Reload Grid
                gridObj.setGridParam({
                    url: tablaUrl,
                    datatype: "json",
                    postData: {
                        codigo: codigo,
                        fecha_desde: fecha_desde,
                        fecha_hasta: fecha_hasta,
                        no_contrato: no_contrato,
                        cliente_id: cliente_id,
                        centro_facturacion_id: centro_facturacion_id,
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
                fecha_desde: '',
                fecha_hasta: '',
                no_contrato: '',
                cliente_id: '',
                centro_facturacion_id: '',
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
    tablaDevolucionesAlquiler.init();
});
