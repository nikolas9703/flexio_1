var tablaAdendas = (function(){
  if(typeof contrato_id === 'undefined'){
			contrato_id="";
	}
  var tablaUrl = phost() + 'contratos/ajax-listar-adendas';
	var gridId = "tablaAdendasGrid";
	var gridObj = $("#tablaAdendasGrid");
	var opcionesModal = $('#opcionesModal');

	var botones = {
		opciones: ".viewOptions"
	};

  var tabla = function(){
 	 gridObj.jqGrid({
 		 url: tablaUrl,
 		 mtype: "POST",
 		 datatype: "json",
 		 colNames:['','Numero de adenda','Fecha','Monto (sin ITBMS)','Monto modificado del contrato','', ''],
 		 colModel:[
 		 {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
 		 {name:'codigo', index:'codigo', width:30, sortable:true},
 		 {name:'fecha', index:'fecha', width:50, sortable:true},
 		 {name:'monto_adenda', index:'monto_adenda', width:50,  sortable:false, },
 		 {name:'monto_total', index:'monto_total', width:50,  sortable:false, },
 		 {name:'options', index:'options',width: 40, align:'center'},
 		 {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
 		 ],
  	   postData: {
  	   		erptkn: tkn,
 				contrato_id: contrato_id
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
 	      $(this).closest("div.ui-jqgrid-view").find("#tablaAdendasGrid_cb, #jqgh_tablaAdendasGrid_link").css("text-align", "center");
 	    },
 			loadComplete: function(data, status, xhr){

         if(gridObj.getGridParam('records') === 0 ){
           $('#gbox_'+gridId).hide();
           $('#'+gridId+'NoRecords').empty().append('No se encontraron Adendas.').css({"color":"#868686","padding":"30px 0 0"}).show();
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
  var recargar = function(){
		//Reload Grid
		gridObj.setGridParam({
			url: tablaUrl,
			datatype: "json",
			postData: {
				cliente: '',
				monto_original: '',
				numero_contrato: '',
				centro: '',
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
   tablaAdendas.init();
});
