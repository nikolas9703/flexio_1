var jqGridPresupuestoPeriodo = function(datos){
  this.idJqgrid = $("#presupuestoDinamicoGrid");
  this.datos = datos;
};

jqGridPresupuestoPeriodo.prototype = function(){
  var popular = function(){
    var jgridModel = this.datos.colModel;
    var jgridName = this.datos.colName;
    var jgridDatos = this.datos.rows;
    this.idJqgrid.jqGrid("clearGridData");
    $.jgrid.gridUnload('presupuestoDinamicoGrid');

    $("#presupuestoDinamicoGrid").jqGrid({
      datatype: "local",
      colNames:jgridName,
      colModel:jgridModel,
      height: "auto",
      autowidth: true,
     shrinkToFit:false,
     forceFit:true,
      rowNum: 1000,
      pager: "#presupuestoDinamicoGridPager",
      loadtext: '<p>Cargando...</p>',
      hoverrows: false,
      viewrecords: true,
      refresh: true,
      page:1,
      //ajaxGridOptions: {cache: false},
       //loadonce:false,
       localReader: { repeatitems: false },
       gridview: true,
       multiselect: false,
       sortname: 'codigo',
       sortorder: "ASC",
       loadComplete : function () {

       },
       onSelectRow: function(id) {
         $(this).find('tr#' + id).removeClass('ui-state-highlight');
       }
    }).trigger('reloadGrid');

     _.forIn(jgridDatos, function(value, i) {
         $("#presupuestoDinamicoGrid").jqGrid('addRowData', (i + 1) , value);
     });

    $('#gridHeader').sticky({
      getWidthFrom: '.ui-jqgrid-view',
      className: 'jqgridHeader'
    });

    $(".moneda").inputmask('currency',{
      prefix: "",
      autoUnmask : true
    });
    $(".porcentaje").inputmask('percentage',{
      suffix: "",
      clearMaskOnLostFocus: false
    });
  };
  return {
    setJqgrid:popular
  };
}();
