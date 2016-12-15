$(document).ready(function(){

    $('#searchBtn').bind('click');


    $(function(){

    var grid = $("#PolizasGrid");
    grid.jqGrid({
    url: phost() + 'polizas/ajax-listar',
    datatype: "json",
    colNames: ['','No. Póliza','Cliente','Ramo','Usuario','Estado','Inicio de Vigencia','Fin de Vigencia','Opciones',''],
    colModel: [
               {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
                {name:'numero', index:'numero',sorttype:"text",sortable:true,width:150},
                {name:'cliente', index:'cliente',sorttype:"text",sortable:true,width:150},
                {name:'ramo', index:'ramo',sorttype:"text",sortable:true,width:150},
                {name:'usuario', index:'usuario',sorttype:"text",sortable:true,width:150},
                {name:'estado', index:'estado',sorttype:"text",sortable:true,width:150},
                {name:'inicio_vigencia', index:'inicio_vigencia',sorttype:"text",sortable:true,width:150},
                {name:'fin_vigencia', index:'fin_vigencia',sorttype:"text",sortable:true,width:150},
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
    pager: "#pager_polizas",
    loadtext: '<p>Cargando...',
    hoverrows: false,
    viewrecords: true,
    multiselect: true,
    refresh: true,
    gridview: true,
    sortname: 'nombre',
    sortorder: "ASC",
    beforeRequest: function(data, status, xhr){},
    loadComplete: function(data){

      //check if isset data
      if(data.total == 0 ){
        $('#gbox_PolizasGrid').hide();
        $('.NoRecordsEmpresa').empty().append('No se encontraron Pólizas.').css({"color":"#868686","padding":"30px 0 0"}).show();
      }
      else{
        $('.NoRecords').hide();
        $('#gbox_PolizasGrid').show();
      }

      //---------
      // Cargar plugin jquery Sticky Objects
      //----------
      //add class to headers
      $("#PolizasGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
      $("#PolizasGrid").find('div.tree-wrap').children().removeClass('ui-icon');
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

  $("#PolizasGrid").on("click", ".viewOptions", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();

    var id = $(this).attr("data-id");
    var rowINFO = $("#PolizasGrid").getRowData(id);
    var options = rowINFO["link"];
      //Init boton de opciones
    $('#opcionesModal').find('.modal-title').empty().html('Opciones: '+ rowINFO["nombre"] +'');
    $('#opcionesModal').find('.modal-body').empty().html(options);
    $('#opcionesModal').find('.modal-footer').empty();
    $('#opcionesModal').modal('show');
  });
  

});



    $('#searchBtn').on("click",function(e) {

        var nombre 		= $('#nombre').val();
        var ruc 		= $('#ruc').val();
        var telefono 		= $('#telefono').val();
        var email 		= $('#email').val();
        if(nombre != "" || ruc != "" || telefono != "" || email != "")
        {
            //Reload Grid
            $("#PolizasGrid").setGridParam({
                url: phost() + 'polizas/ajax-listar',
                datatype: "json",
                postData: {
                    nombre: nombre,
                    ruc: ruc,
                    telefono: telefono,
                    email: email,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
        else{
            $("#PolizasGrid").setGridParam({
                url: phost() + 'polizas/ajax-listar',
                datatype: "json",
                postData: {
                    nombre: "",
                    ruc: "",
                    telefono: "",
                    email: "",
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    });


});



$('#clearBtn').on("click",function(e){
    e.preventDefault();
     
    $("#PolizasGrid").setGridParam({
        url: phost() + 'polizas/ajax-listar',
        datatype: "json",
        postData: {
            nombre: '',
            ruc: '',
            telefono: '',
            email: '',
            erptkn: tkn
        }
    }).trigger('reloadGrid');

    //Reset Fields
    $('#nombre, #ruc, #telefono, #email').val('');
});

