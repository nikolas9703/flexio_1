var moduloPresupuesto  = (function() {
  return {
    guardarPresupuesto:function(element){
      var parametros = $(element).serialize();
      //console.log(parametros);
      return $.post(phost() + 'presupuesto/ajax-guardar-presupuesto', parametros);
    },
    exportar: function(parametros) {
      return $.post(phost() + 'presupuesto/exportar', $.extend({
        erptkn: tkn
      }, parametros));
    }
  };
})();
