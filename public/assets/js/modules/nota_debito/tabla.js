
var multiselect = window.location.pathname.match(/notas_debitos/g) ? true : false;

var tablaNotaDebito = (function(){

		if(typeof cliente_id === 'undefined'){
				 cliente_id="";
		}

	var tablaUrl = phost() + 'notas_debitos/ajax-listar';
	var gridId = "tablaNotaDebitoGrid";
	var gridObj = $("#tablaNotaDebitoGrid");
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
		 colNames:['','N&uacute;mero interno de nota','No. de nota del proveedor','Proveedor','Fecha de emisión','Monto de d&eacute;bito','Creado por','Estado','', ''],
		 colModel:[
		 {name:'uuid', index:'uuid', width:70,  hidedlg:true, hidden: true},
		 {name:'codigo', index:'codigo', width:70, sortable:true},
		 {name:'no_nota_credito', index:'no_nota_credito', width:70, sortable:true},
		 {name:'cliente_id', index:'cliente_id', width:70, sortable:true, hidden: !multiselect ? true : false, hidedlg: !multiselect ? true : false},
		 {name:'fecha', index:'fecha', width:70,  sortable:false, },
		 {name:'monto', index:'monto', width:70,  sortable:false, align:"right"},
		 {name:'usuario', index:'usuario', width: 70,  sortable:false},
     {name:'estado', index:'estado', width: 70,  sortable:false},
		 {name:'options', index:'options',width: 40},
		 {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
		 ],
 	   postData: {
 	   		erptkn: tkn,
			cliente_id: cliente_id,
			proveedor_uuid: typeof window.sp_proveedor_uuid !== 'undefined' ? window.sp_proveedor_uuid : ''
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
	 	  multiselect: window.multiselect,
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
          $('#'+gridId+'NoRecords').empty().append('No se encontraron Notas de D&eacute;bitos.').css({"color":"#868686","padding":"30px 0 0"}).show();
        }
        else{
          $('#gbox_'+gridId).show();
          $('#'+gridId+'NoRecords').empty();
        }

		if(window.multiselect)
		{
			gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
	        $('#gridHeader').sticky({
	        	getWidthFrom: '.ui-jqgrid-view',
	            className:'jqgridHeader'
	        });
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
				opcionesModal.find('.modal-title').empty().append('Opciones: '+ $(rowINFO.codigo).text() +'');
				opcionesModal.find('.modal-body').empty().append(options);
				opcionesModal.find('.modal-footer').empty();
				opcionesModal.modal('show');
		});


	};
		//Documentos Modal
	$("#optionsModal").on("click", ".subirArchivoBtn", function (e) {
		e.preventDefault();
		e.returnValue = false;
		e.stopPropagation();

		//Cerrar modal de opciones
		$("#optionsModal").modal('hide');
		var nota_debito_id = $(this).attr("data-id");

		//Inicializar opciones del Modal
		$('#documentosModal').modal({
			backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
			show: false
		});


		var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();

		scope.safeApply(function () {
			scope.campos.nota_debito_id = nota_debito_id;
		});
		$('#documentosModal').modal('show');
	});

	$(botones.limpiar).click(function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		$('#buscarNotaDebitoForm').find('input[type="text"]').prop("value", "");
		$('#buscarNotaDebitoForm').find('select.chosen-select').prop("value", "");
		$('#buscarNotaDebitoForm').find('select').prop("value", "");
		$(".chosen-select").trigger("chosen:updated");
		recargar();
	});
	$(botones.buscar).click(function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var proveedor_id = $('#proveedor_id').val();
		var desde = $('#fecha1').val();
		var hasta = $('#fecha2').val();
		var etapa = $('#etapa').val();
		var vendedor = $('#vendedor').val();
		var codigo = $('#codigo').val();
		var no_nota_credito = $('#no_nota_credito').val();
		var monto1 = $('#monto1').val();
		var monto2 = $('#monto2').val();

		if (proveedor_id !== "" || desde !== "" || hasta !== "" || etapa !== "" || vendedor !== "" || codigo !== "" || no_nota_credito !== ""|| monto1 !== ""|| monto2 !== "") {
			//Reload Grid
			gridObj.setGridParam({postData:null});
			gridObj.setGridParam({
				url: tablaUrl,
				datatype: "json",
				postData: {
					proveedor_id: proveedor_id,
					desde: desde,
					hasta: hasta,
					etapa: etapa,
					vendedor: vendedor,
					codigo: codigo,
					no_nota_credito: no_nota_credito,
					monto1:monto1,
					monto2:monto2,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}


	});
	var recargar = function(){

		//Reload Grid
		gridObj.setGridParam({postData:null});
		gridObj.setGridParam({
			url: tablaUrl,
			datatype: "json",
			postData: {
				proveedor_id: '',
				desde: '',
				hasta: '',
				etapa: '',
				codigo:'',
				monto1:'',
				monto2:'',
				vendedor: '',
				no_nota_credito:'',
				erptkn: tkn
			}
		}).trigger('reloadGrid');

		$('#buscarNotaDebitoForm').find('selected').find('option:eq(0)').prop("selected", "selected")
		$("select").trigger("chosen:updated").trigger("change");

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
tablaNotaDebito.init();
});
