
var multiselect = window.location.pathname.match(/facturas_compras/g) ? true : false;

var tablaFacturasCompras = (function(){

    var tablaUrl = phost() + 'facturas_compras_contratos/ajax-listar';
    var gridId = "tablaFacturasComprasGrid";
    var gridObj = $("#tablaFacturasComprasGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = '';

    var botones = {
	opciones: ".viewOptions",
	buscar: "#searchBtn",
	limpiar: "#clearBtn",
    refacturar:"#refacturar"
    };

    var tabla = function(){

    	var scaja_id = '';
        if(typeof caja_id != 'undefined'){
            scaja_id = caja_id;
        }
       var pedidosid='';
       if(typeof pedidos_id != 'undefined'){
            pedidosid = pedidos_id;
        }

        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames:['No. Factura','Fecha','Proveedor','No. contrato','Monto','Saldo','Centro contable','Estado','', ''],
            colModel:[
		{name:'Numero', index:'codigo', width:50, sortable:true},
		{name:'Fecha', index:'created_at', width:50, sortable:true},
		{name:'Proveedor', index:'proveedor_id', width:70,  sortable:false, },
		{name:'No.contrato', index:'no_contrato', width:50,  sortable:false, },
                {name:'Monto', index:'monto', width: 50,  sortable:false},
		{name:'Saldo', index:'saldo', width: 50,  sortable:false},
		{name:'Centro', index:'centro_id', width: 70,  sortable:false},
		{name:'Estado', index:'estado_id', width: 55,  sortable:false, align:'center'},
                {name:'options', index:'options',width: 40},
		{name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true}
            ],
            postData: {
                erptkn: tkn,
                caja_id: scaja_id,
                item_id: (typeof item_id !== 'undefined') ? item_id : '',
                pedidos_id: pedidosid,
                proveedor: (typeof proveedor_id !== 'undefined') ? proveedor_id : '',
                orden_compra_id: (typeof orden_compra_id !== 'undefined') ? orden_compra_id : ''
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
                $(this).closest("div.ui-jqgrid-view").find("#tablaClientesGrid_cb, #jqgh_tablaClientesGrid_link").css("text-align", "center");
	    },
            loadComplete: function(data, status, xhr){

                if(gridObj.getGridParam('records') === 0 ){
                    $('#gbox_'+gridId).hide();
                    $('#'+gridId+'NoRecords').empty().append('No se encontraron Facturas.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    $('#gbox_'+gridId).show();
                    $('#'+gridId+'NoRecords').empty();
                }

                if(multiselect == true)
                {
                    gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
                    $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view',
                        className:'jqgridHeader'
                    });
                    $('#jqgh_'+gridId+ "_cb").css("text-align","center");
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
            opcionesModal.find('.modal-title').empty().append('Opciones: '+ $(rowINFO.Numero).text() +'');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });
    };
    //Documentos Modal en  DETALLE
    $('#optionsModal').on("click", ".subirArchivoBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        console.log("Documento");
        //Cerrar modal de opciones
        $('#optionsModal').modal('hide');

        var factura_id = $(this).attr("data-id");
        console.log(factura_id);
        //Inicializar opciones del Modal
        $('#documentosModal').modal({
            backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
            show: false
        });

        var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
        scope.safeApply(function(){
            scope.campos.factura_id = factura_id;
        });
        $('#documentosModal').modal('show');
        $('.guardarDocBoton').on("click", function(e){
            console.log("entrara");
            setTimeout(function(){
                $('#tablaDocumentosGrid').trigger( 'reloadGrid' );
            }, 500);
        });
    });
    $(botones.limpiar).click(function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        $('#buscarFacturasComprasForm').find('input[type="text"]').prop("value", "");
        $('#buscarFacturasComprasForm').find('select.chosen-select').prop("value", "");
        $('#buscarFacturasComprasForm').find('select').prop("value", "");
        $(".chosen-select").trigger("chosen:updated");

        recargar();
    });
    $(botones.buscar).click(function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        var fecha1 = $('#fecha1').val();
        var fecha2 = $('#fecha2').val();
        var proveedor = $('#proveedor').val();
        var estado = $('#estado').val();
        var monto1 = $('#monto1').val();
        var monto2 = $('#monto2').val();
        var centro_contable = $('#centro_contable').val();
        var numero_factura = $('#numero_factura').val();
        //var tipo = '19';
        var tipo = $('#tipo').val();
        console.log(tipo);

        if (fecha1 !== "" || fecha2 !== "" || proveedor !== "" || estado !== "" || monto1 !== "" || monto2 !== "" || centro_contable !== "" || tipo !== "" || numero_factura !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    fecha1: fecha1,
                    fecha2: fecha2,
                    proveedor: proveedor,
                    estado: estado,
                    monto1: monto1,
                    monto2: monto2,
                    centro_contable: centro_contable,
                    tipo: tipo,
                    numero_factura: numero_factura,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    });
    $(botones.refacturar).click(function(){
      var ids = gridObj.jqGrid('getGridParam', 'selarrrow');
      console.log(ids);

      if(!_.isEmpty(ids)){
        $('#items_facturados').val(ids);
        $('#refacturaForm').submit();
      }else{
        swal("Seleccione las facturas para refacturar");
        return false;
      }
    });
    var recargar = function(){

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                fecha1: '',
                fecha2: '',
                proveedor: '',
                estado: '',
                monto1: '',
                monto2: '',
                centro_contable: '',
                tipo: '',
                numero_factura:'',
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

tablaFacturasCompras.init();
