
var multiselect = window.location.pathname.match(/facturas_compras/g) ? true : false;
var operacion_type = window.location.pathname.match(/subcontratos/g) ? '' : 18;

var tablaFacturasCompras = (function(){

    var tablaUrl = phost() + 'facturas_compras/ajax-listar';
    var gridId = "tablaFacturasComprasGrid";
    var gridObj = $("#tablaFacturasComprasGrid");
    var opcionesModal = $('#optionsModal, #opcionesModal');
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
            colNames:['No. Factura','Fecha','Proveedor','Monto','Saldo por pagar','Centro contable','Creado por','Estado','', ''],
            colModel:[
		{name:'No. Factura', index:'factura', width:50, sortable:true},
		{name:'Fecha', index:'created_at', width:50, sortable:true},
		{name:'Proveedor', index:'proveedor_id', width:70,  sortable:false, },
	  {name:'Monto', index:'monto', width: 50,  sortable:false},
   	{name:'Saldo', index:'saldo', width: 50,  sortable:false},
		{name:'Centro', index:'centro_id', width: 70,  sortable:false},
    {name:'Creado por', index:'creado_por', width: 70,  sortable:false},
    {name:'Estado', index:'estado_id', width: 55,  sortable:false},
    {name:'options', index:'options',width: 40},
		{name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true}
            ],
            postData: {
                erptkn: tkn,
                tipo: operacion_type,
                caja_id: scaja_id,
                item_id: (typeof item_id !== 'undefined') ? _.toString(item_id) : '',
                pedidos_id: pedidosid,
                pedido_id: (typeof window.sp_pedido_id !== 'undefined') ? window.sp_pedido_id : '',//from subpanels ver pedido
                proveedor: (typeof proveedor_id !== 'undefined') ? _.toString(proveedor_id) : '',
                creado_por: '',
                categoria_id: '',
                orden_compra_id: (typeof orden_compra_id !== 'undefined') ? _.toString(orden_compra_id) : '',
                subcontrato_id: (typeof subcontrato_id !== 'undefined') ? _.toString(subcontrato_id) : ''
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
	gridObj.on("click", botones.opciones, function(e)
        {
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var id = $(this).attr("data-id");

            var rowINFO = gridObj.getRowData(id);
            var options = rowINFO.link;

            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: '+ $(rowINFO["No. Factura"]).text() +'');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

    };
    $(botones.limpiar).click(function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        $('#buscarFacturasComprasForm').find('input[type="text"]').prop("value", "");
        $('#buscarFacturasComprasForm').find('select.chosen-select').prop("value", "");
        $('#buscarFacturasComprasForm').find('select').prop("value", "");
        $('#buscarFacturasComprasForm').find('select[id="categoria_id"]').find('option').removeAttr("selected");
        $("#buscarFacturasComprasForm").find('#categoria_id').chosen({width: '100%'}).trigger('chosen:updated');
        $(".chosen-select").trigger("chosen:updated");

        recargar();
    });
    $(botones.buscar).click(function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        var numero_factura = $('#numero_factura').val();
        var fecha1 = $('#fecha1').val();
        var fecha2 = $('#fecha2').val();
        var proveedor = $('#proveedor').val();
        var estado = $('#estado').val();
        var monto1 = $('#monto1').val();
        var monto2 = $('#monto2').val();
        var centro_contable = $('#centro_contable').val();
        //var tipo = $('#tipo').val();
        var creado_por = $('#creado_por').val();
        var categoria_id = $('#categoria_id').val();

        if (numero_factura !== "" || fecha1 !== "" || fecha2 !== "" || proveedor !== "" || estado !== "" || monto1 !== "" || monto2 !== "" || centro_contable !== "" || creado_por !== "" || categoria_id !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    numero_factura: numero_factura,
                    fecha1: fecha1,
                    fecha2: fecha2,
                    proveedor: proveedor,
                    estado: estado,
                    monto1: monto1,
                    monto2: monto2,
                    centro_contable: centro_contable,
                    categoria_id: categoria_id,
                    creado_por: creado_por,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    });

    //Documentos Modal
		$("#optionsModal").on("click", ".subirDocumento", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Cerrar modal de opciones
			$("#optionsModal").modal('hide');
			var factura_compra_id = $(this).attr("data-id");

			//Inicializar opciones del Modal
			$('#documentosModal').modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});

                        //$('#pedido_id').val(pedido_id);
                        var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		    scope.safeApply(function(){
		    	scope.campos.factura_id = factura_compra_id;
		    });



			$('#documentosModal').modal('show');
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
        /*$('#buscarFacturasComprasForm').find('select[name="tipo"]').prop("value", "18");
        $("#buscarFacturasComprasForm").find('#tipo').chosen({width: '100%'}).trigger('chosen:updated');*/

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                numero_factura: '',
                fecha1: '',
                fecha2: '',
                proveedor: '',
                estado: '',
                monto1: '',
                monto2: '',
                centro_contable: '',
                categoria_id:'',
                creado_por:'',
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
    var campos = function(){
            $('#buscarFacturasComprasForm').find('select[name="tipo"]').prop("value", "18");
            $("#buscarFacturasComprasForm").find('#categoria_id').chosen({width: '100%'}).trigger('chosen:updated');

    };
    return{
        init:function(){
            campos();
            tabla();
            eventos();
            redimencionar_tabla();
        }
    };

})();

tablaFacturasCompras.init();
