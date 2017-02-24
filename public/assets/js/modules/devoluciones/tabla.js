var tablaDevoluciones = (function(){

		if(typeof cliente_id === 'undefined'){
				cliente_id="";
		}
	var tablaUrl = phost() + 'devoluciones/ajax-listar';
	var gridId = "tablaDevolucionesGrid";
	var gridObj = $("#tablaDevolucionesGrid");
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
		 colNames:['','No.Devoluci&oacute;n','Cliente','Fecha de emisión','Raz&oacute;n','Vendedor','Estado','', ''],
		 colModel:[
		 {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
		 {name:'codigo', index:'codigo', width:30, sortable:true},
		 {name:'cliente_id', index:'cliente_id', width:70, sortable:true},
		 {name:'fecha_desde', index:'fecha_desde', width:50,  sortable:false, },
		 {name:'razon', index:'razon', width:70,  sortable:false, },
		 {name:'created_by', index:'created_by', width: 30,  sortable:false},
		 {name:'estado', index:'estado', width: 30,  sortable:false},
		 {name:'options', index:'options',width: 40},
		 {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
		 ],
 	   postData: {
 	   		erptkn: tkn,
				cliente_id: cliente_id
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
	 	  sortname: 'id',
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
          $('#'+gridId+'NoRecords').empty().append('No se encontraron Devoluciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
	$(botones.limpiar).click(function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		$('#buscarDevolucionesForm').find('input[type="text"]').prop("value", "");
		$('#buscarDevolucionesForm').find('select.chosen-select').prop("value", "");
		$('#buscarDevolucionesForm').find('select').prop("value", "");
		$(".chosen-select").trigger("chosen:updated");

		recargar();
	});
	$(botones.buscar).click(function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var cliente = $('#cliente').val();
		var desde = $('#fecha_desde').val();
		var hasta = $('#fecha_hasta').val();
		var etapa = $('#etapa').val();
		var vendedor = $('#vendedor').val();

		if (cliente !== "" || desde !== "" || hasta !== "" || etapa !== "" || vendedor !== "") {
			//Reload Grid
			gridObj.setGridParam({
				url: tablaUrl,
				datatype: "json",
				postData: {
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

tablaDevoluciones.init();
});
