$(document).ready(function(){
  $(function(){

    var grid = $("#OrganizacionGrid");
    grid.jqGrid({
    url: phost() + 'usuarios/ajax-listar-organizacion',
    datatype: "json",
    colNames: ['','Nombre','Fecha de Creación','Opciones',''],
    colModel: [
               {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
               {name:'nombre', index:'nombre',sorttype:"text",sortable:true,width:150},
               {name:'created_at',index:'created_at', formatter: 'date', formatoptions: { newformat: 'd-m-Y' }, sortable:false, align:"center"},
               {name:'opciones', index:'opciones', sortable:false, align:'center'},
               {name:'link', index:'link', hidedlg:true, hidden: true}
             ],
    mtype: "POST",
    postData: { erptkn:tkn},
    height: "auto",
    autowidth: true,
    rowList: [10, 20,50, 100],
    rowNum: 10,
    page: 1,
    pager: "#pager_organizacion",
    loadtext: '<p>Cargando...',
    hoverrows: false,
    viewrecords: true,
    refresh: true,
    gridview: true,
    sortname: 'nombre',
    sortorder: "ASC",
    beforeRequest: function(data, status, xhr){},
    loadComplete: function(data){

      //check if isset data
      if(data.total == 0 ){
        $('#gbox_OrganizacionGrid').hide();
        $('.NoRecordsEmpresa').empty().append('No se encontraron Organizaciones.').css({"color":"#868686","padding":"30px 0 0"}).show();
      }
      else{
        $('.NoRecords').hide();
        $('#gbox_OrganizacionGrid').show();
      }

      //---------
      // Cargar plugin jquery Sticky Objects
      //----------
      //add class to headers
      $("#OrganizacionGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
      $("#OrganizacionGrid").find('div.tree-wrap').children().removeClass('ui-icon');
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
//-------------------------
  // Redimensioanr Grid al cambiar tamaño de la ventanas.
  //-------------------------
  $(window).resizeEnd(function() {
    $(".ui-jqgrid").each(function(){
      var w = parseInt( $(this).parent().width()) - 6;
      var tmpId = $(this).attr("id");
      var gId = tmpId.replace("gbox_","");
      $("#"+gId).setGridWidth(w);
    });
  });

  $("#OrganizacionGrid").on("click", ".viewOptions", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();

    var id = $(this).attr("data-id");

    var rowINFO = $("#OrganizacionGrid").getRowData(id);
    var options = rowINFO["link"];
    console.log(id);
      //Init boton de opciones
    $('#opcionesModal').find('.modal-title').empty().html('Opciones: '+ rowINFO["nombre"] +'');
    $('#opcionesModal').find('.modal-body').empty().html(options);
    $('#opcionesModal').find('.modal-footer').empty();
    $('#opcionesModal').modal('show');
  });


});


});
