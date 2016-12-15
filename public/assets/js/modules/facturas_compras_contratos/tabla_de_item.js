var tablaFacturasCompras = (function(){

    var tablaUrl = phost() + 'facturas_compras_contratos/ajax-listar-de-item';
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
    	
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames:['Fecha','Factura','Proveedor','Estado','Cantidad','Precio','Total'],
            colModel:[
		{name:'Fecha', index:'created_at', width:50, sortable:false},
                {name:'Factura', index:'codigo', width:50, sortable:false},
		{name:'Proveedor', index:'proveedor_id', width:70,  sortable:false, },
		{name:'Estado', index:'estado_id', width: 55,  sortable:false},
                {name:'Cantidad', index:'cantidad', width: 50,  sortable:false},
		{name:'Precio', index:'precio', width: 50,  sortable:false},
		{name:'Total', index:'total', width: 50,  sortable:false}
            ],
            postData: {
                erptkn: tkn,
                caja_id: scaja_id,
                item_id: (typeof item_id !== 'undefined') ? item_id : ''
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

                //Arreglar tamaño de TD de los checkboxes
                //FALTA ADAPTAR EL CODIGO PARA QUE LOS CHECKBOX SE VEAN BIEN
                $('#jqgh_'+gridId+ "_cb").css("text-align","center");
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
        var tipo = $('#tipo').val();

        if (fecha1 !== "" || fecha2 !== "" || proveedor !== "" || estado !== "" || monto1 !== "" || monto2 !== "" || centro_contable !== "" || tipo !== "") {
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
