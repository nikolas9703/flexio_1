//modulo clientes
var tablaPagos = (function(){
	if(typeof uuid_cotizacion === 'undefined'){
			uuid_cotizacion="";
	}
	var tablaUrl = phost() + 'pagos/ajax-listar';
	var gridId = "tablaPagosGrid";
	var gridObj = $("#tablaPagosGrid");
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
		 colNames:['','N&uacute;mero de pago','Fecha','Proveedor','Tipo', 'Forma de Pago','Banco','Estado','Monto','Opciones', ''],
		 colModel:[
		 {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
		 {name:'codigo', index:'codigo', width:55, sortable:true},
		 {name:'fecha', index:'fecha', width:50, sortable:false},
		 {name:'Proveedor', index:'proveedor', width:70,  sortable:false, },
		 {name:'tipo', index:'tipo', width: 40,  sortable:false},
		 {name:'forma_pago', index:'forma_pago', width: 40,  sortable:false},
		 {name:'banco', index:'banco', width: 70,  sortable:false},
		 {name:'estado', index:'estado', width: 45,  sortable:false},
                 {name:'monto', index:'monto', width: 60,  sortable:false},
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
	      $(this).closest("div.ui-jqgrid-view").find("#tablaClientesGrid_cb, #jqgh_tablaClientesGrid_link").css("text-align", "center");
	    },
			loadComplete: function(data, status, xhr){

        if(gridObj.getGridParam('records') === 0 ){
          $('#gbox_'+gridId).hide();
          $('#'+gridId+'NoRecords').empty().append('No se encontraron Pagos.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
		$('#buscarPagosForm').find('input[type="text"]').prop("value", "");
		$('#buscarPagosForm').find('select.chosen-select').prop("value", "");
		$('#buscarPagosForm').find('select').prop("value", "");
		$(".chosen-select").trigger("chosen:updated");

		recargar();
	});
	$(botones.buscar).click(function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var desde = $('#fecha1').val();
		var hasta = $('#fecha2').val();
                var proveedor = $("#proveedor").val();
                var estado = $("#estado").val();
                var montoMin = $("#monto_min").val();
                var montoMax = $("#monto_max").val();
                var formaPago = $("#forma_pago").val();
                var tipo = $("#tipo").val();
                var banco = $("#banco").val();

		if (desde !== "" || hasta !== "" || proveedor !== "" || estado !== "" || montoMin !== "" || montoMax !== "" || formaPago !== "" || tipo !== "" || banco !== "") {
			//Reload Grid
			gridObj.setGridParam({
				url: tablaUrl,
				datatype: "json",
				postData: {
					desde:desde,
					hasta:hasta,
                                        proveedor:proveedor,
					estado:estado,
					montoMin:montoMin,
                                        montoMax:montoMax,
                                        formaPago:formaPago,
                                        tipo:tipo,
                                        banco:banco,
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
				desde:'',
                                hasta:'',
                                proveedor:'',
                                estado:'',
                                montoMin:'',
                                montoMax:'',
                                formaPago:'',
                                tipo:'',
                                banco:'',
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
   tablaPagos.init();
});
