var tablaManual;
var entrada = {
  settings: {
    url: phost() + 'entrada_manual/ajax-listar',
    gridId : "#entradaManualGrid",
 	  gridObj : $("#entradaManualGrid"),
 	  opcionesModal : $('#opcionesModal')
   },
   botones:{
     opciones: "button.viewOptions",
     exportar: "#exportarEntradasList"
   }, 
   init:function(){
     tablaManual = this.settings;
     this.tablaGrid();
     this.redimencionar();
     this.eventos();
   }, 
   eventos:function(){
     tablaManual.gridObj.on("click", this.botones.opciones, function(e){

      e.preventDefault();
 			e.returnValue=false;
 			e.stopPropagation();
       var id = $(this).data("id");

   		var rowINFO = $.extend({},tablaManual.gridObj.getRowData(id));
   	  var options = rowINFO.link;

       tablaManual.opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO.codigo +'');
       tablaManual.opcionesModal.find('.modal-body').empty().append(options);
       tablaManual.opcionesModal.find('.modal-footer').empty();
       tablaManual.opcionesModal.modal('show');

     });
   },
   redimencionar:function(){
     $(window).resizeEnd(function() {
       $(".ui-jqgrid").each(function(){
         var w = parseInt( $(this).parent().width()) - 6;
         var tmpId = $(this).attr("id");
         var gId = tmpId.replace("gbox_","");
         $("#"+gId).setGridWidth(w);
       });
     });
   },
   tablaGrid:function(){
     tablaManual.gridObj.jqGrid({
     url: tablaManual.url,
     datatype: "json",
     colNames: ['','No. de Entrada','Narración','Fecha de Entrada','Débito','Crédito','',''],
     colModel: [
               {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
                {name:'codigo', index:'codigo',sorttype:"text", sortable:true, width:150},
                {name:'nombre',index:'nombre', sortable:true},
                {name:'created_at',index:'created_at', formatter: 'date', formatoptions: { newformat: 'd/m/Y' }, sortable:true},
                {name:'debito', index:'debito', formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$ "}, sortable:true},
                {name:'credito', index:'credito', formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$ "}, sortable:true},
                {name:'opciones', index:'opciones', sortable:false, align:'center'},
                {name:'link', index:'link', hidedlg:true, hidden: true}
              ],
     mtype: "POST",
     postData: { erptkn:tkn},
     sortorder: "asc",
     hiddengrid: false,
     loadtext: '<p>Cargando...</p>',
     hoverrows: false,
     viewrecords: true,
     refresh: true,
     gridview: true,
     multiselect: true,
     height: 'auto',
     page: 1,
     pager : tablaManual.gridId+"Pager",
     rowNum:10,
     autowidth: true,
     rowList:[10,20,30],
     sortname: 'codigo',
     beforeProcessing: function(data, status, xhr){
       //Check Session
     if( $.isEmptyObject(data.session) === false){
       window.location = phost() + "login?expired";
     }},
 loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	      //$(this).closest("div.ui-jqgrid-view").find("#tablaManualGrid_cb, #jqgh_tablaManualGrid_link").css("text-align", "center");
	    },
     beforeRequest: function(data, status, xhr){},
     loadComplete: function(data, status, xhr){

       //check if isset data
     
       //Boton de Exportar Entradas
		$('#exportarEntradasList').on("click", function(e){
                    
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			if($('#tabla').is(':visible') == true){
				
				//Exportar Seleccionados del jQgrid
				var ids = [];
                                
				ids = tablaManual.gridObj.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(ids.length > 0){
					
					$('#ids').val(ids);
                                        console.log(ids);
			        $('form#exportarEntradas').submit();
			        $('body').trigger('click');
				}
	        }
		}); 


       if(tablaManual.gridObj.getGridParam('records') === 0 ){
         $('#gbox_entradaManualGrid').hide();
         $('#entradaManualGridNoRecords').empty().append('No se encontraron entradas.').css({"color":"#868686","padding":"30px 0 0"}).show();
       }
       else{
         $('.entradaManualGrid').hide();
         $('#entradaManualGridNoRecords').show();
       }
       

       //---------
       // Cargar plugin jquery Sticky Objects
       //----------
       //add class to headers
       tablaManual.gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
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
   }
};
$(document).ready(function(){
    entrada.init();
});
