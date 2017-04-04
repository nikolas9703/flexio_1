var tablaConciliaciones = (function(){

    var tablaUrl = phost() + 'conciliaciones/ajax-listar';
    var gridId = "tablaConciliacionesGrid";
    var gridObj = $("#tablaConciliacionesGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = $('#buscarConciliacionesForm');

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn"
    };

    var tabla = function(){
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames:['','No. de Conciliaci&oacute;n','Cuenta','Balance en banco','Balance en Flexio','Diferencia', 'Rango de fecha','', ''],
            colModel:[
                {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
                {name:'codigo', index:'codigo', width:55, sortable:true},
                {name:'cuenta_id', index:'cuenta_id', width:50, sortable:true},
                {name:'balance_banco', index:'balance_banco', width:50,  sortable:false, },
                {name:'balance_flexio', index:'balance_flexio', width:50,  sortable:false, },
                {name:'diferencia', index:'diferencia', width:50,  sortable:false, },
                {name:'rango_fecha', index:'rango_fecha', width:50,  sortable:false, },
                {name:'options', index:'options',width: 40},
                {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
            ],
            postData: {
                erptkn: tkn
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20,50,100],
            rowNum: 10,
            page: 1,
            pager: gridId+"Pager",
            loadtext: '<p>Cargando...',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: true,
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
                    $('#'+gridId+'NoRecords').empty().append('No se encontraron Conciliaciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    $('#gbox_'+gridId).show();
                    $('#'+gridId+'NoRecords').empty();
                }

            //---------
            // Cargar plugin jquery Sticky Objects
            //----------
            //add class to headers
            gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
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

            var cuenta_id = $('#cuenta_id').val();
            var fecha_inicio = $('#fecha_inicio').val();
            var fecha_fin = $('#fecha_fin').val();

            if (cuenta_id !== "" || fecha_inicio !== "" || fecha_fin !== "") {
                //Reload Grid
                gridObj.setGridParam({
                    url: tablaUrl,
                    datatype: "json",
                    postData: {
                        cuenta_id: cuenta_id,
                        fecha_inicio: fecha_inicio,
                        fecha_fin: fecha_fin,
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
                cuenta_id: '',
                fecha_inicio: '',
                fecha_fin: '',
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
    tablaConciliaciones.init();
});
