var tablaSubContratosVentas = (function(){
    if(typeof proveedor_id === 'undefined'){
        proveedor_id="";
    }
    var tablaUrl = phost() + 'subcontratos/ajax-listar';
    var gridId = "tablaSubContratosGrid";
    var gridObj = $("#tablaSubContratosGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = $('#buscarSubContratosForm');

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
            colNames:['','N. de subcontrato','Proveedor','Monto original','Adendas','Monto actual', 'Facturado','Por facturar','Centro', 'Estado','', ''],
            colModel:[
                {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
                {name:'codigo', index:'codigo', width:55, sortable:true},
                {name:'proveedor_id', index:'proveedor_id', width:50, sortable:true},
                {name:'monto_original', index:'monto_original', width:50,  sortable:false, },
                {name:'adendas', index:'adendas', width:50,  sortable:false, },
                {name:'monto_actual', index:'monto_actual', width: 50,  sortable:false},
                {name:'facturado', index:'facturado', width: 50,  sortable:false},
                {name:'por_facturar', index:'por_facturar', width:50,  sortable:false, },
                {name:'centro', index:'centro', width: 30,  sortable:false},
                {name:'estado', index:'estado', width: 30,  sortable:false},
                {name:'options', index:'options',width: 40},
                {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
            ],
            postData: {
                erptkn: tkn,
                proveedor_id: proveedor_id
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
                    $('#'+gridId+'NoRecords').empty().append('No se encontraron Subcontratos.').css({"color":"#868686","padding":"30px 0 0"}).show();
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

            var proveedor = $('#proveedor').val();
            var monto1 = $('#monto1').val();
            var monto2 = $('#monto2').val();
            var numero_subcontrato = $('#numero_subcontrato').val();
            var centro = $('#centro').val();
            var estado = $('#estado').val();

            if (proveedor !== "" || monto1 !== "" || monto2 !== '' || numero_subcontrato !== "" || centro !== "" || estado !== '') {
                //Reload Grid
                gridObj.setGridParam({
                    url: tablaUrl,
                    datatype: "json",
                    postData: {
                        proveedor: proveedor,
                        monto1: monto1,
                        monto2: monto2,
                        numero_subcontrato: numero_subcontrato,
                        centro: centro,
                        estado: estado,
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
                proveedor: '',
                monto1: '',
                monto2: '',
                numero_subcontrato: '',
                centro: '',
                estado: '',
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
    tablaSubContratosVentas.init();
});
