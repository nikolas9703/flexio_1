var tablaPresupuesto={};
var presupuesto={
  settings: {
    url: phost() + 'presupuesto/ajax-listar',
    gridId : "#presupuestoGrid",
    gridObj : $("#presupuestoGrid"),
    opcionesModal : $('#opcionesModal')
   },
   botones:{
     opciones: "button.viewOptions",
     limpiar: $("#clearBtn"),
     buscar: $("#searchBtn"),
     exportarLista: "a.exportarTablaPresupuesto"
   },
   init:function(){
     tablaPresupuesto = this.settings;
     this.tablaGrid();
     this.redimencionar();
     this.eventos();
   },
   eventos:function(){
     this.botones.limpiar.click(function(e) {
       $('#buscarPresupuestoForm').find('input[type="text"]').prop("value", "");
       $('#buscarPresupuestoForm').find('select').prop("value", "");
       presupuesto.recargar();
     });

     this.botones.buscar.click(function(e) {

       var centro = $('#centro').val();
       var referencia = $('#referencia').val();
       var fecha1 = $('#fecha1').val();
       var fecha2 = $('#fecha2').val();

       if (centro !== "" || referencia !== "" || fecha1 !== "" || fecha2 !== "") {
         //Reload Grid
         tablaPresupuesto.gridObj.setGridParam({
           url: tabla.url,
           datatype: "json",
           postData: {
             centro: centro,
             referencia: referencia,
             fecha1: fecha1,
             fecha2: fecha2,
             erptkn: tkn
           }
         }).trigger('reloadGrid');
       }
     });

     tablaPresupuesto.gridObj.on("click", this.botones.opciones, function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();
       var id = $(this).data("id");

      var rowINFO = $.extend({},tablaPresupuesto.gridObj.getRowData(id));
      var options = rowINFO.link;
       tablaPresupuesto.opcionesModal.find('.modal-title').empty().append('Opciones: '+ $(rowINFO.codigo).text());
       tablaPresupuesto.opcionesModal.find('.modal-body').empty().append(options);
       tablaPresupuesto.opcionesModal.find('.modal-footer').empty();
       tablaPresupuesto.opcionesModal.modal('show');
       console.log(tablaPresupuesto.opcionesModal);
     });

     tablaPresupuesto.opcionesModal.on('click',this.botones.exportarLista,function(e){
         e.preventDefault();
         e.stopPropagation();
         var uuid = $(this).data("id");
         $('#presupuesto_exportar').val(uuid);
         $('#formularioExportarLista').submit();
         tablaPresupuesto.opcionesModal.modal('hide');
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
     tablaPresupuesto.gridObj.jqGrid({
     url: tablaPresupuesto.url,
     datatype: "json",
     colNames: ['','No.de Presupuesto','Centro Contable','Referencia','Fecha inicio','Tipo de presupuesto','',''],
     colModel: [
               {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
                {name:'codigo', index:'codigo',sorttype:"text", sortable:true, width:150},
                {name:'centro_contable', index:'codigo',sorttype:"text", sortable:true, width:150},
                {name:'nombre',index:'nombre', sortable:true},
                {name:'fecha_inicio',index:'fecha_inicio', sortable:true},
                {name:'tipo', index:'tipo', sortable:true, align:'left'},
                {name:'opciones', index:'opciones', sortable:false, align:'center'},
                {name:'link', index:'link', hidedlg:true, hidden: true}
              ],
     mtype: "POST",
     postData: { erptkn:tkn},
     sortorder: "desc",
     hiddengrid: false,
     loadtext: '<p>Cargando...</p>',
     hoverrows: false,
     viewrecords: true,
     refresh: true,
     gridview: true,
     multiselect: true,
     height: 'auto',
     page: 1,
     pager : tablaPresupuesto.gridId+"Pager",
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
	      $(this).closest("div.ui-jqgrid-view").find("#tablaPresupuestoGrid_cb, #jqgh_tablaPresupuestoGrid_link").css("text-align", "center");
	    },
     beforeRequest: function(data, status, xhr){},
     loadComplete: function(data, status, xhr){

       if($("#presupuestoGrid").getGridParam('records') === 0 ){
         $('#gbox_presupuestoGrid').hide();
         $('#presupuestoGridNoRecords').empty().append('No se encontraron Presupuestos.').css({"color":"#868686","padding":"30px 0 0"}).show();
       }
       else{
         $('#gbox_presupuestoGrid').show();
         $('#presupuestoGridNoRecords').empty();
       }

       //---------
       // Cargar plugin jquery Sticky Objects
       //----------
       //add class to headers
       tablaPresupuesto.gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
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
    tablaPresupuesto.gridObj.setGridParam({
      url: tablaPresupuesto.url,
      datatype: "json",
      postData: {
        centro: '',
        referencia: '',
        fecha1:'',
        fecha2:'',
        erptkn: tkn
      }
    }).trigger('reloadGrid');

  }
};

$(document).ready(function(){
 presupuesto.init();
});
