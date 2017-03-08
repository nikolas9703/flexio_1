var moduloAseguradora = (function() {
  return {
    listarRamosTree: function(parametros) {
      return $.post(phost() + 'configuracion_seguros/ajax-listar-ramos-tree', $.extend({
        erptkn: tkn
      }, parametros));
    },
    cambiarEstadoCuentaContable: function(parametros) {
      return $.post(phost() + 'configuracion_seguros/ajax-cambiar-estado-ramo', $.extend({
        erptkn: tkn
      }, parametros));
    },
    guardarRamos:function(element){
      var parametros = $(element).serialize();
      return $.post(phost() + 'configuracion_seguros/ajax_guardar_ramos', parametros);
    },
    
    getRamo:function(parametros){
      return $.post(phost() + 'configuracion_seguros/ajax-buscar-ramo', $.extend({
        erptkn: tkn
      }, parametros));
    },
  };
})();
