var moduloAseguradora = (function() {
  return {
    listarRamosTree: function(parametros) {
      return $.post(phost() + 'aseguradoras/ajax-listar-ramos-tree', $.extend({
        erptkn: tkn
      }, parametros));
    },
    cambiarEstadoCuentaContable: function(parametros) {
      return $.post(phost() + 'aseguradoras/ajax-cambiar-estado-ramo', $.extend({
        erptkn: tkn
      }, parametros));
    },
    guardarRamos:function(element){
      var parametros = $(element).serialize();
      return $.post(phost() + 'aseguradoras/ajax_guardar_ramos', parametros);
    },
    getRamo:function(parametros){
      return $.post(phost() + 'aseguradoras/ajax-buscar-ramo', $.extend({
        erptkn: tkn
      }, parametros));
    }
  };
})();
