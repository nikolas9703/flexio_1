var moduloPolizas = (function(){
  return {
      cambiarEstadoPolizas : function (parametros){
        return $.post(phost() + "polizas/ajax_cambiar_estado_polizas", $.extend({
          erptkn: tkn
        },parametros));
      },
      AgendarCobro: function(parametros){
      	 return $.post(phost() + "polizas/ajax-get-cobro-agendado", $.extend({
          erptkn: tkn
        },parametros));
      }
    };
})();
