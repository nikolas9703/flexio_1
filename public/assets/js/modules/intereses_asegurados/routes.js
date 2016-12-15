var moduloIntereses = (function() {
  return {
    cambiarEstadoIntereses: function(parametros) {
      return $.post(phost() + 'intereses_asegurados/ajax_cambiar_estado_intereses', $.extend({
        erptkn: tkn
      }, parametros));
    }
    
  };
})();