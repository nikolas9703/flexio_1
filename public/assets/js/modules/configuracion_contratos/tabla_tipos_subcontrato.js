var tablaCatalogoTipoSubcontrato = (function(){
    if(typeof subcontrato_id === 'undefined'){
        subcontrato_id="";
    }
    var tablaUrl = phost() + 'configuracion_contratos/ajax-listar-catalogo';
    var gridId = "tablaTipoSubcontratoGrid";
    var gridObj = $("#tablaTipoSubcontratoGrid");
    var opcionesModal = $('#opcionesModal');

    var botones = {
        opciones: ".viewOptions"
    };

  var tabla = function(){
 	 gridObj.jqGrid({
 		 url: tablaUrl,
 		 mtype: "POST",
 		 datatype: "json",
 		 colNames:['Tipo de subcontrato','Confidencial','Estado','','','',''],
 		 colModel:[
   		 {name:'nombre', index:'nombre', width:30, sortable:true},
   		 {name:'con_acceso', index:'con_acceso', width:50,  sortable:false, },
   		 {name:'estado', index:'estado', width:50,  sortable:false, },
   		 {name:'options', index:'options',width: 40, align:'center'},
   		 {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
       {name:'con_acceso_value', hidden: true},
   		 {name:'estado_value', hidden: true},
 		 ],
  	   postData: {
  	   		erptkn: tkn,
          tipo: 'tipo_subcontrato',
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
 	 	  sortname: 'id',
 	 	  sortorder: "DESC",
 			beforeProcessing: function(data, status, xhr){
 				if( $.isEmptyObject(data.session) === false){
 					window.location = phost() + "login?expired";
 				}
 	    },
 			loadBeforeSend: function () {//propiedadesGrid_cb
 	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
 	      $(this).closest("div.ui-jqgrid-view").find("#tablaTipoSubcontrato_cb, #jqgh_tablaTipoSubcontrato_link").css("text-align", "center");
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
  		//Boton de Opciones
    		gridObj.on("click", botones.opciones, function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        var id = $(this).attr("data-id");
        var rowINFO = gridObj.getRowData(id);
        var options = rowINFO.link;
        //Init Modal
        opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO.nombre +'');
        opcionesModal.find('.modal-body').empty().append(options);
        opcionesModal.find('.modal-footer').empty();
        opcionesModal.modal('show');
  		});

      //Boton de Liquidar Colaborador
  		opcionesModal.on("click", ".editarTipoSubcontrato", function(e){
  			e.preventDefault();
  			e.returnValue=false;
  			e.stopPropagation();

  			var id = $(this).attr("data-id");
        var rowINFO = gridObj.getRowData(id);
        var activo = rowINFO.estado_value==true?'1':'0';
        window.subcontratoConfiguracion.$broadcast('llenarFormulario', {id: id, valor: rowINFO.nombre, con_acceso: rowINFO.con_acceso_value.toString(), activo: activo, guardarBtn: 'Guardar'});
        opcionesModal.modal('hide');
      });
	};
  var recargar = function(){
		//Reload Grid
		gridObj.setGridParam({
			url: tablaUrl,
			datatype: "json",
			postData: {
				proveedor: '',
				monto_original: '',
				numero_subcontrato: '',
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
    },
		recargar: function(){
			//reload jqgrid
			recargar();
		},
  };

})();

$(function(){
   tablaCatalogoTipoSubcontrato.init();
});
