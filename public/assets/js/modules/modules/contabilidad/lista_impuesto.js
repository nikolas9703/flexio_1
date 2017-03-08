var tablaImpuesto;
var viewImpuesto={
  settings:{
    formImpuesto: $('crearImpuestoForm'),
    jqgridObj: $('#tablaImpuestoGrid'),
    url: phost() + 'contabilidad/ajax-listar-impuestos',
  },
  init: function() {
    tablaImpuesto = this.settings;
    this.tablaGrid();
    this.redimencionar();
    this.eventos();
  },
  redimencionar:function(){
    $(window).resizeEnd(function() {
      $(".ui-jqgrid").each(function() {
        var w = parseInt($(this).parent().width()) - 6;
        var tmpId = $(this).attr("id");
        var gId = tmpId.replace("gbox_", "");
        $("#" + gId).setGridWidth(w);
      });
    });
  },
  eventos:function(){

  },
  tablaGrid: function(){
    tablaImpuesto.jqgridObj.jqGrid({
      url: tablaImpuesto.url,
      datatype: "json",
      colNames: ['', 'Nombre', 'Descripcion', 'Tasa de Impuesto (%)', 'Cuenta Contable','Estado','', ''],
      colModel: [{
        name: 'id',
        index: 'id',
        hidedlg: true,
        key: true,
        hidden: true
      }, {
        name: 'nombre',
        index: 'nombre',
        sorttype: "text",
        sortable: true,
        width: 150
      }, {
        name: 'descripcion',
        index: 'descripcion',
        formatter: 'text',
        sortable: false
      },{
        name: 'impuesto',
        index: 'impuesto',
        formatter: 'text',
        sortable: false
      },
      {
        name: 'nombre_cuenta',
        index: 'nombre_cuenta',
        formatter: 'text',
        sortable: false
      },{
        name: 'estado',
        index: 'estado',
        formatter: 'text',
        sortable: false,
        align: 'center'
      }, {
        name: 'opciones',
        index: 'opciones',
        sortable: false,
        align: 'center'
      }, {
        name: 'link',
        index: 'link',
        hidedlg: true,
        hidden: true
      }],
      height: "auto",
     autowidth: true,
     rowList: [10, 20,50, 100],
     rowNum: 10,
     page: 1,
     //pager: "#"+ grid_id +"Pager",
     loadtext: '<p>Cargando...</p>',
     hoverrows: false,
       viewrecords: true,
       refresh: true,
       gridview: true,
       multiselect: false,
       sortname: 'nombre',
       sortorder: "ASC",
       beforeProcessing: function(data, status, xhr){
         //Check Session
         console.log(data);
           console.log('row');

     },
       loadComplete : function () {
         console.log('row');

       }
    });

  },

};

(function() {
  viewImpuesto.init();
})();
