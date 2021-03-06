//modulo clientes
var tablaCobros = (function(){
	if(typeof uuid_cotizacion === 'undefined'){
			uuid_cotizacion="";
	}
	var tablaUrl = phost() + 'cobros/ajax-listar';
	var gridId = "tablaCobrosGrid";
	var gridObj = $("#tablaCobrosGrid");
	var opcionesModal = $('#optionsModal');
	var formularioBuscar = '';

	var botones = {
		opciones: ".viewOptions",
		buscar: "#searchBtn",
		limpiar: "#clearBtn"
	};

 var tabla = function(){

     if(typeof uuid_factura === 'undefined'){
			uuid_factura="";
                        cliente_id="";
	}
      var factura_id = uuid_factura !== 'null' ? uuid_factura : '';
      var id_cliente = cliente_id !== 'null' ? cliente_id : '';
      var cajaId = "";
			var spordenalquilerid = "";
      if(typeof caja_id != "undefined"){
				cajaId = $.parseJSON(caja_id);
			}
			if(typeof sp_orden_alquiler_id != "undefined"){
				spordenalquilerid = sp_orden_alquiler_id;
			}


	 gridObj.jqGrid({
		 url: tablaUrl,
		 mtype: "POST",
		 datatype: "json",
		 colNames:['','No. Cobro','Cliente','Fecha de cobro','Monto cobrado','Método de cobro','Estado','', ''],
		 colModel:[
		 {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
		 {name:'codigo', index:'codigo', width:30, sortable:true},
		 {name:'cliente', index:'cliente', width:70, sortable:true},
		 {name:'fecha_pago', index:'fecha_pago', width:30,  sortable:false, },
		 {name:'monto_pagado', index:'monto_pagado', width: 30,  sortable:false},
		 {name:'metodo_pago', index:'metodo_pago', width: 50,  sortable:false},
		 {name:'estado', index:'estado', width: 30,  sortable:false},
		 {name:'options', index:'options',width: 40},
		 {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
		 ],
 	   postData: {
 	   		erptkn: tkn,
				orden_alquiler_id: spordenalquilerid,
                        factura_id: factura_id,
                        cliente_id: id_cliente,
                        caja_id: cajaId
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

				//check if isset data
				if( data['total'] === 0 ){
					$('#gbox_'+ gridId).hide();
					$('#'+gridId+'NoRecords').empty().append('No se encontraron Cobros.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ gridId +'NoRecords').hide();
					$('#gbox_'+ gridId).show();
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
	$(botones.limpiar).click(function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		$('#buscarCobrosForm').find('input[type="text"]').prop("value", "");
		$('#buscarCobrosForm').find('select.chosen-select').prop("value", "");
		$('#buscarCobrosForm').find('select').prop("value", "");
		$(".chosen-select").trigger("chosen:updated");

		recargar();
	});
	$(botones.buscar).click(function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var cliente = $('#cliente').val();
		var desde = $('#fecha_min').val();
		var hasta = $('#fecha_max').val();
		var estado = $('#estado').val();
		var codigo = $('#codigo').val();

		if (cliente !== "" || desde !== "" || hasta !== "" || estado !== "" || codigo !== "") {
			//Reload Grid

			gridObj.setGridParam({
				url: tablaUrl,
				datatype: "json",
				postData: {
					campo:{
						cliente:cliente,
						fecha_min:desde,
						fecha_max:hasta,
						estado:estado,
						codigo:codigo
					},
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
				campo:{
					cliente:'',
					fecha_min:'',
					fecha_max:'',
					estado:'',
					codigo:''
				},
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
   tablaCobros.init();
});
