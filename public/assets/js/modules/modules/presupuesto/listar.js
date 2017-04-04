$(function(){

	//Init Bootstrap Calendar Plugin
    $('#fecha1, #fecha2').daterangepicker({
        format: 'DD-MM-YYYY',
        showDropdowns: true,
        defaultDate: '',
        singleDatePicker: true
    }).val('');

});

var vistaListar={
  boton:{
    exportar:$("#exportarListaPresupuesto")
  },
  init:function(){
    this.eventos();
  },
  eventos:function(){
    this.boton.exportar.click(function(){
      var ids = $("#presupuestoGrid").jqGrid ('getGridParam', 'selarrrow');
      if(_.isEmpty(ids)){
        ids = $("#presupuestoGrid").jqGrid('getDataIDs');
      }
      if(!_.isEmpty(ids)){
        $('#presupuestoExpor').val(ids);
        $('#formularioExportar').submit();
      }else{
        swal("No hay datos para Exportar");
        return false;
      }
    });
  }

};


$(document).ready(function(){
 vistaListar.init();
});
