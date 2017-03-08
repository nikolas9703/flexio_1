//modulo clientes

var tablaOrdenesVentas = (function(){

		if(typeof cliente_id === 'undefined'){
				 cliente_id="";
		}
		/* if(typeof uuid_venta === 'undefined'){
			 uuid_venta="";
		}*/

	var tablaUrl = phost() + 'ordenes_alquiler/ajax-listar';
	var gridId = "tablaOrdenesVentasGrid";
	var gridObj = $("#tablaOrdenesVentasGrid");
	var opcionesModal = $('#optionsModal');
	var formularioBuscar = '';

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
		 colNames:['','No.O/V','Cliente','Fecha de emisión','No. Contrato','Vendedor', 'Estado','', ''],
		 colModel:[
		 {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
		 {name:'codigo', index:'codigo', width:30, sortable:true},
		 {name:'cliente', index:'cliente', width:70, sortable:true},
		 {name:'fecha_desde', index:'fecha_desde', width:50,  sortable:false, },
		 {name:'fecha_hasta', index:'fecha_hasta', width:70,  sortable:false, },
                 {name:'vendedor', index:'vendedor', width: 30,  sortable:false},
                 {name:'estado', index:'estado', width: 30,  sortable:false},
		 {name:'options', index:'options',width: 40},
		 {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
		 ],
 	   postData: {
                erptkn: tkn,
                cotizacion_id: (typeof window.sp_cotizacion_id !== 'undefined') ? window.sp_cotizacion_id : '',
                campo: typeof window.campo !== 'undefined' ? window.campo : {}
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
          $('#'+gridId+'NoRecords').empty().append('No se encontraron Ordenes de Venta.').css({"color":"#868686","padding":"30px 0 0"}).show();
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

	};

        //Documentos Modal
    $("#optionsModal").on("click", ".subirArchivoBtn", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            //Cerrar modal de opciones
            $("#optionsModal").modal('hide');
            var ordenes_ventas_id = $(this).attr("data-id");

            //Inicializar opciones del Modal
            $('#documentosModal').modal({
                    backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                    show: false
            });

            //$('#pedido_id').val(pedido_id);
            var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();

        scope.safeApply(function(){
            scope.campos.ordenes_ventas_id = ordenes_ventas_id;
        });
            $('#documentosModal').modal('show');
    });

	$(botones.limpiar).click(function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		$('#buscarCotizacionesForm').find('input[type="text"]').prop("value", "");
		$('#buscarCotizacionesForm').find('select.chosen-select').prop("value", "");
		$('#buscarCotizacionesForm').find('select').prop("value", "");
		$(".chosen-select").trigger("chosen:updated");

		recargar();
	});
	$(botones.buscar).click(function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var no_orden = $('#no_orden').val();
		var no_contrato = $('#no_contrato').val();
		var no_orden = $('#no_orden').val();
		var cliente = $('#cliente').val();
		var desde = $('#fecha1').val();
		var hasta = $('#fecha2').val();
		var etapa = $('#etapa').val();
		var vendedor = $('#vendedor').val();

		if (no_orden !== "" || no_contrato !== "" || cliente !== "" || desde !== "" || hasta !== "" || etapa !== "" || vendedor !== "") {
			//Reload Grid
			gridObj.setGridParam({
				url: tablaUrl,
				datatype: "json",
				postData: {
					no_orden: no_orden,
					no_contrato: no_contrato,
					cliente: cliente,
					desde: desde,
					hasta: hasta,
					etapa: etapa,
					vendedor: vendedor,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}


	});
	var recargar = function(){

		//Reload Grid
		gridObj.setGridParam({
			url: tablaUrl,
			datatype: "json",
			postData: {
				no_orden: '',
				no_contrato: '',
				cliente: '',
				desde: '',
				hasta: '',
				etapa: '',
				vendedor: '',
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
	//Al redimensionar ventana
	// $(window).resizeEnd(function() {
	// 	tablaColaboradores.redimensionar();
	// });
tablaOrdenesVentas.init();
});
