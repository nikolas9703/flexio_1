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
     exportar: "#exportarEntradasList",
     limpiar: $("#clearBtn"),
     buscar: $("#searchBtn"),
   },
   init:function(){
     tablaManual = this.settings;
     this.tablaGrid();
     this.redimencionar();
     this.eventos();
   },
   eventos:function(){

     this.botones.limpiar.click(function(e) {
             $('#fecha_min').prop("value", "");
             $('#fecha_max').prop("value", "");
             $("#centro_contable").val(null).trigger("change");
             entrada.recargar();
      });
      this.botones.buscar.click(function(e) {

          var fecha1 = $('#fecha_min').val();
          var fecha2 = $('#fecha_max').val();
          var centros = $("#centro_contable").val();
          var myPostData = tablaManual.gridObj.jqGrid('getGridParam', 'postData');
          delete myPostData.campo.centro_contable;
          if (fecha1 !== "" || fecha2 !== "" || centros !=="") {
              //Reload Grid
              tablaManual.gridObj.setGridParam({
                  url: tablaManual.url,
                  datatype: "json",
                  postData: {
                      campo:{
                          centro_contable: centros,
                          fecha_min: fecha1,
                          fecha_max: fecha2,
                      },
                      erptkn: tkn
                  }
              }).trigger('reloadGrid');
          }
      });
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
     colNames: ['','N&uacute;mero de entrada manual','Fecha y hora','Usuario','&sum; Débito','&sum; Crédito','',''],
     colModel: [
               {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
                {name:'codigo', index:'codigo',sorttype:"text", sortable:true, width:150},
                {name:'created_at',index:'created_at', sortable:true},
                {name:'nombre',index:'nombre', sortable:true},
                {name:'debito', index:'debito'},
                {name:'credito', index:'credito',sortable:true},
                {name:'opciones', index:'opciones', sortable:false, align:'center'},
                {name:'link', index:'link', hidedlg:true, hidden: true}
              ],
     mtype: "POST",
     postData: { campo:{},erptkn:tkn},
     sortorder: "desc",
     hiddengrid: false,
     loadtext: '<p>Cargando...</p>',
     hoverrows: false,
     viewrecords: true,
     refresh: true,
     gridview: true,
     multiselect: false,
     height: 'auto',
     page: 1,
     pager : tablaManual.gridId+"Pager",
     rowNum:10,
     autowidth: true,
     rowList:[10,30,50],
     sortname: 'codigo',
     subGrid: true,
     subGridOptions: { "plusicon" : "ui-icon-triangle-1-e",
                      "minusicon" :"ui-icon-triangle-1-s",
                      "openicon" : "ui-icon-arrowreturn-1-e",
                      "reloadOnExpand" : false,
                      "selectOnExpand" : true },
     subGridRowExpanded: function(subgrid_id, row_id) {
       entrada.subgridJqgrid(subgrid_id, row_id);
     },
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
			if($('#tabla').is(':visible') === true){

				//Exportar Seleccionados del jQgrid
				var ids = [];

				//ids = tablaManual.gridObj.jqGrid('getGridParam','selarrrow');

			    $('form#exportarEntradas').submit();
			    $('body').trigger('click');

	        }
		});


       if(tablaManual.gridObj.getGridParam('records') === 0 ){
         $('#gbox_entradaManualGrid').hide();
         $('#entradaManualGridNoRecords').empty().append('No se encontraron entradas.').css({"color":"#868686","padding":"30px 0 0"}).show();
       }
       else{
         $('#gbox_entradaManualGrid').show();
         $('#entradaManualGridNoRecords').hide();
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
   },
   recargar: function() {

         //Reload Grid
         var myPostData = tablaManual.gridObj.jqGrid('getGridParam', 'postData');
         delete myPostData.campo.centro_contable;
         tablaManual.gridObj.setGridParam({
             url: tablaManual.url,
             datatype: "json",
             postData: {
                 campo:{
                     centro_contable:[],
                     fecha_min:'',
                     fecha_max:'',
                 },
                 erptkn: tkn
             }
         }).trigger('reloadGrid');

     },
   subgridJqgrid:function(subgrid_id, row_id){
       var transaccionGrid = new EntradaTransaccion(subgrid_id, row_id);
       var tablaSubgrid = transaccionGrid.subgrid;
   }
};
$(document).ready(function(){
    entrada.init();
});
